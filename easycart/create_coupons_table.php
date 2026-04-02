<?php
require_once __DIR__ . '/config/config.php';

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS vendor_coupons (
        id SERIAL PRIMARY KEY,
        vendor_id INTEGER REFERENCES vendors(id) ON DELETE CASCADE,
        code VARCHAR(50) UNIQUE NOT NULL,
        discount_type VARCHAR(20) DEFAULT 'percent',
        discount_value DECIMAL(12, 2) NOT NULL,
        min_order_amount DECIMAL(12, 2) DEFAULT 0.00,
        valid_until TIMESTAMP,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    
    $pdo->exec($sql);
    echo "vendor_coupons table created successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
