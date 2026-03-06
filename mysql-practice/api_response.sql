CREATE DATABASE API_response;
use API_response;

CREATE TABLE customers (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL
);

CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    price NUMERIC(10,2) NOT NULL
);

CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    customer_id INT REFERENCES customers(id),
    order_date DATE NOT NULL
);

CREATE TABLE order_items (
    id SERIAL PRIMARY KEY,
    order_id INT REFERENCES orders(id),
    product_id INT REFERENCES products(id),
    quantity INT NOT NULL
);

INSERT INTO customers (name) VALUES
('Sneha'),
('Rahul'),
('Priya'),
('Amit');

INSERT INTO products (name, price) VALUES
('Laptop', 70000.00),
('Mobile', 30000.00),
('Headphones', 2000.00),
('Shoes', 4000.00),
('Watch', 15000.00);

INSERT INTO orders (customer_id, order_date) VALUES
(1, '2026-03-01'),
(1, '2026-03-05'),
(2, '2026-03-02'),
(3, '2026-03-06');

INSERT INTO order_items (order_id, product_id, quantity) VALUES
-- Sneha Order 1
(1, 1, 1),  -- Laptop
(1, 3, 2),  -- Headphones

-- Sneha Order 2
(2, 2, 1),  -- Mobile
(2, 5, 1),  -- Watch

-- Rahul Order
(3, 4, 1),  -- Shoes

-- Priya Order
(4, 3, 1),  -- Headphones
(4, 5, 2);  -- Watch

WITH order_items_json AS (
    SELECT
        o.id AS order_id,
        o.customer_id,
        o.order_date,
        json_agg(
            json_build_object(
                'product_id', p.id,
                'product_name', p.name,
                'price', p.price,
                'quantity', oi.quantity
            )
        ) AS items
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.id
    JOIN products p ON p.id = oi.product_id
    GROUP BY o.id, o.customer_id, o.order_date
)

SELECT json_agg(
    json_build_object(
        'customer_id', c.id,
        'customer_name', c.name,
        'orders',
            (
                SELECT json_agg(
                    json_build_object(
                        'order_id', oij.order_id,
                        'order_date', oij.order_date,
                        'items', oij.items
                    )
                )
                FROM order_items_json oij
                WHERE oij.customer_id = c.id
            )
    )
) AS result
FROM customers c
WHERE EXISTS (
    SELECT 1 FROM orders o WHERE o.customer_id = c.id
);
