<?php
// views/cart/cart.php
?>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 class="page-title" style="margin: 0;">Shopping Cart</h1>
            <?php if (!empty($_SESSION['cart'])): ?>
            <button onclick="clearCart()" class="btn-clear-cart">
                <i class="fas fa-trash-alt"></i> Clear Cart
            </button>
            <?php endif; ?>
        </div>
        
        <div class="cart-layout">
            <!-- Cart Items List -->
            <div class="cart-items">
                <?php
                $cart_empty = true;

                if (!empty($_SESSION['cart'])) {
                    $cart_empty = false;
                    foreach($_SESSION['cart'] as $p_id => $qty):
                        // Find product by ID
                        $item = null;
                        foreach($products as $p) {
                            if($p['id'] == $p_id) {
                                $item = $p;
                                break;
                            }
                        }
                        if($item):
                            // Clean price for calc
                            $price_val = (float)str_replace(',', '', $item['price']);
                            $item_total = $price_val * $qty;
                ?>
                <div class="cart-item" data-id="<?php echo $p_id; ?>" data-price="<?php echo $price_val; ?>">
                    <div class="item-visual">
                        <img src="<?php echo $base_path; ?>images/<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>" class="item-img">
                    </div>
                    <div class="item-details">
                        <h3><?php echo $item['title']; ?></h3>
                        <p class="item-category"><?php echo ucfirst($item['category']); ?></p>
                        <h4 class="item-price">
                            ₹<?php echo $item['price']; ?>
                            <?php if(isset($item['old_price']) && !empty($item['old_price'])): ?>
                                <span class="cart-old-price">₹<?php echo $item['old_price']; ?></span>
                                <?php 
                                    $p_val = (float)str_replace(',', '', $item['price']);
                                    $o_val = (float)str_replace(',', '', $item['old_price']);
                                    if($o_val > 0) {
                                        $d_pct = round((($o_val - $p_val) / $o_val) * 100);
                                        if($d_pct > 0) echo "<span class='cart-discount-badge'>{$d_pct}% OFF</span>";
                                    }
                                ?>
                            <?php endif; ?>
                        </h4>
                    </div>
                    <div class="item-actions">
                        <div class="quantity-control">
                            <button type="button" class="qty-btn" onclick="updateQty(<?php echo $p_id; ?>, -1)"><i class="fas fa-minus"></i></button>
                            <input type="number" class="qty-input" value="<?php echo $qty; ?>" min="1" readonly>
                            <button type="button" class="qty-btn" onclick="updateQty(<?php echo $p_id; ?>, 1)"><i class="fas fa-plus"></i></button>
                        </div>
                        <p class="item-total">₹<span class="item-subtotal-val"><?php echo number_format($item_total, 2); ?></span></p>
                        
                        <button type="button" class="btn-text text-danger" onclick="removeCartItem(<?php echo $p_id; ?>)" style="font-size: 0.8rem; background:none; border:none; color: #ef4444; cursor:pointer;">Remove</button>
                    </div>
                </div>
                <?php endif; endforeach; 
                } // End if not empty
                
                if ($cart_empty): ?>
                    <div class="no-results" style="display:block; text-align:center; padding: 2rem;">
                         <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ddd; margin-bottom: 1rem;"></i>
                         <p>Your cart is empty.</p>
                         <a href="products" class="btn" style="margin-top: 1rem;">Start Shopping</a>
                    </div>
                <?php else: ?>
                    <!-- Add More Items Button (Left Side) -->
                    <a href="products" class="btn-add-more">
                        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Add More Items
                    </a>
                <?php endif; 
                
                // Extract variables for use in HTML below from controller-provided $cart_totals
                $subtotal = $cart_totals['subtotal']; 
                $shipping_cost = $cart_totals['shipping_cost'];
                $tax = $cart_totals['tax'];
                $total = $cart_totals['total'];

                $smart_discount = $cart_totals['smart_discount'];
                $reason = $cart_totals['discount_reason'];
                $shipping_options = $cart_totals['shipping_options'];
                $promo_discount = $cart_totals['promo_discount'];
                $promo_message = $cart_totals['promo_message'];
                $applied_promo_code = $cart_totals['promo_code'];
                $shipping_method = $cart_totals['selected_method'];

                ?>
            </div>
            
            <?php if (!$cart_empty): ?>
            <!-- Cart Summary -->
            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-item">
                    <span id="summary-price-label">Price (<?php echo $cart_totals['item_count']; ?> items)</span>
                    <span id="summary-subtotal">₹<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <?php if($smart_discount > 0): ?>
                <div class="summary-item" id="row-smart-discount">
                    <span class="smart-label">
                        Smart Discount 
                        <div class="tooltip-container">
                            <i class="fas fa-info-circle info-icon"></i>
                            <span class="tooltip-text" id="tooltip-text">
                                <?php echo !empty($reason) ? $reason : 'Discount Applied'; ?>
                            </span>
                        </div>
                    </span>
                    <span id="summary-smart-discount" style="color: var(--success, #16a34a);">-₹<?php echo number_format($smart_discount, 2); ?></span>
                </div>
                <?php else: ?>
                <div class="summary-item" id="row-smart-discount" style="display:none;">
                    <span class="smart-label">
                        Smart Discount
                        <div class="tooltip-container">
                            <i class="fas fa-info-circle info-icon"></i>
                            <span class="tooltip-text" id="tooltip-text"></span>
                        </div>
                    </span>
                    <span id="summary-smart-discount" style="color: var(--success, #16a34a);">-₹0</span>
                </div>
                <?php endif; ?>

                <?php if($promo_discount > 0): ?>
                <div class="summary-item" id="row-promo-discount">
                    <span class="promo-label" style="color: var(--primary);">
                        <?php echo $promo_message; ?>
                    </span>
                    <span id="summary-promo-discount" style="color: var(--success, #16a34a);">-₹<?php echo number_format($promo_discount, 2); ?></span>
                </div>
                <?php else: ?>
                 <div class="summary-item" id="row-promo-discount" style="display:none;">
                    <span class="promo-label" style="color: var(--primary);">Promo Discount</span>
                    <span id="summary-promo-discount" style="color: var(--success, #16a34a);">-₹0.00</span>
                </div>
                <?php endif; ?>

                <div class="summary-item">
                    <span>Shipping</span>
                    <span id="summary-shipping">₹<?php echo number_format($shipping_cost, 2); ?></span>
                </div>
                <div class="summary-item">
                    <span>Tax (18%)</span>
                    <span id="summary-tax">₹<?php echo number_format($tax, 2); ?></span>
                </div>
                
                <div class="shipping-methods">
                    <div class="shipping-header" onclick="toggleShipping()">
                        <h4>Shipping Method</h4>
                        <i class="fas fa-chevron-down" id="shipping-chevron"></i>
                    </div>
                    <div class="shipping-options" id="shipping-options-container" style="display: none;">
                        <!-- Standard Shipping (Only for <= 300) -->
                        <?php $standard_disabled = ($subtotal > 300); ?>
                        <label class="shipping-option <?php echo $standard_disabled ? 'disabled-option' : ''; ?>"
                               style="<?php echo $standard_disabled ? 'opacity: 0.5; pointer-events: none;' : ''; ?>">
                            <input type="radio" name="shipping_method" value="standard" 
                                <?php echo ($shipping_method === 'standard') ? 'checked' : ''; ?> 
                                <?php echo $standard_disabled ? 'disabled' : ''; ?>
                                onchange="updateShipping('standard')">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">Standard Shipping</span>
                                <span class="shipping-option-desc">Flat Rate Delivery</span>
                            </div>
                            <span class="shipping-option-price">₹40</span>
                        </label>
                        
                        <!-- Express Shipping (Only for <= 300) -->
                        <?php $express_disabled = ($subtotal > 300); ?>
                        <label class="shipping-option <?php echo $express_disabled ? 'disabled-option' : ''; ?>" 
                               style="<?php echo $express_disabled ? 'opacity: 0.5; pointer-events: none;' : ''; ?>">
                            <input type="radio" name="shipping_method" value="express" 
                                <?php echo ($shipping_method === 'express') ? 'checked' : ''; ?> 
                                <?php echo $express_disabled ? 'disabled' : ''; ?>
                                onchange="updateShipping('express')">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">Express Shipping</span>
                                <span class="shipping-option-desc">₹80 or 10% (Lowest)</span>
                            </div>
                            <span class="shipping-option-price">₹<?php echo number_format($shipping_options['express'], 2); ?></span>
                        </label>
                        
                        <!-- White Glove Delivery (Only for > 300) -->
                        <?php $white_glove_disabled = ($subtotal <= 300); ?>
                        <label class="shipping-option <?php echo $white_glove_disabled ? 'disabled-option' : ''; ?>"
                               style="<?php echo $white_glove_disabled ? 'opacity: 0.5; pointer-events: none;' : ''; ?>">
                            <input type="radio" name="shipping_method" value="white_glove" 
                                <?php echo ($shipping_method === 'white_glove') ? 'checked' : ''; ?> 
                                <?php echo $white_glove_disabled ? 'disabled' : ''; ?>
                                onchange="updateShipping('white_glove')">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">White Glove Delivery</span>
                                <span class="shipping-option-desc">₹150 or 5% (Lowest)</span>
                            </div>
                            <span class="shipping-option-price">₹<?php echo number_format($shipping_options['white_glove'], 2); ?></span>
                        </label>

                        <!-- Freight Shipping (Only for > 300) -->
                        <?php $freight_disabled = ($subtotal <= 300); ?>
                        <label class="shipping-option <?php echo $freight_disabled ? 'disabled-option' : ''; ?>"
                               style="<?php echo $freight_disabled ? 'opacity: 0.5; pointer-events: none;' : ''; ?>">
                            <input type="radio" name="shipping_method" value="freight" 
                                <?php echo ($shipping_method === 'freight') ? 'checked' : ''; ?> 
                                <?php echo $freight_disabled ? 'disabled' : ''; ?>
                                onchange="updateShipping('freight')">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">Freight Shipping</span>
                                <span class="shipping-option-desc">3% or Min ₹250</span>
                            </div>
                            <span class="shipping-option-price">₹<?php echo number_format($shipping_options['freight'], 2); ?></span>
                        </label>
                    </div>
                </div>

                <div class="promo-code-section">
                    <label style="font-size: 0.9rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; display: block;">Promo Code</label>
                    <div class="promo-input-group">
                        <input type="text" id="promo-code" placeholder="Enter promo code" value="<?php echo $applied_promo_code; ?>">
                        <button type="button" class="btn-apply" onclick="applyPromo()">Apply</button>
                    </div>
                    <div id="promo-message" style="display:<?php echo $promo_message ? 'block' : 'none'; ?>; font-size: 0.85rem; margin-top: 0.5rem; color: var(--success);">
                        <?php echo $promo_message; ?>
                    </div>
                </div>

                <hr>
                <div class="summary-total">
                    <span>Total</span>
                    <span id="summary-total">₹<?php echo number_format($total, 2); ?></span>
                </div>
                
                <a href="checkout" class="checkout-btn" style="text-align: center; text-decoration: none;">Proceed to Checkout</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?php echo $base_path; ?>js/cart.js?v=<?php echo time(); ?>"></script>
