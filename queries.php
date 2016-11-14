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
echo "$delete_entry<br>";
$db->query($delete_entry);

// FOREIGN KEY RELATIONSHIP SET UP FOR store_inventories
$foreignkey_1 = "ALTER TABLE store_inventories ADD FOREIGN KEY(title_id) references titles(title_id);";
$foreignkey_2 = "ALTER TABLE store_inventories ADD FOREIGN KEY (stor_id) references stores(stor_id);";
echo "$foreignkey_1<br>";
echo "$foreignkey_2<br>";
$db->query($foreignkey_1);
$db->query($foreignkey_2);

// Find title_id for most recent review
$select_title_id = "SELECT title_id FROM reviews WHERE rev_date IN (SELECT MAX(rev_date) FROM reviews);";
echo "$select_title_id<br>";
$get_title_id = $db->query($select_title_id);
$title_id_result = mysqli_fetch_row($get_title_id);

$insert_title = "INSERT INTO store_inventories VALUES
('0736','th1218',500,200),
('5023','th1218',500,400), 
('1389','th1218',2000,1500);";

// Save title_id as a variable
$title_id = $title_id_result[0];
echo "TITLE ID VARIABLE IS $title_id<br>";
 
// Find rev_date for most recent review
$select_rev_date = "SELECT MAX(rev_date) FROM reviews;";
echo "$select_rev_date<br>";
$get_rev_date = $db->query($select_rev_date);
$rev_date_result = mysqli_fetch_row($get_rev_date);

// Save rev_date as a variable
$rev_date = $rev_date_result[0];
echo "rev_date variable is $rev_date<br>";

// Save rev_datetime as a variable
$rev_datetime = date('Y-m-d H:i:s', strtotime($rev_date));
echo "rev datetime variable is $rev_datetime<br>";

// Save stor_ids as variables
$stor_id_1 = '0736';
$stor_id_2 = '5023';
$stor_id_3 = '1389';
echo "stor id variables are $stor_id_1, $stor_id_2, $stor_id_3<br>";

// Save ord_num as variables
$ord_num_1 = 'th0236';
$ord_num_2 = 'th0246';
$ord_num_3 = 'th0256';
echo "ord num variables are $ord_num_1, $ord_num_2, $ord_num_3<br>";

// Save customer sale quantity as variables
$get_base_sale_qty = 
"SELECT GREATEST(ABS(qty), minStock) FROM store_inventories WHERE stor_id IN ('$stor_id_1', '$stor_id_2', '$stor_id_3') AND title_id = '$title_id'";
echo "$get_base_sale_qty<br>";
$base_sale_qty_result = $db->query($get_base_sale_qty);
$base_sale_qty = mysqli_fetch_row($base_sale_qty_result);

$customer_sale1_qty = $base_sale_qty[0] + 100;
$customer_sale2_qty = $base_sale_qty[1] + 100;
$customer_sale3_qty = $base_sale_qty[2] + 100;
echo "customer sale qty variables are $customer_sale1_qty, $customer_sale2_qty, $customer_sale3_qty<br>";

// QUERY 2. resulting in customer sales at 3 or more bookstores:
$query_2 = "INSERT INTO customer_sales VALUES
('$stor_id_1', '$title_id', 5, $customer_sale1_qty, '$rev_date', 0), 
('$stor_id_2', '$title_id', 5, $customer_sale2_qty, '$rev_date', 0),
('$stor_id_3', '$title_id', 5, $customer_sale3_qty, '$rev_date', 0);";
echo "$query_2<br>";
$result_2 = $db->query($query_2);

// QUERY 3. These sales result in lowering inventory in that book below re-order threshold:
$query_3 = "UPDATE store_inventories, customer_sales SET store_inventories.qty = (store_inventories.qty - customer_sales.qty) WHERE store_inventories.stor_id in ($stor_id_1, $stor_id_2, $stor_id_3) AND store_inventories.title_id = $title_id AND store_inventories.stor_id = customer_sales.store_id AND store_inventories.title_id = customer_sales.title_id;";
echo "$query_3<br>";
$result_3 = $db->query($query_3);

// ********************************** SALE 1 **************************************************

// Find store inventory quantity for sale 1
$select_store1_qty = "SELECT qty FROM store_inventories WHERE store_inventories.stor_id = '$stor_id_1' AND store_inventories.title_id = '$title_id';";
echo "$select_store1_qty<br>";

// Find store inventory quantity for sale 2
$get_store1_qty = $db->query($select_store1_qty);
$store1_qty_result = mysqli_fetch_array($get_store1_qty);
$store1_qty = $store1_qty_result[0];
echo "STORE INVENTORY QTY1 IS $store1_qty<br>";

