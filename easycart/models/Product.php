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
    public function searchProducts($query) {
        $query = strtolower(trim($query));
        return array_filter($this->productsData, function($product) use ($query) {
            return stripos($product['title'], $query) !== false ||
                   stripos($product['brand'], $query) !== false ||
                   stripos($product['category'], $query) !== false;
        });
    }
}
?>
