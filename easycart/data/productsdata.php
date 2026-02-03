<?php
// data/productsdata.php
// Refactored to fetch data from PostgreSQL database

require_once __DIR__ . '/../includes/db.php';

try {
    // 1. Fetch Categories
    // Structure needed: 'slug' => ['name' => ..., 'image' => ...]
    $categories = [];
    $stmtCat = $pdo->query("SELECT * FROM catalog_category_entity");
    while ($row = $stmtCat->fetch()) {
        $categories[$row['slug']] = [
            'name'  => $row['name'],
            'image' => $row['image'] ?? 'default.png'
        ];
    }

    // 2. Fetch Brands
    // Structure needed: 'slug' => 'Name'
    $brands = [];
    $stmtBrand = $pdo->query("SELECT * FROM catalog_brand_entity");
    while ($row = $stmtBrand->fetch()) {
        $brands[$row['slug']] = $row['name'];
    }

    // 3. Fetch Products
    $products = [];
    
    // Main Product Query
    // We join with categories/brands tables to get slugs (needed for 'category' and 'brand' keys)
    $stmtProd = $pdo->query("
        SELECT 
            p.*,
            c.slug as category_slug,
            b.slug as brand_slug
        FROM catalog_product_entity p
        LEFT JOIN catalog_category_products ccp ON p.entity_id = ccp.product_id
        LEFT JOIN catalog_category_entity c ON ccp.category_id = c.entity_id
        LEFT JOIN catalog_brand_products cbp ON p.entity_id = cbp.product_id
        LEFT JOIN catalog_brand_entity b ON cbp.brand_id = b.entity_id
        ORDER BY p.entity_id ASC
    ");
    
    $rawProducts = $stmtProd->fetchAll();

    // Fetch Features (Attributes) in bulk to avoid N+1
    $stmtFeat = $pdo->query("SELECT product_id, value FROM catalog_product_attribute WHERE attribute_code = 'feature'");
    $featuresMap = [];
    while ($row = $stmtFeat->fetch()) {
        $featuresMap[$row['product_id']][] = $row['value'];
    }

    // Fetch Gallery Images in bulk
    $stmtImg = $pdo->query("SELECT product_id, image_path FROM catalog_product_image");
    $galleryMap = [];
    while ($row = $stmtImg->fetch()) {
        $galleryMap[$row['product_id']][] = $row['image_path'];
    }

    foreach ($rawProducts as $row) {
        $pId = $row['entity_id'];
        
        $item = [
            'id'          => $pId,
            'title'       => $row['name'],
            'category'    => $row['category_slug'] ?? 'uncategorized',
            'brand'       => $row['brand_slug'] ?? 'generic',
            // Format price to match formatted string "1,299" style if needed, 
            // but standardizing on numbers is better. However, app expects strings often.
            // Let's keep it numeric or string depending on existing logic?
            // Existing logic had '299', '1,999'. number_format helps mimic that.
            'price'       => number_format($row['price']), 
            'old_price'   => $row['old_price'] ? number_format($row['old_price']) : null,
            'image'       => $row['image'],
            'url'         => 'productdetails.php?id=' . $pId,
            'featured'    => (bool)$row['is_featured'],
            'rating'      => (float)$row['rating'],
            'reviews'     => (int)$row['review_count'],
            'description' => $row['description'],
            'features'    => $featuresMap[$pId] ?? [],
            'gallery'     => $galleryMap[$pId] ?? [$row['image']]
        ];

        // --- ADD DYNAMIC SHIPPING METHOD (Legacy Logic) ---
        // Rule: Price <= 300 -> 'express', Price > 300 -> 'freight'
        // Using raw price from DB row for calculation
        if ($row['price'] <= 300) {
            $item['shipping_method'] = 'express';
        } else {
            $item['shipping_method'] = 'freight';
        }

        $products[] = $item;
    }

} catch (PDOException $e) {
    // Fallback or Error
    error_log("DB Fetch Error: " . $e->getMessage());
    $products = [];
    $categories = [];
    $brands = [];
}
?>