<?php
// Use database authentication information for login
require "settings.php";

// Set default timezone, required for date function
date_default_timezone_set('America/New_York');

// Connect to the team database F16336team1
$db = new mysqli($host, $user, $pass, $team_db);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// DELETE entry for DA9543
$delete_entry = "DELETE FROM store_inventories WHERE title_id='DA9543';";
$db->query($delete_entry);

// FOREIGN KEY RELATIONSHIP SET UP FOR store_inventories
$foreignkey_1 = "ALTER TABLE store_inventories ADD FOREIGN KEY(title_id) references titles(title_id);";
$foreignkey_2 = "ALTER TABLE store_inventories ADD FOREIGN KEY (stor_id) references stores(stor_id);";
$db->query($foreignkey_1);
$db->query($foreignkey_2);

// Find title_id for most recent review
$select_title_id = "SELECT title_id FROM reviews WHERE rev_date IN (SELECT MAX(rev_date) FROM reviews);";
$get_title_id = $db->query($select_title_id);
$title_id_result = mysqli_fetch_row($get_title_id);

// Save title_id as a variable
$title_id = $title_id_result[0];
// echo "Hello title id result is $title_id";
 
// Find rev_date for most recent review
$select_rev_date = "SELECT MAX(rev_date) FROM reviews;";
$get_rev_date = $db->query($select_rev_date);
$rev_date_result = mysqli_fetch_row($get_rev_date);

// Save rev_date as a variable
$rev_date = $rev_date_result[0];
// echo "rev_date is $rev_date as a string";

// Save rev_datetime as a variable
$rev_datetime = date('Y-m-d H:i:s', strtotime($rev_date));

// Save stor_ids as variables
$stor_id_1 = '0736';
$stor_id_2 = '5023';
$stor_id_3 = '1389';

// Save ord_num as variables
$ord_num_1 = 'th0236';
$ord_num_2 = 'th0246';
$ord_num_3 = 'th0256';

// Save sale quantity as variables
$sale1_qty = 400;
$sale2_qty = 200;
$sale3_qty = 1000;

// QUERY 2. resulting in customer sales at 3 or more bookstores:
$query_2 = "INSERT INTO customer_sales VALUES
($stor_id_1, $title_id, 5, $sale1_qty, $rev_date, 0), 
($stor_id_2, $title_id, 5, $sale2_qty, $rev_date, 0),
($stor_id_3, $title_id, 5, $sale3_qty, $rev_date, 0);";
$result_2 = $db->query($query_2);

// QUERY 3. These sales result in lowering inventory in that book below re-order threshold:
$query_3 = "UPDATE store_inventories, customer_sales SET store_inventories.qty = (store_inventories.qty - customer_sales.qty) WHERE store_inventories.stor_id in ($stor_id_1, $stor_id_2, $stor_id_3) AND store_inventories.title_id = $title_id AND store_inventories.stor_id = customer_sales.store_id AND store_inventories.title_id = customer_sales.title_id;";
$result_3 = $db->query($query_3);

// ********************************** SALE 1 **************************************************

// Find customer sales 1 quantity 
// $select_sale1_qty = "SELECT qty FROM customer_sales WHERE customer_sales.store_id = $stor_id_1 AND customer_sales.title_id = $title_id;";
// $get_sale1_qty = $db->query($select_sale1_qty);
// $sale1_qty_result = mysqli_fetch_array($get_sale1_qty);
// $sale1_qty = $sale1_qty_result[0];

// Find store inventory quantity for sale 1

// $select_store = "SELECT qty FROM store_inventories WHERE store_inventories.stor_id = '$stor_id_1' AND store_inventories.title_id = '$title_id';";
// $select_store = "SELECT qty FROM store_inventories WHERE store_inventories.stor_id = '0736'  AND store_inventories.title_id = 'th1218';";
// echo "select_store IS $select_store<br>";



// $get_store = $db->query("SELECT qty FROM store_inventories WHERE store_inventories.stor_id = '0736' AND store_inventories.title_id = 'th1218';");
// $get_store = $db->query($select_store);
// echo "get_store IS $get_store<br>";

// $store1_qty_result = mysqli_fetch_array($get_store);
// echo "store1_qty_result IS $store1_qty_result<br>";

// $store1_qty = $store1_qty_result[0];
// echo "store1_qty IS $store1_qty<br>";
// Save pending_order_qty1
//$pending_order_qty1 = $sale1_qty - $store1_qty;


// 4.Generate pending orders for this book from each affected bookstore:
// $query_4a = "INSERT INTO pending_orders VALUES($stor_id_1,$ord_num_1,$title_id,300,$rev_datetime,1);";
// $query_4b = "INSERT INTO pending_orders VALUES($stor_id_1,$ord_num_2,$title_id,400,$rev_datetime,1);";
// $query_4c = "INSERT INTO pending_orders VALUES($stor_id_1,$ord_num_3,$title_id,900,$rev_datetime,1);";
// $result_4a = $db->query($query_4a);
// $result_4b = $db->query($query_4b);
// $result_4c = $db->query($query_4c);

