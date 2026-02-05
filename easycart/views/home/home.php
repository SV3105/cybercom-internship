
    <!-- New Hero Section: Split Layout -->
    <div class="hero-split">
        <div class="hero-content">
            <span class="badge">SEASONAL OFFER</span>
            <h2>Style Meets <br>Innovation</h2>
            <p>Discover the latest trends in fashion and technology with exclusive deals.</p>
            <div class="hero-btns">
                <a href="<?php echo BASE_URL; ?>products" class="btn">Shop Now</a>
                <a href="<?php echo BASE_URL; ?>products?category=fashion" class="btn btn-outline">Trending</a>
            </div>
        </div>
        <div class="hero-image">
            <div class="hero-composition">
                <div class="hero-circle-bg"></div>
                <div class="floating-pill pill-1">
                    <i class="fas fa-bolt" style="color: #ffb400;"></i> Fast Shipping
                </div>
                <div class="floating-pill pill-2">
                    <i class="fas fa-tag"></i> 50% OFF
                </div>
                <div class="hero-visual-card">
                    <div class="visual-img-placeholder">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="visual-content">
                        <h4>Ultra Boost 5.0</h4>
                        <p>Comfort Redefined</p>
                        <div class="visual-price">₹2,999</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Main Content Card -->
        <div class="page-content hero-overlap">
            
            <!-- Trending Categories (Images) -->
            <section class="section">
                <div class="section-header">
                    <h2>Shop By Category</h2>
                    <a href="<?php echo BASE_URL; ?>products" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="categories-grid">
                    <?php 
                    // Use $categories from data/productsdata.php
                    $allowed_display_cats = ['electronics', 'fashion', 'beauty', 'smartphones'];
                    foreach($categories as $cat_slug => $cat_data): 
                        if (!in_array($cat_slug, $allowed_display_cats)) continue;
                    ?>
                    <div class="category-card-img">
                        <a href="<?php echo BASE_URL; ?>products?category=<?php echo $cat_slug; ?>">
                            <img src="<?php echo BASE_URL; ?>images/<?php echo $cat_data['image']; ?>" alt="<?php echo $cat_data['name']; ?>">
                            <div class="cat-overlay">
                                <h3><?php echo $cat_data['name']; ?></h3>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Featured Products -->
            <section class="section section-blockbuster">
                <div class="section-header">
                    <h2>Blockbuster Deals</h2>
                    <a href="<?php echo BASE_URL; ?>products" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="products-grid">
                    <?php 
                    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
                    $bb_count = 0;
                    foreach($products as $product): ?>
                        <?php if(isset($product['featured']) && $product['featured']): 
                            if($bb_count >= 4) break;
                            $bb_count++;
                            $qty = isset($cart[$product['id']]) ? (int)$cart[$product['id']] : 0;
                            
                            // Calculate Discount
                            $discount_percent = 0;
                            if($product['old_price']) {
                                $p_val = floatval(str_replace(',', '', $product['price']));
                                $o_val = floatval(str_replace(',', '', $product['old_price']));
                                if($o_val > 0) {
                                    $discount_percent = round((($o_val - $p_val) / $o_val) * 100);
                                }
                            }
                        ?>
                        <div class="product-card" data-id="<?php echo $product['id']; ?>">
                            <?php 
                            $in_wishlist = isset($_SESSION['wishlist']) && in_array($product['id'], $_SESSION['wishlist']);
                            ?>
                            <button class="btn-wishlist-toggle" onclick="toggleWishlist(<?php echo $product['id']; ?>, this, true)">
                                <i class="<?php echo $in_wishlist ? 'fas active-wishlist' : 'far'; ?> fa-heart"></i>
                            </button>
                            <div class="product-image-container">
                                <?php if($discount_percent > 0): ?>
                                    <span class="discount-badge"><?php echo $discount_percent; ?>% OFF</span>
                                <?php endif; ?>
                                <img src="<?php echo BASE_URL; ?>images/<?php echo $product['image']; ?>" alt="<?php echo $product['title']; ?>">
                            </div>
                            <h3><?php echo $product['title']; ?></h3>
                            <p class="price">₹<?php echo $product['price']; ?> 
                                <?php if($product['old_price']): ?>
                                <span class="old-price">₹<?php echo $product['old_price']; ?></span>
                                <?php endif; ?>
                            </p>

                            <div class="quick-add-container">
                                <?php if ($qty > 0): ?>
                                    <div class="qty-selector">
                                        <button class="btn-qty btn-minus" onclick="updateQuickQty(<?php echo $product['id']; ?>, -1, true)">-</button>
                                        <span class="qty-display"><?php echo $qty; ?></span>
                                        <button class="btn-qty btn-plus" onclick="updateQuickQty(<?php echo $product['id']; ?>, 1, true)">+</button>
                                    </div>
                                <?php else: ?>
                                    <button class="btn btn-quick-add" onclick="updateQuickQty(<?php echo $product['id']; ?>, 1, true)">
                                        <i class="fas fa-plus"></i> Add to Cart
                                    </button>
                                <?php endif; ?>
                            </div>

                            <a href="<?php echo BASE_URL; ?>product-details?id=<?php echo $product['id']; ?>" class="btn-view-details-pill">View Details</a>

                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                </div>
            </section>

            <!-- Top Indian Brands (Clickable) -->
            <section class="section">
                <h2>Top Indian Brands</h2>
                <div class="brands-grid">
                    <a href="<?php echo BASE_URL; ?>products?brand=boat" class="brand-card">
                        <h3 class="brand-boat">boAt</h3>
                        <p>Audio Wearables</p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>products?brand=fabindia" class="brand-card">
                        <h3 class="brand-fabindia">FabIndia</h3>
                        <p>Ethnic Fashion</p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>products?brand=mamaearth" class="brand-card">
                        <h3 class="brand-mamaearth">Mamaearth</h3>
                        <p>Toxin Free</p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>products?brand=titan" class="brand-card">
                        <h3 class="brand-titan">Titan</h3>
                        <p>Timeless Watches</p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>products?brand=hrx" class="brand-card">
                        <h3 class="brand-hrx">HRX</h3>
                        <p>Fitness Gear</p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>products?brand=noise" class="brand-card">
                        <h3 class="brand-noise">Noise</h3>
                        <p>Smart Tech</p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>products?brand=vip" class="brand-card">
                        <h3 class="brand-vip">VIP</h3>
                        <p>Travel Gear</p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>products?brand=bajaj" class="brand-card">
                        <h3 class="brand-bajaj">Bajaj</h3>
                        <p>Home Appliances</p>
                    </a>
                </div>
            </section>

        </div>
    </div>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/wishlist.css">
    <script src="<?php echo BASE_URL; ?>js/products.js?v=<?php echo time(); ?>"></script>
