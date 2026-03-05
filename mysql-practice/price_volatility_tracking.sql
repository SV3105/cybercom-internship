create database price_tracking;
use price_tracking;

create table products (
	id int primary key auto_increment,
    name varchar(100),
    category varchar(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

create table product_prices (
	id int primary key auto_increment,
    product_id int not null,
    price decimal(10, 2) not null,
    valid_from DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP 
                ON UPDATE CURRENT_TIMESTAMP,
    foreign key(product_id) references products(id)
);

ALTER TABLE products
ADD COLUMN updated_at DATETIME 
DEFAULT CURRENT_TIMESTAMP 
ON UPDATE CURRENT_TIMESTAMP;

INSERT INTO products (name, category, created_at) VALUES
('Laptop Pro 15', 'Electronics', '2025-09-01 10:00:00'),
('Smartphone X', 'Electronics', '2025-10-05 11:30:00'),
('Wireless Headphones', 'Accessories', '2025-08-20 09:45:00'),
('Smart Watch', 'Wearables', '2025-11-15 14:20:00');


INSERT INTO product_prices 
(product_id, price, valid_from, created_at)
VALUES
(1, 50000, '2025-01-01 09:00:00', '2025-01-01 09:00:00'),
(1, 52000, '2025-03-01 09:00:00', '2025-03-01 09:00:00'),
(1, 51000, '2025-06-01 09:00:00', '2025-06-01 09:00:00'),
(1, 55000, '2025-09-01 10:00:00', '2025-09-01 10:00:00');

INSERT INTO product_prices 
(product_id, price, valid_from, created_at)
VALUES
(2, 30000, '2025-01-10 10:00:00', '2025-01-10 10:00:00'),
(2, 35000, '2025-02-15 10:00:00', '2025-02-15 10:00:00'),
(2, 28000, '2025-05-01 10:00:00', '2025-05-01 10:00:00'),
(2, 40000, '2025-08-01 10:00:00', '2025-08-01 10:00:00');

INSERT INTO product_prices 
(product_id, price, valid_from, created_at)
VALUES
(3, 1000, '2025-01-01 08:00:00', '2025-01-01 08:00:00'),
(3, 1020, '2025-04-01 08:00:00', '2025-04-01 08:00:00'),
(3, 1015, '2025-07-01 08:00:00', '2025-07-01 08:00:00'),
(3, 1030, '2025-10-01 08:00:00', '2025-10-01 08:00:00');

INSERT INTO product_prices 
(product_id, price, valid_from, created_at)
VALUES
(4, 1500, '2025-11-15 14:20:00', '2025-11-15 14:20:00'),
(4, 1650, '2026-01-01 10:00:00', '2026-01-01 10:00:00'),
(4, 1550, '2026-03-01 10:00:00', '2026-03-01 10:00:00');

WITH price_changes AS (
    SELECT 
        p.id AS product_id,
        p.name,
        pp.price,
        pp.valid_from,
        
        LAG(pp.price) OVER (
            PARTITION BY p.id 
            ORDER BY pp.valid_from
        ) AS previous_price
    FROM products p
    JOIN product_prices pp 
        ON p.id = pp.product_id
),

percentage_changes AS (
    SELECT
        product_id,
        name,
        price,
        previous_price,
        valid_from,
        
        CASE 
            WHEN previous_price IS NULL THEN NULL
            ELSE ((price - previous_price) / previous_price) * 100
        END AS percentage_change
    FROM price_changes
)

SELECT
    product_id,
    name,
    COUNT(percentage_change) AS total_price_changes,
    ROUND(AVG(price), 2) AS average_price,
    ROUND(MAX(percentage_change), 2) AS max_increase_percent,
    ROUND(MIN(percentage_change), 2) AS max_decrease_percent,
    ROUND(MAX(percentage_change) - MIN(percentage_change), 2) 
        AS volatility_range_percent
FROM percentage_changes
GROUP BY product_id, name
ORDER BY volatility_range_percent DESC;
