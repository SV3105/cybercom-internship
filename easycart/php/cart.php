<?php
session_start();
$title = "Shopping Cart - EasyCart";
$base_path = "../";
$page = "cart";
$extra_css = "cart.css";
include '../includes/products_data.php';

// --- CART LOGIC ---

// 1. Initialize Cart if empty
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
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
    }

    // Check for AJAX/Fetch request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    
    // Redirect for standard form submissions
    header("Location: cart.php");
    exit;
}

include '../includes/header.php';
?>

    <div class="container">
        <h1 class="page-title">Shopping Cart</h1>
        
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
                
                $shipping = ($subtotal > 500 || $subtotal == 0) ? 0 : 50;
                $tax = $subtotal * 0.18; // 18% Tax
                $total = $subtotal + $tax + $shipping;
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
                    <span id="summary-shipping"><?php echo $shipping == 0 ? 'Free' : '₹'.number_format($shipping); ?></span>
                </div>
                <div class="summary-item">
                    <span>Tax (18%)</span>
                    <span id="summary-tax">₹<?php echo number_format($tax); ?></span>
                </div>
                
                <div class="shipping-methods">
                    <h4>Shipping Method</h4>
                    <div class="shipping-options">
                        <label class="shipping-option <?php echo ($subtotal >= 500) ? 'selected' : ''; ?>" id="label-free">
                            <input type="radio" name="shipping_method" value="0" <?php echo ($subtotal >= 500) ? 'checked' : 'disabled'; ?> onchange="updateSummary()">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">Free Delivery</span>
                                <span class="shipping-option-desc"><?php echo ($subtotal >= 500) ? 'Eligible for free shipping' : 'Spend ₹' . (500 - $subtotal) . ' more for free shipping'; ?></span>
                            </div>
                            <span class="shipping-option-price free-highlight">Free</span>
                        </label>
                        
                        <label class="shipping-option <?php echo ($subtotal < 500) ? 'selected' : ''; ?>" id="label-normal">
                            <input type="radio" name="shipping_method" value="50" <?php echo ($subtotal < 500) ? 'checked' : ''; ?> onchange="updateSummary()">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">Normal Delivery</span>
                                <span class="shipping-option-desc">3-5 business days</span>
                            </div>
                            <span class="shipping-option-price">₹50</span>
                        </label>
                        
                        <label class="shipping-option" id="label-fast">
                            <input type="radio" name="shipping_method" value="150" onchange="updateSummary()">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">Faster Delivery</span>
                                <span class="shipping-option-desc">1-2 business days</span>
                            </div>
                            <span class="shipping-option-price">₹150</span>
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

    <script>
        // --- Cart Interactions ---
        function updateQty(productId, change) {
            const itemRow = document.querySelector(`.cart-item[data-id="${productId}"]`);
            if (!itemRow) return;

            const qtyInput = itemRow.querySelector('.qty-input');
            const price = parseFloat(itemRow.dataset.price);
            let currentQty = parseInt(qtyInput.value);
            
            let newQty = currentQty + change;
            if (newQty < 1) return;

            // Sync with session
            const formData = new FormData();
            formData.append('action', 'update_qty');
            formData.append('product_id', productId);
            formData.append('qty', newQty);

            fetch('cart.php', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    qtyInput.value = newQty;
                    // Update item subtotal in UI
                    const subtotalSpan = itemRow.querySelector('.item-subtotal-val');
                    const newSubtotal = price * newQty;
                    subtotalSpan.textContent = new Intl.NumberFormat('en-IN').format(Math.round(newSubtotal));
                    updateSummary();
                }
            })
            .catch(err => console.error('Error updating quantity:', err));
        }

        function removeCartItem(productId) {
            if (!confirm('Are you sure you want to remove this item?')) return;
            const itemRow = document.querySelector(`.cart-item[data-id="${productId}"]`);
            if (!itemRow) return;

            // Sync with session
            const formData = new FormData();
            formData.append('action', 'remove');
            formData.append('product_id', productId);

            fetch('cart.php', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    itemRow.classList.add('removing');
                    setTimeout(() => {
                        itemRow.remove();
                        updateSummary();
                        
                        // If cart is empty now
                        const remainingItems = document.querySelectorAll('.cart-item');
                        if (remainingItems.length === 0) {
                            location.reload(); 
                        }
                    }, 400); // Wait for CSS transition
                }
            })
            .catch(err => console.error('Error removing item:', err));
        }

        function updateSummary() {
            let subtotal = 0;
            const items = document.querySelectorAll('.cart-item');
            
            items.forEach(item => {
                const price = parseFloat(item.dataset.price);
                const qty = parseInt(item.querySelector('.qty-input').value);
                subtotal += price * qty;
            });

            // Update Shipping Options UI availability
            const freeOption = document.querySelector('input[name="shipping_method"][value="0"]');
            const freeLabel = document.getElementById('label-free');
            const freeDesc = freeLabel.querySelector('.shipping-option-desc');
            
            if (subtotal >= 500) {
                freeOption.disabled = false;
                freeDesc.textContent = 'Eligible for free shipping';
                freeLabel.classList.remove('unavailable');
            } else {
                if (freeOption.checked) {
                    document.querySelector('input[name="shipping_method"][value="50"]').checked = true;
                }
                freeOption.disabled = true;
                freeDesc.textContent = 'Spend ₹' + (500 - subtotal) + ' more for free shipping';
                freeLabel.classList.add('unavailable');
            }

            // Get selected shipping
            const selectedShipping = document.querySelector('input[name="shipping_method"]:checked');
            const shipping = selectedShipping ? parseInt(selectedShipping.value) : 0;
            
            // Update selected class on labels
            document.querySelectorAll('.shipping-option').forEach(label => {
                label.classList.remove('selected');
                if (label.querySelector('input').checked) {
                    label.classList.add('selected');
                }
            });

            const tax = subtotal * 0.18;
            const total = subtotal + tax + shipping;

            // Update Summary UI
            document.getElementById('summary-subtotal').textContent = '₹' + new Intl.NumberFormat('en-IN').format(Math.round(subtotal));
            
            const shippingElem = document.getElementById('summary-shipping');
            shippingElem.textContent = (shipping === 0) ? 'Free' : '₹' + shipping;
            if (shipping === 0) {
                shippingElem.style.color = '#166534';
                shippingElem.style.fontWeight = '700';
            } else {
                shippingElem.style.color = 'inherit';
                shippingElem.style.fontWeight = 'inherit';
            }
            
            document.getElementById('summary-tax').textContent = '₹' + new Intl.NumberFormat('en-IN').format(Math.round(tax));
            document.getElementById('summary-total').textContent = '₹' + new Intl.NumberFormat('en-IN').format(Math.round(total));
            
            // Update Checkout Button Text
            const checkoutBtn = document.getElementById('checkout-btn-text');
            if (checkoutBtn) {
                checkoutBtn.textContent = 'Place Order (₹' + new Intl.NumberFormat('en-IN').format(Math.round(total)) + ')';
            }
        }

        // --- Checkout Validation ---
        const checkoutForm = document.getElementById('checkoutForm');

        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const name = document.getElementById('checkoutName').value.trim();
                const email = document.getElementById('checkoutEmail').value.trim();
                const address = document.getElementById('checkoutAddress').value.trim();
                const city = document.getElementById('checkoutCity').value.trim();
                const zip = document.getElementById('checkoutZip').value.trim();
                const card = document.getElementById('checkoutCard').value.replace(/\s/g, '');
                const expiry = document.getElementById('checkoutExpiry').value.trim();
                const cvv = document.getElementById('checkoutCVV').value.trim();

                // Validation rules
                if (name.length < 2) {
                    alert('Please enter your full name.');
                    return;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert('Please enter a valid email address.');
                    return;
                }

                if (address.length < 5) {
                    alert('Please enter a valid shipping address.');
                    return;
                }

                if (city.length < 2) {
                    alert('Please enter your city.');
                    return;
                }

                if (!/^\d{6}$/.test(zip)) {
                    alert('Postal code must be 6 digits.');
                    return;
                }

                if (!/^\d{16}$/.test(card)) {
                    alert('Card number must be 16 digits.');
                    return;
                }

                if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
                    alert('Expiry must be in MM/YY format.');
                    return;
                }

                if (!/^\d{3}$/.test(cvv)) {
                    alert('CVV must be 3 digits.');
                    return;
                }

                alert('Order placed successfully! Redirecting to orders...');
                window.location.href = 'orders.php';
            });
        }
    </script>

<?php include '../includes/footer.php'; ?>