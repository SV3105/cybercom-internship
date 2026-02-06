
    <div class="container">
      <div class="page-content layout-transparent product-detail-container">
        <div class="product-detail">
            <div class="product-image-section">
                <div class="main-image">
                    <button class="nav-arrow prev" onclick="callChangeImage(-1)"><i class="fas fa-chevron-left"></i></button>

                    <img id="mainProductImg" src="<?php echo $base_path; ?>images/<?php echo $current_product['image']; ?>" alt="<?php echo $current_product['title']; ?>">

                    <button class="nav-arrow next" onclick="callChangeImage(1)"><i class="fas fa-chevron-right"></i></button>

                </div>
                
                <?php if(isset($current_product['gallery'])): ?>
                <div class="thumbnail-gallery">
                    <?php foreach($current_product['gallery'] as $img): ?>
                    <div class="thumb <?php echo ($img == $current_product['image']) ? 'active' : ''; ?>" onclick="callSwitchImage(this, '<?php echo $base_path; ?>images/<?php echo $img; ?>')">
                        <img src="<?php echo $base_path; ?>images/<?php echo $img; ?>" alt="Thumbnail">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="product-info">
            <h2><?php echo $current_product['title']; ?></h2>
            
            <div class="product-categories">
                <?php 
                $productCats = isset($current_product['categories']) ? (array)$current_product['categories'] : [$current_product['category']];
                foreach ($productCats as $catSlug): 
                    $catName = isset($categories[$catSlug]) ? $categories[$catSlug]['name'] : ucwords(str_replace('-', ' ', $catSlug));
                ?>
                    <a href="products?category=<?php echo $catSlug; ?>" class="category-tag"><?php echo $catName; ?></a>
                <?php endforeach; ?>
            </div>

            <div class="rating">
                <?php 
                $rating = isset($current_product['rating']) ? $current_product['rating'] : 4.0;
                $full_stars = floor($rating);
                $half_star = $rating - $full_stars >= 0.5;
                
                for($i=1; $i<=5; $i++) {
                    if($i <= $full_stars) {
                        echo '<i class="fas fa-star"></i>';
                    } elseif($i == $full_stars + 1 && $half_star) {
                        echo '<i class="fas fa-star-half-alt"></i>';
                    } else {
                        echo '<i class="far fa-star"></i>';
                    }
                }
                ?>
                <span>(<?php echo $rating; ?> stars)</span>
            </div>
            <p class="price">
                ₹ <?php echo $current_product['price']; ?>
                <?php if(isset($current_product['old_price']) && $current_product['old_price']): ?>
                <span style="text-decoration: line-through; color: #999; font-size: 0.8em; margin-left: 10px;">₹ <?php echo $current_product['old_price']; ?></span>
                <?php endif; ?>
            </p>

            <div class="product-stock-status">
                <?php if ($current_product['stock_qty'] <= 0): ?>
                    <span class="stock-badge stock-out"><i class="fas fa-times-circle"></i> Out of Stock</span>
                <?php elseif ($current_product['stock_qty'] <= 5): ?>
                    <span class="stock-badge stock-low"><i class="fas fa-exclamation-triangle"></i> Only <?php echo $current_product['stock_qty']; ?> left</span>
                <?php else: ?>
                    <span class="stock-badge stock-in"><i class="fas fa-check-circle"></i> In Stock</span>
                <?php endif; ?>
            </div>
            
            <div class="product-description">
                <h3>Description</h3>
                <p>
                <?php echo isset($current_product['description']) ? $current_product['description'] : 'Product description not available.'; ?>
                </p>
                
                <?php if(isset($current_product['features']) && is_array($current_product['features']) && !empty($current_product['features'])): ?>
                <div class="features-section">
                    <h3>Key Features</h3>
                    <ul class="features-list">
                        <?php foreach($current_product['features'] as $feature): ?>
                        <li><i class="fas fa-check"></i> <?php echo $feature; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="product-actions">
                <form action="cart" method="POST">
                    <input type="hidden" name="action" value="update_qty">
                    <input type="hidden" name="product_id" value="<?php echo $current_product['id']; ?>">
                    <input type="hidden" name="change" value="1">
                    <button type="submit" class="btn btn-large">Add to Cart</button>
                </form>
                
                <?php 
                $in_wishlist = isset($_SESSION['wishlist']) && in_array($current_product['id'], $_SESSION['wishlist']);
                ?>
                <button class="btn btn-large btn-outline" style="margin-top: 1rem; width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;" onclick="toggleWishlist(<?php echo $current_product['id']; ?>, this)">
                    <i class="<?php echo $in_wishlist ? 'fas active-wishlist' : 'far'; ?> fa-heart"></i>
                    <span class="wishlist-text"><?php echo $in_wishlist ? 'In Wishlist' : 'Add to Wishlist'; ?></span>
                </button>
            </div>
            </div>
        </div>
      </div>
    </div>

    <script src="<?php echo $base_path; ?>js/productdetails.js"></script>
    <script>
        const gallery = <?php echo json_encode(isset($current_product['gallery']) && !empty($current_product['gallery']) ? $current_product['gallery'] : [$current_product['image']]); ?>;
        const basePath = "images/";
        const currentImage = "<?php echo $current_product['image']; ?>";
        let currentIndexObj = { index: gallery.indexOf(currentImage) };
        
        if (currentIndexObj.index === -1) currentIndexObj.index = 0;
        
        console.log('Gallery:', gallery, 'Index:', currentIndexObj.index);
        
        function callSwitchImage(thumb, src) { switchImage(thumb, src, gallery, basePath); }
        function callChangeImage(direction) { changeImage(direction, gallery, basePath, currentIndexObj); }
    </script>
