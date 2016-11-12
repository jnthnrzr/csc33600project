-- // DELETE entry for DA9543
DELETE FROM store_inventories WHERE title_id='DA9543';
-- // FOREIGN KEY RELATIONSHIP SET UP FOR store_inventories
alter table store_inventories add foreign key(title_id) references titles(title_id);
alter table store_inventories add foreign key (stor_id) references stores(stor_id);
insert into store_inventories values('0736','th1218',500,200);
insert into store_inventories values('5023','th1218',500,400);
insert into store_inventories values('1389','th1218',2000,1500);
insert into reviews values('0011','th1218','int001',20160911,5,'Lagercrantz’s real achievement here is the subtle development of Lisbeth’s character; he allows us access to her complex, alienated world but is careful not to remove her mystery and unknowability. Lisbeth Salander remains, in Lagercrantz’s hands, the most enigmatic and fascinating anti-heroine in fiction');
-- // 2. resulting in customer sales at 3 or more bookstores:
insert into customer_sales values('0736','th1218',5,400,'11-09-16',0);
insert into customer_sales values('5023','th1218',5,200,'11-09-16',0);
insert into customer_sales values('1389','th1218',5,1000,'11-09-16',0);
-- // 3. These sales result in lowering inventory in that book below re-order threshold:
insert into store_inventories values('0736','th1218',100,200 );
insert into store_inventories values('5023','th1218',300,400 );
insert into store_inventories values('1389','th1218',1000,1500 );
-- // 4.Generate pending orders for this book from each affected bookstore:
insert into pending_orders values('0736','th0236','th1218',300,20161110110000,1);
insert into pending_orders values('5023','th0246','th1218',400,20161110110000,1);
SELECT DISTINCT stor_id, ord_num, title_id, qty, date, fulfilled FROM pending_orders;
insert into pending_orders values('1389','th0256','th1218',900,20161110110000,1);
-- // 5.generate sales:
insert into sales values('0736','th0236', 20161111120000);
insert into sales values('5023','th0246', 20161111130000);
insert into sales values('1389','th0256', 20161111150000);
-- // 6.and salesdetail records:
insert into salesdetail values('0736','th0236','th1218',300,0);
insert into salesdetail values('5023','th0246','th1218',400,0);
insert into salesdetail values('1389','th0256','th1218',900,0);
-- // 7.set pending orders to fulfilled:
update pending_orders set fulfilled=9999300 where stor_id='0736' and title_id='th1218';
update pending_orders set fulfilled=400 where stor_id='5023' and title_id='th1218';
update pending_orders set fulfilled=900 where stor_id='1389' and title_id='th1218';
-- // 8.update the bookstore inventories:
update store_inventories set qty=300 where stor_id='0736' and title_id='th1218';
update store_inventories set qty=400 where stor_id='5023' and title_id='th1218';
update store_inventories set qty=900 where stor_id='1389' and title_id='th1218';
