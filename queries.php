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
$foreignkey_1 = "ALTER TABLE store_inventories ADD FOREIGN KEY(title_id) REFERENCES titles(title_id);";
$foreignkey_2 = "ALTER TABLE store_inventories ADD FOREIGN KEY(stor_id) REFERENCES stores(stor_id);";
$db->query($foreignkey_1);
$db->query($foreignkey_2);

// Find title_id for most recent review
$select_title_id = "SELECT title_id FROM reviews WHERE rev_date IN (SELECT MAX(rev_date) FROM reviews);";
$get_title_id = $db->query($select_title_id);
$title_id_result = mysqli_fetch_row($get_title_id);
/*
$insert_title = "INSERT INTO store_inventories VALUES
('0736','th1218',500,200),
('5023','th1218',500,400), 
('1389','th1218',2000,1500);";
$insert_title_result = $db->query($insert_title);
*/
// Save title_id as a variable
$title_id = $title_id_result[0];
 
// Find rev_date for most recent review
$select_rev_date = "SELECT MAX(rev_date) FROM reviews;";
$get_rev_date = $db->query($select_rev_date);
$rev_date_result = mysqli_fetch_row($get_rev_date);

// Save rev_date as a variable
$rev_date = $rev_date_result[0];

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

// Save customer sale quantity as variables
$get_base_sale_qty = 
"SELECT GREATEST(ABS(qty), minStock) FROM store_inventories WHERE stor_id IN ('$stor_id_1', '$stor_id_2', '$stor_id_3') AND title_id = '$title_id';";
$base_sale_qty_result = $db->query($get_base_sale_qty);
$base_sale_qty = array();
while($row =mysqli_fetch_assoc($base_sale_qty_result)) {
	$base_sale_qty[] = $row['GREATEST(ABS(qty), minStock)'];
}

$get_minStock = "SELECT minStock FROM store_inventories WHERE stor_id IN ('$stor_id_1', '$stor_id_2', '$stor_id_3') AND title_id = '$title_id'";
$minStock_result = $db->query($get_minStock);
$minStock = array();
while($row = mysqli_fetch_assoc($minStock_result)) {
	$minStock[] = $row['minStock'];
}

$customer_sale1_qty = $base_sale_qty[0]; // + $minStock[0];
$customer_sale2_qty = $base_sale_qty[1]; //  + $minStock[1];
$customer_sale3_qty = $base_sale_qty[2]; // + $minStock[2];

// QUERY 2. resulting in customer sales at 3 or more bookstores:
$query_2 = "INSERT INTO customer_sales VALUES
('$stor_id_1', '$title_id', 5, $customer_sale1_qty, '$rev_date', 0), 
('$stor_id_2', '$title_id', 5, $customer_sale2_qty, '$rev_date', 0),
('$stor_id_3', '$title_id', 5, $customer_sale3_qty, '$rev_date', 0);";
$result_2 = $db->query($query_2);

// QUERY 3. These sales result in lowering inventory in that book below re-order threshold:
$query_3 = "UPDATE store_inventories, customer_sales SET store_inventories.qty = (store_inventories.qty - customer_sales.qty) WHERE store_inventories.stor_id in ('$stor_id_1', '$stor_id_2', '$stor_id_3') AND store_inventories.title_id = '$title_id' AND store_inventories.stor_id = customer_sales.store_id AND store_inventories.title_id = customer_sales.title_id;";
$result_3 = $db->query($query_3);

// ********************************** SALE 1 **************************************************

// Find store inventory quantity for sale 1
$select_store1_qty = "SELECT qty FROM store_inventories WHERE store_inventories.stor_id = '$stor_id_1' AND store_inventories.title_id = '$title_id';";

// Find store inventory quantity for sale 2
$get_store1_qty = $db->query($select_store1_qty);
$store1_qty_result = mysqli_fetch_array($get_store1_qty);
$store1_qty = $store1_qty_result[0];

