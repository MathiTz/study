drop table cccat10.item;
drop table cccat10.order;
drop table cccat10.product;
drop table cccat10.coupon;
drop schema cccat10;

create schema cccat10;

create table cccat10.product(
  id_product integer primary key,
  description text,
  price numeric,
  width integer,
  height integer,
  length integer,
  weight numeric,
  currency text
);

insert into cccat10.product (id_product, description, price, width, height, length, weight, currency) values (1, 'A', 1000, 100, 30, 10, 3, 'BRL');
insert into cccat10.product (id_product, description, price, width, height, length, weight, currency) values (2, 'B', 5000, 50, 50, 50, 22, 'BRL');
insert into cccat10.product (id_product, description, price, width, height, length, weight, currency) values (3, 'C', 30, 10, 10, 10, 0.9, 'BRL');
insert into cccat10.product (id_product, description, price, width, height, length, weight, currency) values (4, 'D', 30, -10, 10, 10, 0.9, 'BRL');
insert into cccat10.product (id_product, description, price, width, height, length, weight, currency) values (5, 'A', 1000, 100, 30, 10, 3, 'USD');

create table cccat10.coupon(
  code text primary key,
  percentage numeric,
  expire_date timestamp
);

insert into cccat10.coupon (code, percentage, expire_date) values ('VALE10', 10, '2025-12-31T10:00:00');
insert into cccat10.coupon (code, percentage, expire_date) values ('VALE20', 20, '2025-12-31T10:00:00');
insert into cccat10.coupon (code, percentage, expire_date) values ('VALE50', 50, '2020-12-31T10:00:00');

create table cccat10.order (
  id_order text,
  code text,
  cpf text,
  total numeric,
  freight numeric
);

create table cccat10.item (
  id_order text,
  id_product integer,
  quantity integer,
  price numeric
);
