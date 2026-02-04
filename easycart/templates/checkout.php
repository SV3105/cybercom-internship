
<div class="checkout-container">
    <!-- Progress Steps -->
    <div class="checkout-progress">
        <div class="progress-step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Delivery Address</div>
        </div>
        <div class="progress-line"></div>
        <div class="progress-step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-label">Payment Method</div>
        </div>
        <div class="progress-line"></div>
        <div class="progress-step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-label">Review Order</div>
        </div>
    </div>

    <div class="checkout-content">
        <!-- Left: Form Steps -->
        <div class="checkout-form-container">
            
            <!-- Step 1: Address -->
            <div class="checkout-step" id="step-1">
                <h2>Delivery Address</h2>
                <form id="addressForm" class="checkout-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name <span class="required">*</span></label>
                            <input type="text" name="firstname" id="firstname" value="<?php echo htmlspecialchars($prefill['firstname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name <span class="required">*</span></label>
                            <input type="text" name="lastname" id="lastname" value="<?php echo htmlspecialchars($prefill['lastname']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($prefill['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Phone Number <span class="required">*</span></label>
                        <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($prefill['phone']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Street Address <span class="required">*</span></label>
                        <input type="text" name="street" id="street" placeholder="House No., Building Name, Street" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>City <span class="required">*</span></label>
                            <input type="text" name="city" id="city" value="<?php echo htmlspecialchars($prefill['city']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Postal Code <span class="required">*</span></label>
                            <input type="text" name="postcode" id="postcode" placeholder="000000" required>
                        </div>
                    </div>

                    <button type="button" class="btn btn-next" onclick="nextStep(2)">
                        Continue to Payment <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>

            <!-- Step 2: Payment -->
            <div class="checkout-step" id="step-2" style="display: none;">
                <h2>Payment Method</h2>
                <form id="paymentForm" class="checkout-form">
                    <div class="payment-methods">
                        <!-- COD -->
                        <div class="payment-method-group">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cash" checked onchange="togglePaymentDetails(this)">
                                <div class="payment-card">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <div>
                                        <strong>Cash on Delivery</strong>
                                        <p>Pay when you receive</p>
                                    </div>
                                </div>
                            </label>
                            <div class="payment-details" id="details-cash" style="display: block;">
                                <p class="info-text"><i class="fas fa-info-circle"></i> Pay cash to the delivery agent upon receiving your order.</p>
                            </div>
                        </div>

                        <!-- Card -->
                        <div class="payment-method-group">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="card" onchange="togglePaymentDetails(this)">
                                <div class="payment-card">
                                    <i class="fas fa-credit-card"></i>
                                    <div>
                                        <strong>Credit / Debit Card</strong>
                                        <p>Visa, Mastercard, Rupay</p>
                                    </div>
                                </div>
                            </label>
                            <div class="payment-details" id="details-card" style="display: none;">
                                <div class="form-group">
                                    <label>Card Number</label>
                                    <div class="input-icon-wrapper">
                                        <i class="far fa-credit-card"></i>
                                        <input type="text" placeholder="0000 0000 0000 0000" maxlength="19">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Expiry Date</label>
                                        <div class="input-icon-wrapper">
                                            <i class="far fa-calendar-alt"></i>
                                            <input type="text" placeholder="MM/YY" maxlength="5">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>CVV</label>
                                        <div class="input-icon-wrapper">
                                            <i class="fas fa-lock"></i>
                                            <input type="password" placeholder="123" maxlength="3">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Cardholder Name</label>
                                    <div class="input-icon-wrapper">
                                        <i class="far fa-user"></i>
                                        <input type="text" placeholder="Name as on card">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="step-actions">
                        <button type="button" class="btn btn-back" onclick="prevStep(1)">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn btn-next" onclick="nextStep(3)">
                            Review Order <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 3: Review -->
            <div class="checkout-step" id="step-3" style="display: none;">
                <h2>Review Your Order</h2>
                
                <div class="review-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Delivery Address</h3>
                    <div class="review-card" id="review-address">
                        <!-- Filled by JS -->
                    </div>
                    <button type="button" class="btn-edit" onclick="prevStep(1)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>

                <div class="review-section">
                    <h3><i class="fas fa-wallet"></i> Payment Method</h3>
                    <div class="review-card" id="review-payment">
                        <!-- Filled by JS -->
                    </div>
                    <button type="button" class="btn-edit" onclick="prevStep(2)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>

                <div class="step-actions">
                    <button type="button" class="btn btn-back" onclick="prevStep(2)">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-place-order" id="placeOrderBtn" onclick="placeOrder()">
                        <i class="fas fa-check-circle"></i> Place Order (₹<?php echo number_format($total, 2); ?>)
                    </button>
                </div>
            </div>
        </div>

        <!-- Right: Order Summary -->
        <div class="order-summary-sidebar">
            <div class="summary-sticky">
                <h3>Order Summary</h3>
                
                <div class="summary-items">
                    <?php foreach($cart_items as $p_id => $qty):
                        $product = null;
                        foreach($products as $p) {
                            if($p['id'] == $p_id) {
                                $product = $p;
                                break;
                            }
                        }
                        if ($product):
                            $price = (float)str_replace(',', '', $product['price']);
                    ?>
                    <div class="summary-item">
                        <img src="../images/<?php echo $product['image']; ?>" alt="<?php echo $product['title']; ?>">
                        <div class="item-details">
                            <p class="item-name"><?php echo $product['title']; ?></p>
                            <p class="item-qty">Qty: <?php echo $qty; ?></p>
                        </div>
                        <p class="item-price">₹<?php echo number_format($price * $qty, 2); ?></p>
                    </div>
                    <?php endif; endforeach; ?>
                </div>

                <div class="summary-totals">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>₹<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <?php if ($smart_discount > 0): ?>
                    <div class="summary-row discount">
                        <span>Smart Discount</span>
                        <span>-₹<?php echo number_format($smart_discount, 2); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>₹<?php echo number_format($shipping_cost, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (GST 18%)</span>
                        <span>₹<?php echo number_format($tax, 2); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>₹<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../js/checkout.js?v=<?php echo time(); ?>"></script>
