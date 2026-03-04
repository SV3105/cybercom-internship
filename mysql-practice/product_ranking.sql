create database products;
use products;

create table products (
	product_id int primary key auto_increment,
    category_id int not null,
    product_name varchar(100) not null,
    revenue decimal(10, 2) not null
);

INSERT INTO products (category_id, product_name, revenue) VALUES
-- Category 1
(1, 'Laptop', 100000),
(1, 'Mobile', 80000),
(1, 'Tablet', 80000),
(1, 'Headphones', 50000),
(1, 'Keyboard', 30000),

-- Category 2
(2, 'Shirt', 40000),
(2, 'Jeans', 40000),
(2, 'Jacket', 35000),
(2, 'Shoes', 30000),
(2, 'Cap', 20000),

-- Category 3
(3, 'Sofa', 90000),
(3, 'Table', 70000),
(3, 'Chair', 70000),
(3, 'Bed', 60000),
(3, 'Cupboard', 50000);

select * from products;

select 
	product_id,
    category_id,
    product_name,
    revenue,
    product_rank
from (
	select 
		product_id,
		category_id,
        product_name,
        revenue,
        dense_rank() OVER (
        PARTITION BY category_id 
        order by revenue desc) as product_rank
        from products
) ranked_products
where product_rank <=3
order by category_id, product_rank;
