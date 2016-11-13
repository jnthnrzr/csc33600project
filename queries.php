<?php
// Use database authentication information for login
require "settings.php";
// Connect to the team database F16336team1
$db = new mysqli($host, $user, $pass, $team_db);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
// VARIABLES
/*
$title_id = 'th1218';
$review_id = '0011';
$periodical_id = 'int000';
$rev_date = '20160911';
$rating = '5';
$content = 'Lagercrantz’s real achievement here is the subtle development of Lisbeth’s character; he allows us access to her complex, alienated world but is careful not to remove her mystery and unknowability. Lisbeth Salander remains, in Lagercrantz’s hands, the most enigmatic and fascinating anti-heroine in fiction');
*/
// DELETE entry for DA9543
$delete_entry = "DELETE FROM store_inventories WHERE title_id='DA9543';";
$db->query($delete_entry);
// FOREIGN KEY RELATIONSHIP SET UP FOR store_inventories
$foreignkey_1 = "alter table store_inventories add foreign key(title_id) references titles(title_id);";
$foreignkey_2 = "alter table store_inventories add foreign key (stor_id) references stores(stor_id);";
$db->query($foreignkey_1);
$db->query($foreignkey_2);
/*
steps followed:
I fixed store_inventories to add the book I choose because it was not in the store_inventories, but I did this for easiness after I searched for a review.
*/
$title_1 = "insert into store_inventories values('0736','th1218',500,200);";
$title_2 = "insert into store_inventories values('5023','th1218',500,400);";
$title_3 = "insert into store_inventories values('1389','th1218',2000,1500);";
$add_titles1 = $db->query($title_1);
$add_titles2 = $db->query($title_2);
$add_titles3 = $db->query($title_3);
// Then we proceed to do the direction prof. said in homework:
// 1.	  A review of an existing book is published, so we Insert reviews into data:
$reviewquery = "insert into reviews values('0011','th1218','int001',20160911,5,'Lagercrantz’s real achievement here is the subtle development of Lisbeth’s character; he allows us access to her complex, alienated world but is careful not to remove her mystery and unknowability. Lisbeth Salander remains, in Lagercrantz’s hands, the most enigmatic and fascinating anti-heroine in fiction');";
$result = $db->query($reviewquery);

$title_id = "SELECT title_id FROM reviews WHERE rev_date IN (SELECT MAX(rev_date) FROM reviews);";
$rev_date = "SELECT MAX(rev_date) FROM reviews;";
echo "$title_id\n$rev_date\n"
// 2. resulting in customer sales at 3 or more bookstores:
$query_2a = "insert into customer_sales values('0736','th1218',5,400,'11-09-16',0);";
$query_2b = "insert into customer_sales values('5023','th1218',5,200,'11-09-16',0);";
$query_2c = "insert into customer_sales values('1389','th1218',5,1000,'11-09-16',0);";
$result_2a = $db->query($query_2a);
$result_2a = $db->query($query_2b);
$result_2a = $db->query($query_2c);

// 3. These sales result in lowering inventory in that book below re-order threshold:
$query_3a = "insert into store_inventories values('0736','th1218',100,200 );";
$query_3b = "insert into store_inventories values('5023','th1218',300,400 );";
$query_3c = "insert into store_inventories values('1389','th1218',1000,1500 );";
$result_3a = $db->query($query_3a);
$result_3b = $db->query($query_3b);
$result_3c = $db->query($query_3c);

// 4.Generate pending orders for this book from each affected bookstore:
$query_4a = "insert into pending_orders values('0736','th0236','th1218',300,20161110110000,1);";
$query_4b = "insert into pending_orders values('5023','th0246','th1218',400,20161110110000,1);";
$query_4c = "insert into pending_orders values('1389','th0256','th1218',900,20161110110000,1);";
$result_4a = $db->query($query_4a);
$result_4b = $db->query($query_4b);
$result_4c = $db->query($query_4c);

// 5.generate sales:
$query_5a = "insert into sales values('0736','th0236', 20161111120000);";
$query_5b = "insert into sales values('5023','th0246', 20161111130000);";
$query_5c = "insert into sales values('1389','th0256', 20161111150000);";
$result_5a = $db->query($query_5a);
$result_5b = $db->query($query_5b);
$result_5c = $db->query($query_5c);

// 6.and salesdetail records:
$query_6a = "insert into salesdetail values('0736','th0236','th1218',300,0);";
$query_6b = "insert into salesdetail values('5023','th0246','th1218',400,0);";
$query_6c = "insert into salesdetail values('1389','th0256','th1218',900,0);";
$result_6a = $db->query($query_6a);
$result_6b = $db->query($query_6b);
$result_6c = $db->query($query_6c);

// 7.set pending orders to fulfilled:
$query_7a = "update pending_orders set fulfilled=0 where stor_id='0736' and title_id='th1218';";
$query_7b = "update pending_orders set fulfilled=0 where stor_id='5023' and title_id='th1218';";
$query_7c = "update pending_orders set fulfilled=0 where stor_id='1389' and title_id='th1218';";
$result_7a = $db->query($query_7a);
$result_7b = $db->query($query_7b);
$result_7c = $db->query($query_7c);

// 8.update the bookstore inventories:
$query_8a = "update store_inventories set qty=300 where stor_id='0736' and title_id='th1218';";
$query_8b = "update store_inventories set qty=400 where stor_id='5023' and title_id='th1218';";
$query_8c = "update store_inventories set qty=900 where stor_id='1389' and title_id='th1218';";
$result_8a = $db->query($query_8a);
$result_8b = $db->query($query_8b);
$result_8c = $db->query($query_8c);

// 9. delete entries from pending_orders
$query_9a = "DELETE FROM pending_orders WHERE stor_id='0736' and title_id='th1218'";
$query_9b = "DELETE FROM pending_orders WHERE stor_id='5023' and title_id='th1218'";
$query_9c = "DELETE FROM pending_orders WHERE stor_id='1389' and title_id='th1218'";
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
