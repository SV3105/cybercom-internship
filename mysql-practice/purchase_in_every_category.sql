create database ecommerce_db;
use ecommerce_db;

CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category_id INT,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    order_date DATE,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO customers (name) VALUES
('Sneha'),
('Rahul'),
('Priya');

INSERT INTO categories (name) VALUES
('Electronics'),
('Clothing'),
('Accessories');

INSERT INTO products (name, category_id) VALUES
('Laptop', 1),
('Mobile', 1),
('T-Shirt', 2),
('Jeans', 2),
('Watch', 3),
('Bag', 3);

INSERT INTO orders (customer_id, order_date) VALUES
(1, '2026-03-01'),
(1, '2026-03-02'),
(2, '2026-03-01'),
(3, '2026-03-03');

INSERT INTO orders (customer_id, order_date) VALUES
(2, '2026-03-02');

INSERT INTO order_items (order_id, product_id, quantity) VALUES
(1, 1, 1),  
(1, 3, 2), 
(2, 5, 1); 

INSERT INTO order_items (order_id, product_id, quantity) VALUES
(5, 3, 1),
(5, 1, 1);

INSERT INTO order_items (order_id, product_id, quantity) VALUES
(5,5,1);

select 
	c.id as customer_id,
	c.name as customer_name
from customers c
where not exists (
	select 1 
    from categories cat
where not exists (
	select 1 
    from orders o
    join order_items oi on oi.order_id = o.id
    join products p on p.id = oi.product_id
    where c.id = o.customer_id and p.category_id = cat.id
)
);
    