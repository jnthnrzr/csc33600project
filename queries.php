<?php
// Use database authentication information for login
require "settings.php";

// Connect to the team database F16336team1
$db = new mysqli($host, $user, $pass, $team_db);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// DELETE entry for DA9543
$delete_entry = "DELETE FROM store_inventories WHERE title_id='DA9543';";
$db->query($delete_entry);

// FOREIGN KEY RELATIONSHIP SET UP FOR store_inventories
$foreignkey_1 = "alter table store_inventories add foreign key(title_id) references titles(title_id);";
$foreignkey_2 = "alter table store_inventories add foreign key (stor_id) references stores(stor_id);";
$db->query($foreignkey_1);
$db->query($foreignkey_2);

// Find title_id for most recent review
$select_title_id = "SELECT title_id FROM reviews WHERE rev_date IN (SELECT MAX(rev_date) FROM reviews);";
$get_title_id = $db->query($select_title_id);
$title_id_result = mysqli_fetch_row($get_title_id);

// Save title_id as a variable
$title_id = $title_id_result[0];
echo "Hello $title_id\n";
 
// Find rev_date for most recent review
$select_rev_date = "SELECT MAX(rev_date) FROM reviews;";
$get_rev_date = $db->query($select_rev_date);
$rev_date_result = mysqli_fetch_array($get_rev_date);

// Save rev_date as a variable
$rev_date = $rev_date_result[0];
echo "Hello $rev_date\n";

// Set default timezone
date_default_timezone_set('America/New_York');

// Save rev_datetime as a variable
$rev_datetime = date('Y-m-d H:i:s', strtotime($rev_date));
echo "Hello datetime is $rev_datetime";

// 2. resulting in customer sales at 3 or more bookstores:
$query_2a = "insert into customer_sales values('0736',$title_id,5,400,$rev_date,0);";
$query_2b = "insert into customer_sales values('5023','$title_id,5,200,$rev_date,0);";
$query_2c = "insert into customer_sales values('1389',$title_id,5,1000,$rev_date,0);";
$result_2a = $db->query($query_2a);
$result_2a = $db->query($query_2b);
$result_2a = $db->query($query_2c);

// 3. These sales result in lowering inventory in that book below re-order threshold:
$query_3 = "UPDATE store_inventories, customer_sales SET store_inventories.qty = (store_inventories.qty - customer_sales.qty) WHERE store_inventories.stor_id in ('1389','5023','0736') AND store_inventories.title_id='th1218' AND store_inventories.stor_id = customer_sales.store_id AND store_inventories.title_id = customer_sales.title_id;"

$query_3a = "insert into store_inventories values('0736',$title_id,100,200 );";
$query_3b = "insert into store_inventories values('5023',$title_id,300,400 );";
$query_3c = "insert into store_inventories values('1389',$title_id,1000,1500 );";
$result_3a = $db->query($query_3a);
$result_3b = $db->query($query_3b);
$result_3c = $db->query($query_3c);

// 4.Generate pending orders for this book from each affected bookstore:
$query_4a = "insert into pending_orders values('0736','th0236',$title_id,300,$rev_datetime,1);";
$query_4b = "insert into pending_orders values('5023','th0246',$title_id,400,$rev_datetime,1);";
$query_4c = "insert into pending_orders values('1389','th0256',$title_id,900,$rev_datetime,1);";
$result_4a = $db->query($query_4a);
$result_4b = $db->query($query_4b);
$result_4c = $db->query($query_4c);

// 5.generate sales:
$query_5a = "insert into sales values('0736','th0236', $rev_datetime);";
$query_5b = "insert into sales values('5023','th0246', $rev_datetime);";
$query_5c = "insert into sales values('1389','th0256', $rev_datetime);";
$result_5a = $db->query($query_5a);
$result_5b = $db->query($query_5b);
$result_5c = $db->query($query_5c);

// 6.and salesdetail records:
$query_6a = "insert into salesdetail values('0736','th0236',$title_id,300,0);";
$query_6b = "insert into salesdetail values('5023','th0246',$title_id,400,0);";
$query_6c = "insert into salesdetail values('1389','th0256',$title_id,900,0);";
$result_6a = $db->query($query_6a);
$result_6b = $db->query($query_6b);
$result_6c = $db->query($query_6c);

// 7.set pending orders to fulfilled:
$query_7a = "update pending_orders set fulfilled=0 where stor_id='0736' and title_id=$title_id;";
$query_7b = "update pending_orders set fulfilled=0 where stor_id='5023' and title_id=$title_id;";
$query_7c = "update pending_orders set fulfilled=0 where stor_id='1389' and title_id=$title_id;";
$result_7a = $db->query($query_7a);
$result_7b = $db->query($query_7b);
$result_7c = $db->query($query_7c);

// 8.update the bookstore inventories:
$query_8a = "update store_inventories set qty=300 where stor_id='0736' and title_id=$title_id;";
$query_8b = "update store_inventories set qty=400 where stor_id='5023' and title_id=$title_id;";
$query_8c = "update store_inventories set qty=900 where stor_id='1389' and title_id=$title_id;";
$result_8a = $db->query($query_8a);
$result_8b = $db->query($query_8b);
$result_8c = $db->query($query_8c);

// 9. delete entries from pending_orders
$query_9a = "DELETE FROM pending_orders WHERE stor_id='0736' and title_id=$title_id";
$query_9b = "DELETE FROM pending_orders WHERE stor_id='5023' and title_id=$title_id";
$query_9c = "DELETE FROM pending_orders WHERE stor_id='1389' and title_id=$title_id";
$result_9a = $db->query($query_9a);
$result_9b = $db->query($query_9b);
$result_9c = $db->query($query_9c);


$show_reviews = $db->query("SELECT * FROM reviews;");
$show_titles = $db->query("SELECT * FROM titles;");
$show_customer_sales = $db->query("SELECT * FROM customer_sales;");
$show_store_inventories = $db->query("SELECT * FROM store_inventories;");
$show_pending_orders = $db->query("SELECT * FROM pending_orders;");
$show_sales = $db->query("SELECT * FROM sales;");
$show_salesdetail = $db->query("SELECT * FROM salesdetail;");

$tables = array(
"reviews",
"titles",
"customer_sales",
"store_inventories",
"pending_orders",
"sales",
"salesdetail"
);
$count = $db->query("SELECT COUNT(*) FROM pending_orders");
include('team1.html');
?>
