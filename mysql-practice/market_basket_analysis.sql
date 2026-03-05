create database market_basket_analysis;
use market_basket_analysis;

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT not null,
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

INSERT INTO products (name, category) VALUES
('Laptop', 'Electronics'),
('Mouse', 'Electronics'),
('Keyboard', 'Electronics'),
('Monitor', 'Electronics'),
('USB Cable', 'Accessories'),
('Headphones', 'Electronics');

INSERT INTO orders (customer_id) VALUES
(1),(2),(3),(4),(5),
(6),(7),(8),(9),(10),
(11),(12),(13),(14),(15),
(16),(17),(18),(19),(20);

-- Frequently bought pair: Laptop (1) + Mouse (2) → 11 times
INSERT INTO order_items (order_id, product_id) VALUES
(1,1),(1,2),
(2,1),(2,2),
(3,1),(3,2),
(4,1),(4,2),
(5,1),(5,2),
(6,1),(6,2),
(7,1),(7,2),
(8,1),(8,2),
(9,1),(9,2),
(10,1),(10,2),
(11,1),(11,2);

-- Frequently bought pair: Mouse (2) + Keyboard (3) → 9 times
INSERT INTO order_items (order_id, product_id) VALUES
(12,2),(12,3),
(13,2),(13,3),
(14,2),(14,3),
(15,2),(15,3),
(16,2),(16,3),
(17,2),(17,3),
(18,2),(18,3),
(19,2),(19,3),
(20,2),(20,3);

-- some random orders 
INSERT INTO order_items (order_id, product_id) VALUES
(1,5),
(3,6),
(7,4),
(10,6),
(15,4);

WITH product_pairs AS (
    SELECT 
        oi1.product_id AS product_1,
        oi2.product_id AS product_2,
        oi1.order_id
    FROM order_items oi1
    JOIN order_items oi2 
        ON oi1.order_id = oi2.order_id
        AND oi1.product_id < oi2.product_id
),

pair_counts AS (
    SELECT 
        product_1,
        product_2,
        COUNT(DISTINCT order_id) AS times_bought_together
    FROM product_pairs
    GROUP BY product_1, product_2
),

total_orders AS (
    SELECT COUNT(*) AS total_order_count FROM orders
)

SELECT 
    pc.product_1,
    pc.product_2,
    pc.times_bought_together,
    ROUND(
        (pc.times_bought_together / t.total_order_count) * 100,
        2
    ) AS percentage_of_orders
FROM pair_counts pc
CROSS JOIN total_orders t
WHERE pc.times_bought_together > 10
ORDER BY pc.times_bought_together DESC;
