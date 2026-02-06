<?php
// data/productsdata.php
// Simplified to work with main entity tables only

// Try new config first, fallback to old includes
if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
} else {
    require_once __DIR__ . '/../includes/db.php';
}

try {
    // 1. Fetch Categories
    $categories = [];
    $stmtCat = $pdo->query("SELECT slug, name, image FROM catalog_category_entity");
    while ($row = $stmtCat->fetch()) {
        $categories[$row['slug']] = [
            'name'  => $row['name'],
            'image' => $row['image'] ?? 'default.png'
        ];
    }

    // 2. Fetch Brands
    $brands = [];
    $stmtBrand = $pdo->query("SELECT slug, name FROM catalog_brand_entity");
    while ($row = $stmtBrand->fetch()) {
        $brands[$row['slug']] = $row['name'];
    }

    // 3. Fetch Products (joined with main entity tables)
    $products = [];
    
    $stmtProd = $pdo->query("
        SELECT 
            p.entity_id,
            p.name,
            p.price,
            p.old_price,
            p.image,
            p.description,
            p.is_featured,
            p.rating,
            p.review_count,
            p.stock_qty,
            (SELECT STRING_AGG(c.slug, ',') FROM catalog_category_products cp 
             JOIN catalog_category_entity c ON cp.category_id = c.entity_id 
             WHERE cp.product_id = p.entity_id) as category_slugs,
            (SELECT STRING_AGG(b.slug, ',') FROM catalog_brand_products bp 
             JOIN catalog_brand_entity b ON bp.brand_id = b.entity_id 
             WHERE bp.product_id = p.entity_id) as brand_slugs,
            (SELECT STRING_AGG(g.image, ',' ORDER BY g.is_primary DESC, g.image_id ASC) 
             FROM catalog_product_gallery g WHERE g.product_id = p.entity_id) as gallery_images,
            STRING_AGG(DISTINCT a.attribute_value, '|' ORDER BY a.attribute_value) as features
        FROM catalog_product_entity p
        LEFT JOIN catalog_product_attribute a ON p.entity_id = a.product_id AND a.attribute_name = 'feature'
        GROUP BY p.entity_id, p.stock_qty
        ORDER BY p.entity_id ASC
    ");
    
    $rawProducts = $stmtProd->fetchAll();

    foreach ($rawProducts as $row) {
        $pId = $row['entity_id'];
        
        // Process categories
        $categorySlugs = !empty($row['category_slugs']) ? explode(',', $row['category_slugs']) : [];
        $category_slug = !empty($categorySlugs) ? $categorySlugs[0] : 'uncategorized';

        // Process brands
        $brandSlugs = !empty($row['brand_slugs']) ? explode(',', $row['brand_slugs']) : [];
        $brand_slug = !empty($brandSlugs) ? $brandSlugs[0] : 'unknown';
        
        // Process gallery images (Main + 3 from DB = 4 Total)
        $gallery = [];
        if (!empty($row['gallery_images'])) {
            $gallery = explode(',', $row['gallery_images']);
        }
        
        // Prepend main image to be the first thumbnail
        array_unshift($gallery, $row['image']);
        $gallery = array_values($gallery);

        // Process features
        $features = [];
        if (!empty($row['features'])) {
            $features = explode('|', $row['features']);
        }

        $item = [
            'id'          => $pId,
            'title'       => $row['name'],
            'category'    => $category_slug, 
            'categories'  => $categorySlugs,
            'brand'       => $brand_slug,
            'brands'      => $brandSlugs,
            'price'       => number_format($row['price']), 
            'old_price'   => $row['old_price'] ? number_format($row['old_price']) : null,
            'image'       => $row['image'],
            'url'         => 'productdetails.php?id=' . $pId,
            'featured'    => (bool)$row['is_featured'],
            'rating'      => (float)$row['rating'],
            'reviews'     => (int)$row['review_count'],
            'stock_qty'   => (int)$row['stock_qty'],
            'description' => $row['description'],
            'features'    => $features,
            'gallery'     => $gallery
        ];

        // Dynamic shipping method
        if ($row['price'] <= 300) {
            $item['shipping_method'] = 'express';
        } else {
            $item['shipping_method'] = 'freight';
        }

        $products[] = $item;
    }

} catch (PDOException $e) {
    error_log("DB Fetch Error: " . $e->getMessage());
    $products = [];
    $categories = [];
    $brands = [];
}
?>