create database multi_dimensional_sales_reporting;
use multi_dimensional_sales_reporting;

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category_id INT,
    price DECIMAL(10,2),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    status ENUM('completed', 'pending', 'cancelled') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO categories (name) VALUES 
('Electronics'), 
('Accessories'), 
('Wearables');

INSERT INTO products (name, category_id, price) VALUES
('Laptop', 1, 70000),
('Mouse', 2, 500),
('Keyboard', 2, 1500),
('Smart Watch', 3, 15000),
('Headphones', 2, 2000);

INSERT INTO orders (customer_id, status, created_at) VALUES
(1,'completed','2026-01-15'),
(2,'pending','2026-02-20'),
(3,'completed','2026-03-05'),
(4,'cancelled','2026-01-25'),
(5,'completed','2026-04-10'),
(6,'pending','2026-04-15');

INSERT INTO order_items (order_id, product_id, quantity) VALUES
(1,1,1),
(1,2,2),
(2,3,1),
(3,4,1),
(4,5,2),
(5,1,1),
(6,2,1);

select 
	c.name as category,
    concat('Q', QUARTER(MIN(o.created_at)), '-', YEAR(MIN(o.created_at))) as quarter,
    
    -- completed
    sum(CASE when o.status = 'completed' then 1 else 0 end) as completed_orders_count,
    sum(case when o.status = 'completed' then oi.quantity * p.price else 0 end) as completed_orders_amount,
    
	-- pending
    sum(CASE when o.status = 'pending' then 1 else 0 end) as pending_orders_count,
    sum(case when o.status = 'pending' then oi.quantity * p.price else 0 end) as pending_orders_amount,
    
	-- cancelled
    sum(CASE when o.status = 'cancelled' then 1 else 0 end) as cancelled_orders_count,
    sum(case when o.status = 'cancelled' then oi.quantity * p.price else 0 end) as cancelled_orders_amount
    
from orders o
join order_items oi on o.id = oi.order_id
join products p on oi.product_id = p.id
join categories c on p.category_id = c.id

GROUP BY c.name, QUARTER(o.created_at), YEAR(o.created_at)
ORDER BY c.name, quarter;
