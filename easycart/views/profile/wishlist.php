
<div class="container">
    <div class="page-content">
        <h1 class="page-title">My Wishlist</h1>
        
        <div class="wishlist-grid">
            <?php if (!empty($wishlist_items)): ?>
                <?php foreach($wishlist_items as $item): ?>
                <div class="product-card wishlist-card" data-id="<?php echo $item['id']; ?>">
                    <button class="btn-wishlist-remove" onclick="toggleWishlist(<?php echo $item['id']; ?>, this)">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="product-image-container">
                        <img src="images/<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>">
                    </div>
                    <h3><?php echo $item['title']; ?></h3>
                    <p class="price">₹<?php echo $item['price']; ?></p>
                    
                    <div class="wishlist-actions">
                        <a href="product-details?id=<?php echo $item['id']; ?>" class="btn-view-details-pill">View Details</a>
                        <div class="quick-add-container">
                            <button class="btn btn-quick-add" onclick="updateQuickQty(<?php echo $item['id']; ?>, 1)">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results" style="grid-column: 1/-1; display:block; text-align:center; padding: 4rem 0;">
                    <i class="far fa-heart" style="font-size: 4rem; color: #ddd; margin-bottom: 1.5rem;"></i>
                    <p>Your wishlist is empty.</p>
                    <a href="products" class="btn" style="margin-top: 1.5rem;">Discover Products</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="js/products.js"></script>