// Save pending_order_qty1
// $pending_order_qty1 = $customer_sale1_qty - $store1_qty;
$pending_order_qty1 = abs($store1_qty - $minStock[0]);
// echo "CUSTOMER ORDER QTY1 IS $customer_sale1_qty<br>";

// ********************************** SALE 2 **************************************************

// Find store inventory quantity for sale 2
$select_store2_qty = "SELECT qty FROM store_inventories WHERE store_inventories.stor_id = '$stor_id_2' AND store_inventories.title_id = '$title_id';";

// Find store inventory quantity for sale 3
$get_store2_qty = $db->query($select_store2_qty);
$store2_qty_result = mysqli_fetch_array($get_store2_qty);
$store2_qty = $store2_qty_result[0];

// Save pending_order_qty2
$pending_order_qty2 = abs($store2_qty - $minStock[1]);

// ********************************** SALE 3 **************************************************

// Find store inventory quantity for sale 3
$select_store3_qty = "SELECT qty FROM store_inventories WHERE store_inventories.stor_id = '$stor_id_3' AND store_inventories.title_id = '$title_id';";

$get_store3_qty = $db->query($select_store3_qty);
$store3_qty_result = mysqli_fetch_array($get_store3_qty);
$store3_qty = $store3_qty_result[0];
 
// Save pending_order_qty3
$pending_order_qty3 = abs($store3_qty - $minStock[2]);

// ========================================================================= //
// QUERY 4: Generate pending orders for this book from each affected bookstore:
$query_4 = "INSERT INTO pending_orders VALUES
('$stor_id_1', '$ord_num_1', '$title_id', $pending_order_qty1, '$rev_datetime', 1), 
('$stor_id_2', '$ord_num_2', '$title_id', $pending_order_qty2, '$rev_datetime', 1), 
('$stor_id_3', '$ord_num_3', '$title_id', $pending_order_qty3, '$rev_datetime', 1);";
$result_4 = $db->query($query_4);
// ========================================================================= //

// ============================================ //
// QUERY 5: Generate sales records:
$query_5 = "INSERT INTO sales VALUES
('$stor_id_1', '$ord_num_1', '$rev_datetime'), 
('$stor_id_2', '$ord_num_2', '$rev_datetime'), 
('$stor_id_3', '$ord_num_3', '$rev_datetime');";
$result_5 = $db->query($query_5);
// ============================================ //

// ====================================== //
// QUERY 6: Generate salesdetail records:
$query_6 = "INSERT INTO salesdetail VALUES 
('$stor_id_1', '$ord_num_1', '$title_id', 300, 0), 
('$stor_id_2', '$ord_num_2', '$title_id', 400, 0), 
('$stor_id_3', '$ord_num_3', '$title_id', 900, 0);";
$result_6 = $db->query($query_6);
// ====================================== //

// ====================================== //
// QUERY 7: Set pending orders to fulfilled:
$query_7 = "UPDATE pending_orders SET fulfilled = 0 WHERE stor_id in ('$stor_id_1', '$stor_id_2', '$stor_id_3') AND title_id = '$title_id';";
$result_7 = $db->query($query_7);
// ===================================== //

// ================================ //
// QUERY 8: UPDATE the bookstore inventories:
$query_8 = "UPDATE store_inventories set qty=300 WHERE stor_id IN ('$stor_id_1', '$stor_id_2', '$stor_id_3') AND title_id = '$title_id';";
$result_8 = $db->query($query_8);
// =============================== //

// ======================================== //
// QUERY 9: Delete entries from pending_orders
$query_9 = "DELETE FROM pending_orders WHERE stor_id IN ('$stor_id_1', '$stor_id_2', '$stor_id_3') AND title_id = '$title_id';";
$result_9 = $db->query($query_9);
// ======================================= //

// Delete the title in store_inventories
// $query_10 = "DELETE FROM store_inventories WHERE title_id = '$title_id' AND stor_id IN ('$stor_id_1', '$stor_id_2', '$stor_id_3');"; 
// $result_10 = $db->query($query_10);


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