// Save pending_order_qty1
$pending_order_qty1 = $customer_sale1_qty - $store1_qty;
echo "PENDING ORDER QTY1 IS $pending_order_qty1<br>";

// ********************************** SALE 2 **************************************************

// Find store inventory quantity for sale 2
$select_store2_qty = "SELECT qty FROM store_inventories WHERE store_inventories.stor_id = '$stor_id_2' AND store_inventories.title_id = '$title_id';";
echo "$select_store2_qty<br>";

// Find store inventory quantity for sale 3
$get_store2_qty = $db->query($select_store2_qty);
$store2_qty_result = mysqli_fetch_array($get_store2_qty);
$store2_qty = $store2_qty_result[0];
echo "STORE INVENTORY QTY2 IS $store2_qty<br>";

// Save pending_order_qty2
$pending_order_qty2 = $customer_sale2_qty - $store2_qty;
echo "PENDING ORDER QTY2 IS $pending_order_qty2<br>";

// ********************************** SALE 3 **************************************************

// Find store inventory quantity for sale 3
$select_store3_qty = "SELECT qty FROM store_inventories WHERE store_inventories.stor_id = '$stor_id_3' AND store_inventories.title_id = '$title_id';";
echo "$select_store3_qty<br>";

$get_store3_qty = $db->query($select_store3_qty);
$store3_qty_result = mysqli_fetch_array($get_store3_qty);
$store3_qty = $store3_qty_result[0];
echo "STORE INVENTORY QTY3 IS $store3_qty<br>";
 
// Save pending_order_qty3
$pending_order_qty3 = $customer_sale3_qty - $store3_qty;
echo "PENDING ORDER QTY3 IS $pending_order_qty3<br>";

/////////////////////////////
// PENDING ORDER VARIABLES //
// $pending_order_qty1 = 300; //
// $pending_order_qty2 = 400; //
// $pending_order_qty3 = 900; //
/////////////////////////////
// echo "pending order qty variables are $pending_order_qty1, $pending_order_qty2, $pending_order_qty3<br>";
// ========================================================================= //
// QUERY 4: Generate pending orders for this book from each affected bookstore:
$query_4 = "INSERT INTO pending_orders VALUES
($stor_id_1, $ord_num_1, $title_id, $pending_order_qty1, $rev_datetime, 1), 
($stor_id_2, $ord_num_2, $title_id, $pending_order_qty2, $rev_datetime, 1), 
($stor_id_3, $ord_num_3, $title_id, $pending_order_qty3, $rev_datetime, 1);";
$result_4 = $db->query($query_4);
// ========================================================================= //
echo "$query_4<br>";
// ==================================== //
// QUERY 5: Generate sales records:
$query_5 = "INSERT INTO sales VALUES
($stor_id_1, $ord_num_1, $rev_datetime), 
($stor_id_2, $ord_num_2, $rev_datetime), 
($stor_id_3, $ord_num_3, $rev_datetime);";
$result_5 = $db->query($query_5);
// ==================================== //
echo "$query_5<br>";
// ====================================== //
// QUERY 6: Generate salesdetail records:
$query_6 = "INSERT INTO salesdetail VALUES 
($stor_id_1, $ord_num_1,$title_id,300,0), 
($stor_id_2, $ord_num_2,$title_id,400,0), 
($stor_id_3, $ord_num_3,$title_id,900,0);";
$result_6 = $db->query($query_6);
// ====================================== //
echo "$query_6<br>";
// ====================================== //
// QUERY 7: Set pending orders to fulfilled:
$query_7 = "UPDATE pending_orders SET fulfilled = 0 WHERE stor_id in ($stor_id_1, $stor_id_2, $stor_id_3) AND title_id=$title_id;";
$result_7 = $db->query($query_7);
// ===================================== //
echo "$query_7<br>";
// ================================ //
// QUERY 8: UPDATE the bookstore inventories:
$query_8 = "UPDATE store_inventories set qty=300 WHERE stor_id IN ($stor_id_1, $stor_id_2, $stor_id_3) AND title_id = $title_id;";
$result_8 = $db->query($query_8);
// =============================== //
echo "$query_8<br>";
// ======================================== //
// QUERY 9: Delete entries from pending_orders
$query_9 = "DELETE FROM pending_orders WHERE stor_id IN ($stor_id_1, $stor_id_2, $stor_id_3) AND title_id=$title_id";
$result_9 = $db->query($query_9);
// ======================================= //
echo "$query_9<br>";
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
