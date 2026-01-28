<?php
session_start();
$title = "Shopping Cart - EasyCart";
$base_path = "../";
$page = "cart";
$extra_css = "cart.css";
include '../data/products_data.php';


// --- CART LOGIC ---

// 1. Initialize Cart if empty
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/**
 * Centralized Cart Calculation Logic
 * Calculates Subtotal, Shipping, Tax, and Total based on current cart state.
 */
function calculateCartTotals($cart_items, $products_data) {
    $subtotal = 0;
    
    // Calculate Subtotal
    foreach($cart_items as $p_id => $qty) {
        foreach($products_data as $p) {
            if($p['id'] == $p_id) {
                // Remove commas from price string (e.g. "1,200" -> 1200)
                $price_val = (float)str_replace(',', '', $p['price']);
                $subtotal += $price_val * $qty;
                break;
            }
        }
    }

    // Shipping Rules
    $shipping_options = [
        'standard' => 40,
        'express' => min(80, $subtotal * 0.10),
        'white_glove' => min(150, $subtotal * 0.05),
        'freight' => max(200, $subtotal * 0.03)
    ];

    // Determine Selected Shipping Cost
    $selected_method = isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : 'standard';
    // Default to standard if method invalid, but logic below handles it via array lookup or fallback
    $shipping_cost = isset($shipping_options[$selected_method]) ? $shipping_options[$selected_method] : 40;
    
    // Empty cart checks
    if (empty($cart_items)) {
        $shipping_cost = 0;
        $shipping_options = array_map(function() { return 0; }, $shipping_options);
    }

    // Tax (18%)
    $tax = ($subtotal + $shipping_cost) * 0.18;
    $total = $subtotal + $shipping_cost + $tax;

    return [
        'subtotal' => $subtotal,
        'shipping_cost' => $shipping_cost,
        'tax' => $tax,
        'total' => $total,
        'shipping_options' => $shipping_options,
        'item_count' => array_sum($cart_items)
    ];
}

// 2. Handle Actions (Update Quantity / Remove)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $p_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if ($_POST['action'] === 'update_qty' && $p_id > 0) {
        if (isset($_POST['qty'])) {
            $_SESSION['cart'][$p_id] = (int)$_POST['qty'];
        } elseif (isset($_POST['change'])) {
            $change = (int)$_POST['change'];
            if (!isset($_SESSION['cart'][$p_id])) {
                $_SESSION['cart'][$p_id] = 0;
            }
            $_SESSION['cart'][$p_id] += $change;
        }
        
        if (isset($_SESSION['cart'][$p_id]) && $_SESSION['cart'][$p_id] <= 0) {
            unset($_SESSION['cart'][$p_id]);
        }
    } elseif ($_POST['action'] === 'remove' && $p_id > 0) {
        if (isset($_SESSION['cart'][$p_id])) {
            unset($_SESSION['cart'][$p_id]);
        }
    } elseif ($_POST['action'] === 'set_shipping' && isset($_POST['method'])) {
        $_SESSION['shipping_method'] = $_POST['method'];
    } elseif ($_POST['action'] === 'clear') {
        $_SESSION['cart'] = [];
    }



    /*
     * WHY WE USE AJAX VS PAGE RELOAD?
     * -------------------------------------------------------------------------
     * Feature      | Without AJAX (Old School)         | With AJAX (Modern)
     * -------------------------------------------------------------------------
     * Visuals      | Screen flashes white on update    | Smooth, instant updates
     * Speed        | Slow (re-downloads CSS/Images)    | Fast (only sends JSON)
     * Scroll       | Jumps to top after every click    | Stays exactly where you are
     * -------------------------------------------------------------------------
     * 
     * HOW IT WORKS:
     * 1. Page Load: PHP renders full HTML with initial values.
     * 2. User Action: JS sends hidden background request (AJAX).
     * 3. PHP: Detects this flag, calculates new totals, and sends ONLY JSON.
     * 4. JS: Receives JSON and updates specific numbers in the DOM.
     */

    // Check for AJAX/Fetch request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        
        // Use the centralized function
        $totals = calculateCartTotals($_SESSION['cart'], $products);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'summary' => [
                'subtotal' => $totals['subtotal'],
                'shipping' => $totals['shipping_cost'], // JS expects 'shipping'
                'tax' => $totals['tax'],
                'total' => $totals['total'],
                'count' => $totals['item_count'],
                'shipping_options' => $totals['shipping_options']
            ]
        ]);
        exit;
    }
    
    // Redirect for standard form submissions
    header("Location: cart.php");
    exit;
}

