<?php
// models/Product.php
// Product model - handles product data operations

class Product {
    private $pdo;
    private $productsData;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        // Load products data from file
        global $products, $categories;
        if (!isset($products)) {
            require_once __DIR__ . '/../data/productsdata.php';
        }
        $this->productsData = $products;
    }
    
    /**
     * Get all products
     */
    public function getAllProducts() {
        return $this->productsData;
    }
    
    /**
     * Get product by ID
     */
    public function getProductById($id) {
        foreach ($this->productsData as $product) {
            if ($product['id'] == $id) {
                return $product;
            }
        }
        return null;
    }
    
    /**
     * Get products by category
     */
    public function getProductsByCategory($category) {
        return array_filter($this->productsData, function($product) use ($category) {
            return $product['category'] === $category;
        });
    }
    
    /**
     * Get products by brand
     */
    public function getProductsByBrand($brand) {
        return array_filter($this->productsData, function($product) use ($brand) {
            return $product['brand'] === $brand;
        });
    }
    
    /**
     * Search products
     */
    /**
     * Search products (Robust)
     */
    public function searchProducts($query) {
        $query = strtolower(trim($query));
        $terms = explode(' ', $query); // Split query into words
        
        return array_filter($this->productsData, function($product) use ($query, $terms) {
            
            // 1. Exact phrase match in Title (High Priority)
            if (stripos($product['title'], $query) !== false) return true;
            
            // 2. Check each word in: Title, Brand, Category, Description, Features
            foreach ($terms as $term) {
                if (strlen($term) < 2) continue; // Skip single chars
                
                $found = false;
                
                // Title
                if (stripos($product['title'], $term) !== false) $found = true;
                
                // Brands (Array or String)
                if (!$found) {
                     if (isset($product['brands']) && is_array($product['brands'])) {
                         foreach ($product['brands'] as $b) {
                             if (stripos($b, $term) !== false) { $found = true; break; }
                         }
                     } elseif (isset($product['brand']) && stripos($product['brand'], $term) !== false) {
                         $found = true;
                     }
                }
                
                // Categories (Array or String)
                if (!$found) {
                     if (isset($product['categories']) && is_array($product['categories'])) {
                         foreach ($product['categories'] as $c) {
                             if (stripos($c, $term) !== false) { $found = true; break; }
                         }
                     } elseif (isset($product['category']) && stripos($product['category'], $term) !== false) {
                         $found = true;
                     }
                }
                
                // Description
                if (!$found && isset($product['description']) && stripos($product['description'], $term) !== false) {
                    $found = true;
                }
                
                // Features (Array)
                if (!$found && isset($product['features']) && is_array($product['features'])) {
                    foreach ($product['features'] as $f) {
                        if (stripos($f, $term) !== false) { $found = true; break; }
                    }
                }
                
                if (!$found) return false; // If any term is not found, fail (AND logic)
                // For "OR" logic, change to: if ($found) return true;
            }
            
            return true; // All terms found
        });
    }
}
?>
