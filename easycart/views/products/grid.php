<?php
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    
    if(!empty($items)):
        foreach($items as $product): 
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
        <button class="btn-wishlist-toggle" onclick="toggleWishlist(<?php echo $product['id']; ?>, this)">
            <i class="<?php echo $in_wishlist ? 'fas active-wishlist' : 'far'; ?> fa-heart"></i>
        </button>
        <div class="product-image-container">
            <?php if($discount_percent > 0): ?>
                <span class="discount-badge"><?php echo $discount_percent; ?>% OFF</span>
            <?php endif; ?>
            <img src="<?php echo $base_path; ?>images/<?php echo $product['image']; ?>" alt="<?php echo $product['title']; ?>">
        </div>
        <div class="product-stock-status">
            <?php if ($product['stock_qty'] <= 0): ?>
                <span class="stock-badge stock-out"><i class="fas fa-times-circle"></i> Out of Stock</span>
            <?php elseif ($product['stock_qty'] <= 5): ?>
                <span class="stock-badge stock-low"><i class="fas fa-exclamation-triangle"></i> Only <?php echo $product['stock_qty']; ?> left</span>
            <?php else: ?>
                <span class="stock-badge stock-in"><i class="fas fa-check-circle"></i> In Stock</span>
            <?php endif; ?>
        </div>
        <h3><?php echo $product['title']; ?></h3>
        <p class="price">₹<?php echo $product['price']; ?>
            <?php if($product['old_price']): ?>
                <span class="old-price">₹<?php echo $product['old_price']; ?></span>
            <?php endif; ?>
        </p>
        
        <div class="quick-add-container">
            <?php if ($product['stock_qty'] <= 0): ?>
                <button class="btn btn-quick-add disabled" disabled style="background: #f1f5f9; color: #94a3b8; border-color: #e2e8f0; cursor: not-allowed; opacity: 0.7;">
                    <i class="fas fa-ban"></i> Out of Stock
                </button>
            <?php elseif ($qty > 0): ?>
                <div class="qty-selector">
                    <button class="btn-qty btn-minus" onclick="updateQuickQty(<?php echo $product['id']; ?>, -1)">-</button>
                    <span class="qty-display"><?php echo $qty; ?></span>
                    <button class="btn-qty btn-plus" onclick="updateQuickQty(<?php echo $product['id']; ?>, 1)">+</button>
                </div>
            <?php else: ?>
                <button class="btn btn-quick-add" onclick="updateQuickQty(<?php echo $product['id']; ?>, 1)">
                    <i class="fas fa-plus"></i> Add to Cart
                </button>
            <?php endif; ?>
        </div>

        <a href="<?php echo $base_path; ?>product-details?id=<?php echo $product['id']; ?>" class="btn-view-details">View Details <i class="fas fa-chevron-right"></i></a>

    </div>
    <?php endforeach; 
    else: ?>
    <div class="no-results" style="display:block;">
        <i class="fas fa-search"></i>
        <p>No products found matching your filters.</p>
    </div>
    <?php endif; 
    
    // Render Pagination UI
    if ($total_pages > 1): ?>
    <div class="pagination">
        <!-- Prev Button -->
        <?php if ($page > 1): ?>
            <button onclick="changePage(<?php echo $page - 1; ?>)" class="btn-page"><i class="fas fa-chevron-left"></i></button>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <button onclick="changePage(<?php echo $i; ?>)" class="btn-page <?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </button>
        <?php endfor; ?>

        <!-- Next Button -->
        <?php if ($page < $total_pages): ?>
            <button onclick="changePage(<?php echo $page + 1; ?>)" class="btn-page"><i class="fas fa-chevron-right"></i></button>
        <?php endif; ?>
    </div>
    <?php endif; ?>