// ********************************** SALE 2 **************************************************


// Find customer sales 2 quantity
// $select_sale2_qty = "SELECT qty FROM customer_sales WHERE customer_sales.store_id = $stor_id_2 AND customer_sales.title_id = $title_id;";
// $get_sale2_qty = $db->query($select_sale2_qty);
// $sale2_qty_result = mysqli_fetch_array($get_sale2_qty);
// $sale2_qty = $sale2_qty_result[0];

// Find store inventory quantity for sale 2
// $select_store2_qty = "SELECT qty FROM store_inventories WHERE store_inventories.stor_id = $stor_id_2 AND store_inventories.title_id = $title_id;";
// $get_store2_qty = $db->query($select_store2_qty);
// $store2_qty_result = mysqli_fetch_array($get_store2_qty);
// $store2_qty = $store2_qty_result[0];

// Save pending_order_qty2
// $pending_order_qty2 = $sale2_qty - $store2_qty;


// ********************************** SALE 3 **************************************************

// Find customer sales 3 quantity
// $select_sale3_qty = "SELECT qty FROM customer_sales WHERE customer_sales.store_id = $stor_id_3 AND customer_sales.title_id = $title_id;";
// $get_sale3_qty = $db->query($select_sale3_qty);
// $sale3_qty_result = mysqli_fetch_array($get_sale3_qty);
// $sale3_qty = $sale3_qty_result[0];

// Find store inventory quantity for sale 3
// $select_store3_qty = "SELECT qty FROM store_inventories WHERE store_inventories.stor_id = $stor_id_3 AND store_inventories.title_id = $title_id;";
// $get_store3_qty = $db->query($select_store3_qty);
// $store3_qty_result = mysqli_fetch_array($get_store3_qty);
// $store3_qty = $store3_qty_result[0];

// Save pending_order_qty3
// $pending_order_qty3 = $sale3_qty - $store3_qty;


// //////////////////////////
// PENDING ORDER VARIABLES //
$pending_order_qty1 = 300; //
$pending_order_qty2 = 400; //
$pending_order_qty3 = 900; //
// //////////////////////////

// ========================================================================= //
// QUERY 4: Generate pending orders for this book from each affected bookstore:
$query_4 = "INSERT INTO pending_orders VALUES
($stor_id_1, $ord_num_1, $title_id, $pending_order_qty1, $rev_datetime, 1), 
($stor_id_2, $ord_num_2, $title_id, $pending_order_qty2, $rev_datetime, 1), 
($stor_id_3, $ord_num_3, $title_id, $pending_order_qty3, $rev_datetime, 1);";
$result_4 = $db->query($query_4);
// ========================================================================= //

// ==================================== //
// QUERY 5: Generate sales records:
$query_5 = "INSERT INTO sales VALUES
($stor_id_1, $ord_num_1, $rev_datetime), 
($stor_id_2, $ord_num_2, $rev_datetime), 
($stor_id_3, $ord_num_3, $rev_datetime);";
$result_5 = $db->query($query_5);
// ==================================== //

// ====================================== //
// QUERY 6: Generate salesdetail records:
$query_6 = "INSERT INTO salesdetail VALUES 
($stor_id_1, $ord_num_1,$title_id,300,0), 
($stor_id_2, $ord_num_2,$title_id,400,0), 
($stor_id_3, $ord_num_3,$title_id,900,0);";
$result_6 = $db->query($query_6);
// ====================================== //

// ====================================== //
// QUERY 7: Set pending orders to fulfilled:
$query_7 = "UPDATE pending_orders SET fulfilled = 0 WHERE stor_id in ($stor_id_1, $stor_id_2, $stor_id_3) AND title_id=$title_id;";
$result_7 = $db->query($query_7);
// ===================================== //

// ================================ //
// QUERY 8: UPDATE the bookstore inventories:
$query_8 = "UPDATE store_inventories set qty=300 WHERE stor_id IN ($stor_id_1, $stor_id_2, $stor_id_3) AND title_id = $title_id;";
$result_8 = $db->query($query_8);
// =============================== //

// ======================================== //
// QUERY 9: Delete entries from pending_orders
$query_9 = "DELETE FROM pending_orders WHERE stor_id IN ($stor_id_1, $stor_id_2, $stor_id_3) AND title_id=$title_id";
$result_9 = $db->query($query_9);
// ======================================= //

$show_reviews = $db->query("SELECT * FROM reviews;");
$show_titles = $db->query("SELECT * FROM titles;");
$show_customer_sales = $db->query("SELECT * FROM customer_sales;");
$show_store_inventories = $db->query("SELECT * FROM store_inventories;");
$show_pending_orders = $db->query("SELECT * FROM pending_orders;");
$show_sales = $db->query("SELECT * FROM sales;");
$show_salesdetail = $db->query("SELECT * FROM salesdetail;");

$TABLEs = array(
"reviews",
"titles",
"customer_sales",
"store_inventories",
"pending_orders",
"sales",
"salesdetail"
);
include('team1.html');
?>