include '../includes/header.php';
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
                $subtotal = 0;
                $shipping = 0; 
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
                            $subtotal += $item_total;
                ?>
                <div class="cart-item" data-id="<?php echo $p_id; ?>" data-price="<?php echo $price_val; ?>">
                    <div class="item-visual">
                        <img src="../images/<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>" class="item-img">
                    </div>
                    <div class="item-details">
                        <h3><?php echo $item['title']; ?></h3>
                        <p class="item-category"><?php echo ucfirst($item['category']); ?></p>
                        <h4 class="item-price">₹<?php echo $item['price']; ?></h4>
                    </div>
                    <div class="item-actions">
                        <div class="quantity-control">
                            <button type="button" class="qty-btn" onclick="updateQty(<?php echo $p_id; ?>, -1)"><i class="fas fa-minus"></i></button>
                            <input type="number" class="qty-input" value="<?php echo $qty; ?>" min="1" readonly>
                            <button type="button" class="qty-btn" onclick="updateQty(<?php echo $p_id; ?>, 1)"><i class="fas fa-plus"></i></button>
                        </div>
                        <p class="item-total">₹<span class="item-subtotal-val"><?php echo number_format($item_total); ?></span></p>
                        
                        <button type="button" class="btn-text text-danger" onclick="removeCartItem(<?php echo $p_id; ?>)" style="font-size: 0.8rem; background:none; border:none; color: #ef4444; cursor:pointer;">Remove</button>
                    </div>
                </div>
                <?php endif; endforeach; 
                } // End if not empty
                
                if ($cart_empty): ?>
                    <div class="no-results" style="display:block; text-align:center; padding: 2rem;">
                         <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ddd; margin-bottom: 1rem;"></i>
                         <p>Your cart is empty.</p>
                         <a href="products.php" class="btn" style="margin-top: 1rem;">Start Shopping</a>
                    </div>
                <?php else: ?>
                    <!-- Add More Items Button (Left Side) -->
                    <a href="products.php" class="btn-add-more">
                        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Add More Items
                    </a>
                <?php endif; 
                

                
                // --- Phase 4: Shipping & Tax Logic (Rupee Edition) ---
                // REFACTORED: Now using the centralized function
                $cart_totals = calculateCartTotals($_SESSION['cart'], $products);
                
                // Extract variables for use in HTML below
                $subtotal = $cart_totals['subtotal']; // Note: This overwrites the loop subtotal, which is safer
                $shipping_cost = $cart_totals['shipping_cost'];
                $tax = $cart_totals['tax'];
                $total = $cart_totals['total'];
                $shipping_options = $cart_totals['shipping_options'];
                $shipping_method = isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : 'standard';
                ?>
            </div>
            
            <?php if (!$cart_empty): ?>
            <!-- Cart Summary -->
            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-item">
                    <span>Subtotal</span>
                    <span id="summary-subtotal">₹<?php echo number_format($subtotal); ?></span>
                </div>
                <div class="summary-item">
                    <span>Shipping</span>
                    <span id="summary-shipping">₹<?php echo number_format($shipping_cost); ?></span>
                </div>
                <div class="summary-item">
                    <span>Tax (18%)</span>
                    <span id="summary-tax">₹<?php echo number_format($tax); ?></span>
                </div>
                
                <div class="shipping-methods">
                    <h4>Shipping Method</h4>
                    <div class="shipping-options">
                        <!-- Standard Shipping -->
                        <label class="shipping-option <?php echo ($shipping_method === 'standard') ? 'selected' : ''; ?>">
                            <input type="radio" name="shipping_method" value="standard" <?php echo ($shipping_method === 'standard') ? 'checked' : ''; ?> onchange="updateShipping('standard')">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">Standard Shipping</span>
                                <span class="shipping-option-desc">Flat Rate Delivery</span>
                            </div>
                            <span class="shipping-option-price">₹40</span>
                        </label>
                        
                        <!-- Express Shipping -->
                        <label class="shipping-option <?php echo ($shipping_method === 'express') ? 'selected' : ''; ?>">
                            <input type="radio" name="shipping_method" value="express" <?php echo ($shipping_method === 'express') ? 'checked' : ''; ?> onchange="updateShipping('express')">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">Express Shipping</span>
                                <span class="shipping-option-desc">₹80 or 10% (Lowest)</span>
                            </div>
                            <span class="shipping-option-price">₹<?php echo number_format($shipping_options['express']); ?></span>
                        </label>
                        
                        <!-- White Glove Delivery -->
                        <label class="shipping-option <?php echo ($shipping_method === 'white_glove') ? 'selected' : ''; ?>">
                            <input type="radio" name="shipping_method" value="white_glove" <?php echo ($shipping_method === 'white_glove') ? 'checked' : ''; ?> onchange="updateShipping('white_glove')">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">White Glove Delivery</span>
                                <span class="shipping-option-desc">₹150 or 5% (Lowest)</span>
                            </div>
                            <span class="shipping-option-price">₹<?php echo number_format($shipping_options['white_glove']); ?></span>
                        </label>

                        <!-- Freight Shipping -->
                        <label class="shipping-option <?php echo ($shipping_method === 'freight') ? 'selected' : ''; ?>">
                            <input type="radio" name="shipping_method" value="freight" <?php echo ($shipping_method === 'freight') ? 'checked' : ''; ?> onchange="updateShipping('freight')">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">Freight Shipping</span>
                                <span class="shipping-option-desc">3% or Min ₹200</span>
                            </div>
                            <span class="shipping-option-price">₹<?php echo number_format($shipping_options['freight']); ?></span>
                        </label>
                    </div>
                </div>

                <hr>
                <div class="summary-total">
                    <span>Total</span>
                    <span id="summary-total">₹<?php echo number_format($total); ?></span>
                </div>
                
                <a href="#checkout-modal" class="checkout-btn" style="text-align: center; text-decoration: none;">Proceed to Checkout</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Checkout Modal (Pure CSS Target) -->
    <div id="checkout-modal" class="modal-overlay">
        <a href="#" class="modal-close-area"></a>
        <div class="modal-content">
            <div class="modal-header">
                <h2>Checkout</h2>
                <a href="#" class="close-btn">&times;</a>
            </div>
            <div class="modal-body">
                <form action="#" class="checkout-form" id="checkoutForm">
                    <div class="form-section">
                        <h4>Contact Info</h4>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" id="checkoutName" placeholder="John Doe" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" id="checkoutEmail" placeholder="john@example.com" required>
                        </div>
                    </div>
                    
                    <div class="form-section address-highlight">
                        <h4>Shipping Address</h4>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" id="checkoutAddress" placeholder="123 Street Name" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" id="checkoutCity" placeholder="City" required>
                            </div>
                            <div class="form-group">
                                <label>Postal Code</label>
                                <input type="text" id="checkoutZip" placeholder="000000" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Payment</h4>
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" id="checkoutCard" placeholder="0000 0000 0000 0000" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Expiry</label>
                                <input type="text" id="checkoutExpiry" placeholder="MM/YY" required>
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="text" id="checkoutCVV" placeholder="123" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-block" id="checkout-btn-text">Place Order (₹<?php echo number_format($total); ?>)</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/cart.js"></script>


<?php include '../includes/footer.php'; ?>