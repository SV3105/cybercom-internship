<?php
// data/productsdata.php
// Simplified to work with main entity tables only

require_once __DIR__ . '/../includes/db.php';

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
            c.slug as category_slug,
            b.slug as brand_slug,
            STRING_AGG(g.image, ',' ORDER BY g.is_primary DESC, g.image_id ASC) as gallery_images,
            STRING_AGG(DISTINCT a.attribute_value, '|' ORDER BY a.attribute_value) as features
        FROM catalog_product_entity p
        LEFT JOIN catalog_category_entity c ON p.category_id = c.entity_id
        LEFT JOIN catalog_brand_entity b ON p.brand_id = b.entity_id
        LEFT JOIN catalog_product_gallery g ON p.entity_id = g.product_id
        LEFT JOIN catalog_product_attribute a ON p.entity_id = a.product_id AND a.attribute_name = 'feature'
        GROUP BY p.entity_id, c.slug, b.slug
        ORDER BY p.entity_id ASC
    ");
    
    $rawProducts = $stmtProd->fetchAll();

    foreach ($rawProducts as $row) {
        $pId = $row['entity_id'];
        
        // Process gallery images
        $gallery = [];
        if (!empty($row['gallery_images'])) {
            $gallery = array_unique(explode(',', $row['gallery_images']));
        } elseif (!empty($row['image'])) {
            $gallery = [$row['image']];
        }

        // Process features
        $features = [];
        if (!empty($row['features'])) {
            $features = explode('|', $row['features']);
        }

        $item = [
            'id'          => $pId,
            'title'       => $row['name'],
            'category'    => $row['category_slug'] ?? 'independent', 
            'brand'       => $row['brand_slug'] ?? 'generic',
            'price'       => number_format($row['price']), 
            'old_price'   => $row['old_price'] ? number_format($row['old_price']) : null,
            'image'       => $row['image'],
            'url'         => 'productdetails.php?id=' . $pId,
            'featured'    => (bool)$row['is_featured'],
            'rating'      => (float)$row['rating'],
            'reviews'     => (int)$row['review_count'],
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