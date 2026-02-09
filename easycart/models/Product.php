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

    /**
     * Export all products to CSV format
     * Returns array with 'success', 'data', and 'error' keys
     * @param string|null $filter - Optional filter: 'low_stock', 'featured'
     */
    public function exportToCSV($filter = null) {
        try {
            $sql = "
                SELECT 
                    p.sku,
                    p.name,
                    p.price,
                    p.old_price,
                    c.slug as category,
                    b.slug as brand,
                    p.description,
                    p.image,
                    p.is_featured,
                    p.rating,
                    p.review_count,
                    p.stock_qty
                FROM catalog_product_entity p
                LEFT JOIN catalog_category_entity c ON p.category_id = c.entity_id
                LEFT JOIN catalog_brand_entity b ON p.brand_id = b.entity_id
            ";
            
            // Apply filters
            $where = [];
            if ($filter === 'low_stock') {
                $where[] = "p.stock_qty < 10";
            } elseif ($filter === 'featured') {
                $where[] = "p.is_featured = 't'";
            }
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            
            $sql .= " ORDER BY p.entity_id ASC";
            
            $stmt = $this->pdo->query($sql);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'data' => $products,
                'count' => count($products)
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Import products from CSV data
     * @param array $csvData - Array of product data from CSV
     * @return array - Import results with success/failure counts
     */
    public function importFromCSV($csvData) {
        $results = [
            'success' => 0,
            'updated' => 0,
            'failed' => 0,
            'errors' => [],
            'failed_rows' => [] // Store full row data + error
        ];
        
        foreach ($csvData as $index => $row) {
            $rowNum = $index + 2; // +2 because index starts at 0 and row 1 is header
            
            // 1. Auto-generate SKU if missing (from Name)
            if (empty($row['sku']) && !empty($row['name'])) {
                $row['sku'] = $this->generateSku($row['name']);
            }
            
            // 2. Validate required fields
            $validation = $this->validateProductData($row);
            if (!$validation['valid']) {
                $results['failed']++;
                $results['errors'][] = "Row $rowNum: " . $validation['error'];
                $failedRow = $row;
                $failedRow['import_error_reason'] = $validation['error'];
                $results['failed_rows'][] = $failedRow;
                continue;
            }

            // ---------------------------------------------------------
            // IMAGE FALLBACK LOGIC
            // ---------------------------------------------------------
            $imagePath = __DIR__ . '/../public/images/';
            $imageName = $row['image'] ?? '';
            $finalImage = 'placeholder.svg'; // Default fallback (Step 3)

            // Step 1: Check if provided image exists
            if (!empty($imageName) && file_exists($imagePath . $imageName)) {
                $finalImage = $imageName;
            } else {
                // Step 2: Fallback to Category Image
                $categorySlug = $this->cleanString($row['category'] ?? '');
                if (!empty($categorySlug)) {
                    $catImage = $this->getCategoryImageBySlug($categorySlug);
                    if (!empty($catImage) && file_exists($imagePath . $catImage)) {
                        $finalImage = $catImage;
                    }
                }
            }
            $row['image'] = $finalImage;
            // ---------------------------------------------------------
            
            // Check if product exists (by SKU)
            $existing = $this->getProductBySKU($row['sku']);
            
            try {
                if ($existing) {
                    // Update existing product
                    $this->updateProduct($existing['entity_id'], $row);
                    $results['updated']++;
                } else {
                    // Insert new product
                    // If Name is missing here, we MUST generate it
                    if (empty($row['name']) && !empty($row['sku'])) {
                        $row['name'] = ucwords(str_replace(['-', '_'], ' ', $row['sku']));
                    }
                    
                    $this->insertProduct($row);
                    $results['success']++;
                }
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Row $rowNum: " . $e->getMessage();
                $failedRow = $row;
                $failedRow['import_error_reason'] = $e->getMessage();
                $results['failed_rows'][] = $failedRow;
            }
        }
        
        return $results;
    }

    /**
     * Helper to get category image by slug
     */
    private function getCategoryImageBySlug($slug) {
        if (empty($slug)) return null;
        $stmt = $this->pdo->prepare("SELECT image FROM catalog_category_entity WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetchColumn();
    }
    
    /**
     * Helper function to convert empty strings to null for numeric fields
     */
    private function emptyToNull($value) {
        if ($value === null || (is_string($value) && trim($value) === '')) {
            return null;
        }
        return $value;
    }

    /**
     * Helper to safely convert to boolean compatible with Postgres
     */
    private function toBool($value) {
        if ($value === null || $value === '') return 'f';
        if (is_bool($value)) return $value ? 't' : 'f';
        $v = strtolower(trim((string)$value));
        return ($v === 'true' || $v === '1' || $v === 't' || $v === 'yes') ? 't' : 'f';
        return ($v === 'true' || $v === '1' || $v === 't' || $v === 'yes') ? 't' : 'f';
    }

    /**
     * Generate SKU from Name
     */
    private function generateSku($name) {
        $sku = strtolower(trim($name));
        $sku = preg_replace('/[^a-z0-9]+/', '-', $sku);
        $sku = trim($sku, '-');
        return $sku ?: 'sku-' . time() . '-' . rand(100,999);
    }

    /**
     * Helper to clean price strings and convert USD to INR
     */
    private function cleanPrice($value) {
        if ($value === null || $value === '') return 0.00;
        
        // Detect if value is in Dollars (contains $)
        $isDollar = strpos((string)$value, '$') !== false;
        
        // Remove everything except numbers, dot, and minus
        $cleaned = preg_replace('/[^0-9\.-]/', '', $value);
        $amount = (float)$cleaned;
        
        // Convert to INR if it was Dollar (1 USD = 90.42 INR)
        if ($isDollar) {
            $amount = $amount * 90.42;
        }
        
        return round($amount, 2);
    }
    
    /**
     * Helper to clean standard strings
     */
    private function cleanString($value) {
        if ($value === null) return null;
        return trim((string)$value);
    }
    
    /**
     * Validate product data
     */
    private function validateProductData($data) {
        // Required fields
        if (empty($data['name']) && empty($data['sku'])) {
            return ['valid' => false, 'error' => 'SKU or Name is required'];
        }
        // Name is optional check now (handled in import logic)
        
        if (!isset($data['price'])) {
            return ['valid' => false, 'error' => 'Valid price is required'];
        }
        $price = $this->cleanPrice($data['price']);
        if ($price < 0) {
             return ['valid' => false, 'error' => 'Price cannot be negative'];
        }
        
        // Stock Quantity Required
        if (!isset($data['stock_qty']) || $data['stock_qty'] === '') {
             return ['valid' => false, 'error' => 'Stock Quantity is required'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Get product by SKU
     */
    private function getProductBySKU($sku) {
        $stmt = $this->pdo->prepare("SELECT entity_id, sku FROM catalog_product_entity WHERE sku = ?");
        $stmt->execute([$sku]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Insert new product into database
     */
    private function insertProduct($data) {
        // Get category and brand IDs
        $categoryId = $this->getCategoryIdBySlug($data['category'] ?? null);
        $brandId = $this->getBrandIdBySlug($data['brand'] ?? null);
        
        $stmt = $this->pdo->prepare("
            INSERT INTO catalog_product_entity 
            (sku, name, price, old_price, category_id, brand_id, description, image, is_featured, rating, review_count, stock_qty)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $this->cleanString($data['sku']),
            $this->cleanString($data['name']),
            $this->cleanPrice($data['price']),
            $this->emptyToNull($this->cleanPrice($data['old_price'] ?? null) ?: null),
            $categoryId,
            $brandId,
            $this->emptyToNull($this->cleanString($data['description'] ?? null)),
            $this->emptyToNull($this->cleanString($data['image'] ?? null)),
            $this->toBool($data['is_featured'] ?? false),
            (float)$this->cleanPrice($data['rating'] ?? 0), 
            (int)($this->cleanPrice($data['review_count'] ?? 0)), // Use cleanPrice to handle "1,000"
            (int)($this->cleanPrice($data['stock_qty'] ?? 0))
        ]);
    }
    
    /**
     * Update existing product
     */
    private function updateProduct($id, $data) {
        $categoryId = $this->getCategoryIdBySlug($data['category'] ?? null);
        $brandId = $this->getBrandIdBySlug($data['brand'] ?? null);
        
        $stmt = $this->pdo->prepare("
            UPDATE catalog_product_entity 
            SET name = ?, price = ?, old_price = ?, category_id = ?, brand_id = ?, 
                description = ?, image = ?, is_featured = ?, rating = ?, review_count = ?,
                stock_qty = ?, updated_at = CURRENT_TIMESTAMP
            WHERE entity_id = ?
        ");
        
        $stmt->execute([
            $this->cleanString($data['name']),
            $this->cleanPrice($data['price']),
            $this->emptyToNull($this->cleanPrice($data['old_price'] ?? null) ?: null),
            $categoryId,
            $brandId,
            $this->emptyToNull($this->cleanString($data['description'] ?? null)),
            $this->emptyToNull($this->cleanString($data['image'] ?? null)),
            $this->toBool($data['is_featured'] ?? false),
            (float)$this->cleanPrice($data['rating'] ?? 0),
            (int)($this->cleanPrice($data['review_count'] ?? 0)),
            (int)($this->cleanPrice($data['stock_qty'] ?? 0)),
            $id
        ]);
    }
    
    /**
     * Get or Create category ID by slug
     */
    private function getCategoryIdBySlug($slug) {
        $slug = $this->cleanString($slug);
        if (empty($slug)) return null;
        
        $stmt = $this->pdo->prepare("SELECT entity_id FROM catalog_category_entity WHERE slug = ? OR name ILIKE ?");
        $stmt->execute([$slug, $slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result['entity_id'];
        }
        
        // Auto-create if not found
        return $this->createCategory($slug);
    }
    
    /**
     * Create new Category
     */
    private function createCategory($name) {
        $slug = $this->generateSlug($name);
        try {
            $stmt = $this->pdo->prepare("INSERT INTO catalog_category_entity (name, slug) VALUES (?, ?) RETURNING entity_id");
            // Note: RETURNING is Postgres specific. For MySQL use lastInsertId.
            // Assuming Postgres based on previous conversation context about "invalid text representation" issues 
            // BUT earlier I used lastInsertId() for Order. Let's check schema.
            // Schema used SERIAL. Access pattern suggests standard PDO.
            // If MySQL:
            if ($this->isPostgres()) {
                 $stmt->execute([ucwords($name), $slug]);
                 return $stmt->fetchColumn();
            } else {
                 $stmt = $this->pdo->prepare("INSERT INTO catalog_category_entity (name, slug) VALUES (?, ?)");
                 $stmt->execute([ucwords($name), $slug]);
                 return $this->pdo->lastInsertId();
            }
        } catch (Exception $e) {
            // Handle race condition or duplicate collision gracefully
            // Retry fetch
             $stmt = $this->pdo->prepare("SELECT entity_id FROM catalog_category_entity WHERE slug = ?");
             $stmt->execute([$slug]);
             return $stmt->fetchColumn() ?: null;
        }
    }

    /**
     * Get or Create brand ID by slug
     */
    private function getBrandIdBySlug($slug) {
        $slug = $this->cleanString($slug);
        if (empty($slug)) return null;
        
        $stmt = $this->pdo->prepare("SELECT entity_id FROM catalog_brand_entity WHERE slug = ? OR name ILIKE ?");
        $stmt->execute([$slug, $slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
             return $result['entity_id'];
        }
        
        // Auto-create
        return $this->createBrand($slug);
    }
    
    /**
     * Create new Brand
     */
    private function createBrand($name) {
        $slug = $this->generateSlug($name);
        try {
            if ($this->isPostgres()) {
                 $stmt = $this->pdo->prepare("INSERT INTO catalog_brand_entity (name, slug) VALUES (?, ?) RETURNING entity_id");
                 $stmt->execute([ucwords($name), $slug]);
                 return $stmt->fetchColumn();
            } else {
                 $stmt = $this->pdo->prepare("INSERT INTO catalog_brand_entity (name, slug) VALUES (?, ?)");
                 $stmt->execute([ucwords($name), $slug]);
                 return $this->pdo->lastInsertId();
            }
        } catch (Exception $e) {
             $stmt = $this->pdo->prepare("SELECT entity_id FROM catalog_brand_entity WHERE slug = ?");
             $stmt->execute([$slug]);
             return $stmt->fetchColumn() ?: null;
        }
    }
    
    /**
     * Helper to check DB type (simple heuristic or config check)
     * For now, assuming MySQL/MariaDB based on XAMPP path but ensuring safety.
     * Actually, user context mentioned Postgres issues earlier.
     * Let's check if we can rely on one.
     * The schema `SERIAL` and `RETURNING` are Postgres.
     * But XAMPP is usually MySQL.
     * I'll assume standard PDO lastInsertId works for both if Insert is standard.
     * But `RETURNING` is only Postgres. Use lastInsertId for broader compat if possible.
     * Postgres supports lastInsertId() on the connection object too if sequence is used.
     */
    private function isPostgres() {
        return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql';
    }
    
    private function generateSlug($string) {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    }
}
?>
