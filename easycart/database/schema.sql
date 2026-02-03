-- EasyCart Database Schema (PostgreSQL)
-- Hybrid EAV Structure: Common fields in Entity, dynamic ones in Attribute.

-- ==========================================
-- 0. Dependencies (Users)
-- ==========================================

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 1. Catalog System (Hybrid EAV)
-- ==========================================

-- 1.1 Products
CREATE TABLE catalog_product_entity (
    entity_id SERIAL PRIMARY KEY,
    sku VARCHAR(255) UNIQUE NOT NULL, -- Derived from Title if not provided
    name VARCHAR(255) NOT NULL,
    price DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    -- Added columns based on productsdata.php analysis
    old_price DECIMAL(12, 2) DEFAULT NULL,
    image VARCHAR(255), -- Main image
    description TEXT,
    is_featured BOOLEAN DEFAULT FALSE,
    rating DECIMAL(3, 2) DEFAULT 0.00, -- Cached average
    review_count INTEGER DEFAULT 0, -- Cached count
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE catalog_product_attribute (
    attribute_id SERIAL PRIMARY KEY,
    product_id INTEGER REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE,
    attribute_code VARCHAR(255) NOT NULL, -- e.g., 'feature', 'color', 'size'
    value TEXT
);

CREATE TABLE catalog_product_image (
    image_id SERIAL PRIMARY KEY,
    product_id INTEGER REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE,
    image_path VARCHAR(255) NOT NULL,
    is_thumbnail BOOLEAN DEFAULT FALSE
);

-- 1.2 Categories
CREATE TABLE catalog_category_entity (
    entity_id SERIAL PRIMARY KEY,
    parent_id INTEGER DEFAULT 0, -- 0 for root
    name VARCHAR(255) NOT NULL, -- Added for easier joins
    image VARCHAR(255), -- Added based on data
    slug VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE catalog_category_attribute (
    attribute_id SERIAL PRIMARY KEY,
    category_id INTEGER REFERENCES catalog_category_entity(entity_id) ON DELETE CASCADE,
    attribute_code VARCHAR(255) NOT NULL,
    value TEXT
);

CREATE TABLE catalog_category_products (
    id SERIAL PRIMARY KEY,
    category_id INTEGER REFERENCES catalog_category_entity(entity_id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE
);

-- 1.3 Brands
CREATE TABLE catalog_brand_entity (
    entity_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE catalog_brand_attribute (
    attribute_id SERIAL PRIMARY KEY,
    brand_id INTEGER REFERENCES catalog_brand_entity(entity_id) ON DELETE CASCADE,
    attribute_code VARCHAR(255) NOT NULL,
    value TEXT
);

CREATE TABLE catalog_brand_products (
    id SERIAL PRIMARY KEY,
    brand_id INTEGER REFERENCES catalog_brand_entity(entity_id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE
);

-- ==========================================
-- 2. Sales Cart System
-- ==========================================

CREATE TABLE sales_cart (
    id SERIAL PRIMARY KEY,
    session_id VARCHAR(255) UNIQUE,
    user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    is_active BOOLEAN DEFAULT TRUE,
    grand_total DECIMAL(12, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sales_cart_products (
    id SERIAL PRIMARY KEY,
    cart_id INTEGER REFERENCES sales_cart(id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE,
    quantity INTEGER DEFAULT 1,
    price DECIMAL(12, 2) NOT NULL -- Cached price
);

CREATE TABLE sales_cart_address (
    id SERIAL PRIMARY KEY,
    cart_id INTEGER REFERENCES sales_cart(id) ON DELETE CASCADE,
    address_type VARCHAR(50) NOT NULL, -- 'billing' or 'shipping'
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    email VARCHAR(255),
    street TEXT,
    city VARCHAR(255),
    postcode VARCHAR(20)
);

CREATE TABLE sales_cart_shipping (
    id SERIAL PRIMARY KEY,
    cart_id INTEGER REFERENCES sales_cart(id) ON DELETE CASCADE,
    method_code VARCHAR(255),
    carrier_code VARCHAR(255),
    price DECIMAL(12, 2) DEFAULT 0.00
);

CREATE TABLE sales_cart_payment (
    id SERIAL PRIMARY KEY,
    cart_id INTEGER REFERENCES sales_cart(id) ON DELETE CASCADE,
    method_code VARCHAR(255),
    po_number VARCHAR(255)
);

-- ==========================================
-- 3. Sales Order System
-- ==========================================

CREATE TABLE sales_order (
    order_id SERIAL PRIMARY KEY,
    increment_id VARCHAR(50) UNIQUE,
    user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    status VARCHAR(50) DEFAULT 'pending',
    subtotal DECIMAL(12, 2) DEFAULT 0.00,
    shipping_amount DECIMAL(12, 2) DEFAULT 0.00,
    tax_amount DECIMAL(12, 2) DEFAULT 0.00,
    grand_total DECIMAL(12, 2) DEFAULT 0.00,
    customer_email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sales_order_products (
    id SERIAL PRIMARY KEY,
    order_id INTEGER REFERENCES sales_order(order_id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES catalog_product_entity(entity_id) ON DELETE SET NULL,
    sku VARCHAR(255),
    name VARCHAR(255), -- Frozen Name
    price DECIMAL(12, 2) NOT NULL, -- Frozen Price
    quantity INTEGER DEFAULT 1,
    total_price DECIMAL(12, 2) NOT NULL
);

CREATE TABLE sales_order_address (
    id SERIAL PRIMARY KEY,
    order_id INTEGER REFERENCES sales_order(order_id) ON DELETE CASCADE,
    address_type VARCHAR(50),
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    street TEXT,
    city VARCHAR(255),
    postcode VARCHAR(20),
    telephone VARCHAR(50)
);

CREATE TABLE sales_order_payment (
    id SERIAL PRIMARY KEY,
    order_id INTEGER REFERENCES sales_order(order_id) ON DELETE CASCADE,
    method VARCHAR(255)
);

-- ==========================================
-- 4. Interactions
-- ==========================================

CREATE TABLE wishlist (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, product_id)
);
