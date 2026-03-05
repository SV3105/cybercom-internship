create database customer_analysis;
use customer_analysis;

create table customers (
	id int primary key auto_increment,
    name varchar(100) not null,
    email varchar(150) unique not null,
    created_at datetime default current_timestamp
);

create table orders (
	id int primary key auto_increment,
    customer_id int not null,
    order_date datetime default current_timestamp,
    foreign key (customer_id) references customers(id)
);

create table order_items (
	id int primary key auto_increment,
    order_id int not null,
    product_name varchar(200) not null,
    quantity int,
    price decimal(10, 2) not null,
    foreign key (order_id) references orders(id)
);

INSERT INTO customers (name, email, created_at) VALUES
('Sneha', 'sneha@gmail.com', '2025-12-01 10:00:00'),
('Rahul', 'rahul@gmail.com', '2025-12-15 11:30:00'),
('Priya', 'priya@gmail.com', '2026-01-05 09:45:00'),
('Amit', 'amit@gmail.com', '2025-11-20 14:20:00'),
('Neha', 'neha@gmail.com', '2026-01-10 16:10:00');

INSERT INTO orders (customer_id, order_date) VALUES
-- Sneha (created: 2025-12-01)
(1, '2026-02-10 12:30:00'),
(1, '2026-02-25 18:15:00'),
(1, '2026-01-05 09:00:00'), 

-- Rahul (created: 2025-12-15)
(2, '2026-02-15 14:20:00'),
(2, '2026-02-28 19:45:00'),

-- Priya (created: 2026-01-05)
(3, '2026-02-20 11:10:00'),

-- Amit (created: 2025-11-20)
(4, '2026-01-10 16:00:00'),

-- Neha (created: 2026-01-10)
(5, '2026-02-18 13:30:00'),
(5, '2026-03-03 20:00:00');

INSERT INTO order_items (order_id, product_name, quantity, price) VALUES

-- Sneha
(1, 'Laptop', 1, 70000),
(1, 'Mouse', 2, 500),
(2, 'Mobile', 1, 30000),
(3, 'Tablet', 1, 20000),

-- Rahul
(4, 'Shoes', 2, 2000),
(5, 'Jacket', 1, 4000),

-- Priya
(6, 'Headphones', 3, 1500),

-- Amit 
(7, 'Watch', 1, 5000),

-- Neha
(8, 'Bag', 2, 2500),
(9, 'Perfume', 1, 3500);

select * from customers;
select * from orders;
select * from order_items;

INSERT INTO order_items (order_id, product_name, quantity, price)
VALUES (4, 'Gaming Console', 1, 35000);

WITH customer_spending AS (
    SELECT 
        c.id AS customer_id,
        c.name,
        COUNT(DISTINCT o.id) AS purchase_count,
        SUM(oi.quantity * oi.price) AS total_spending
    FROM customers c
    JOIN orders o ON c.id = o.customer_id
    JOIN order_items oi ON o.id = oi.order_id
    WHERE o.order_date >= NOW() - INTERVAL 30 DAY
    GROUP BY c.id, c.name
)

SELECT 
    customer_id,
    name,
    purchase_count,
    total_spending,
    total_spending - (
        SELECT AVG(total_spending) 
        FROM customer_spending
    ) AS amount_above_average
FROM customer_spending
WHERE total_spending > (
    SELECT AVG(total_spending) 
    FROM customer_spending
);

