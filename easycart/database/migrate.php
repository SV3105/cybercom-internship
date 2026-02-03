<?php
// database/migrate.php
// Run this script ONCE to populate your database with existing data.

require_once '../includes/db.php';
require_once '../data/productsdata.php';
// require_once '../data/usersdata.php'; // Uncomment if you have users to migrate
// require_once '../data/ordersdata.php'; // Uncomment if you have orders to migrate

echo "Starting Migration...\n";

try {
    $pdo->beginTransaction();

    // 1. Migrate Categories
    // Extract unique categories from products
    $categories = [];
    foreach ($products as $p) {
        if (!in_array($p['category'], $categories)) {
            $categories[] = $p['category'];
        }
    }

    $catMap = []; // slug -> id
    
    // Hardcoded category images map based on your products.php logic or previous data
    $catImages = [
        'electronics' => 'earbuds.png',
        'smartphones' => 'smartphone.png',
        'fashion' => 'sneakers.png',
        'home' => 'LGfridge.png',
        'beauty' => 'perfume.png',
        'sports' => 'sneakers.png',
        'snacks' => 'meggie.jpg'
    ];
    $catNames = [
        'electronics' => 'Electronics',
        'smartphones' => 'Smartphones',
        'fashion' => 'Fashion',
        'home' => 'Home & Living',
        'beauty' => 'Beauty',
        'sports' => 'Sports',
        'snacks' => 'Snacks & Groceries'
    ];

    echo "Migrating Categories...\n";
    $stmtCat = $pdo->prepare("INSERT INTO catalog_category_entity (name, slug, image) VALUES (:name, :slug, :image) RETURNING entity_id");
    
    foreach ($categories as $slug) {
        $name = isset($catNames[$slug]) ? $catNames[$slug] : ucfirst($slug);
        $image = isset($catImages[$slug]) ? $catImages[$slug] : 'default.png';
        
        // Check if exists
        $check = $pdo->prepare("SELECT entity_id FROM catalog_category_entity WHERE slug = ?");
        $check->execute([$slug]);
        $existing = $check->fetchColumn();

        if ($existing) {
            $catMap[$slug] = $existing;
        } else {
            $stmtCat->execute(['name' => $name, 'slug' => $slug, 'image' => $image]);
            $catMap[$slug] = $stmtCat->fetchColumn();
        }
    }

    // 2. Migrate Brands
    $brands = [];
    foreach ($products as $p) {
        if (!in_array($p['brand'], $brands)) {
            $brands[] = $p['brand'];
        }
    }
    
    $brandMap = []; // slug -> id
    echo "Migrating Brands...\n";
    $stmtBrand = $pdo->prepare("INSERT INTO catalog_brand_entity (name, slug) VALUES (:name, :slug) RETURNING entity_id");

    foreach ($brands as $slug) {
        $name = ucfirst($slug); // You had a $brands array in data file, but simple ucfirst works for now or load that array.
        
        $check = $pdo->prepare("SELECT entity_id FROM catalog_brand_entity WHERE slug = ?");
        $check->execute([$slug]);
        $existing = $check->fetchColumn();

        if ($existing) {
            $brandMap[$slug] = $existing;
        } else {
            $stmtBrand->execute(['name' => $name, 'slug' => $slug]);
            $brandMap[$slug] = $stmtBrand->fetchColumn();
        }
    }

    // 3. Migrate Products
    echo "Migrating Products...\n";
    $stmtProd = $pdo->prepare("
        INSERT INTO catalog_product_entity 
        (sku, name, price, old_price, image, description, is_featured, rating, review_count) 
        VALUES (:sku, :name, :price, :old_price, :image, :description, :is_featured, :rating, :review_count) 
        RETURNING entity_id
    ");

    $stmtProdEx = $pdo->prepare("SELECT entity_id FROM catalog_product_entity WHERE sku = ?");

    $stmtLinkCat = $pdo->prepare("INSERT INTO catalog_category_products (category_id, product_id) VALUES (?, ?)");
    $stmtLinkBrand = $pdo->prepare("INSERT INTO catalog_brand_products (brand_id, product_id) VALUES (?, ?)");
    $stmtFeature = $pdo->prepare("INSERT INTO catalog_product_attribute (product_id, attribute_code, value) VALUES (?, 'feature', ?)");
    $stmtGallery = $pdo->prepare("INSERT INTO catalog_product_image (product_id, image_path, is_thumbnail) VALUES (?, ?, ?)");

    foreach ($products as $p) {
        $sku = 'SKU-' . $p['id'];
        
        // Clean price
        $price = (float)str_replace(',', '', $p['price']);
        $old_price = null;
        if (isset($p['old_price']) && $p['old_price']) {
            $old_price = (float)str_replace(',', '', $p['old_price']);
        }

        $stmtProdEx->execute([$sku]);
        $existingId = $stmtProdEx->fetchColumn();

        $prodId = $existingId;

        if (!$prodId) {
            $stmtProd->execute([
                'sku' => $sku,
                'name' => $p['title'],
                'price' => $price,
                'old_price' => $old_price,
                'image' => $p['image'],
                'description' => isset($p['description']) ? $p['description'] : '',
                'is_featured' => isset($p['featured']) ? ($p['featured'] ? 'true' : 'false') : 'false',
                'rating' => isset($p['rating']) ? $p['rating'] : 0,
                'review_count' => isset($p['reviews']) ? $p['reviews'] : 0
            ]);
            $prodId = $stmtProd->fetchColumn();

            // Link Category
            if (isset($catMap[$p['category']])) {
                $stmtLinkCat->execute([$catMap[$p['category']], $prodId]);
            }

            // Link Brand
            if (isset($brandMap[$p['brand']])) {
                $stmtLinkBrand->execute([$brandMap[$p['brand']], $prodId]);
            }

            // Features (Attribute)
            if (isset($p['features']) && is_array($p['features'])) {
                foreach ($p['features'] as $feature) {
                    $stmtFeature->execute([$prodId, $feature]);
                }
            }

            // Gallery
            if (isset($p['gallery']) && is_array($p['gallery'])) {
                foreach ($p['gallery'] as $img) {
                    $isThumb = ($img == $p['image']) ? 'true' : 'false';
                    $stmtGallery->execute([$prodId, $img, $isThumb]);
                }
            }
        }
    }

    $pdo->commit();
    echo "Migration Completed Successfully!\n";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Migration Failed: " . $e->getMessage() . "\n";
}
?>
