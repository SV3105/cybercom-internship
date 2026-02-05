
<div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
    <!-- Back Button -->
    <div style="margin-bottom: 1.5rem;">
        <a href="orders" style="color: #4f46e5; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 500;">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    <!-- Order Header -->
    <div style="background: white; border-radius: 12px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="margin: 0 0 0.5rem; font-size: 1.75rem; color: #1f2937;">Order #<?php echo $order['id']; ?></h1>
                <p style="margin: 0; color: #6b7280; font-size: 0.95rem;">
                    <i class="fas fa-calendar"></i> Placed on <?php echo $order['date']; ?>
                </p>
            </div>
            <div style="text-align: right;">
                <span style="display: inline-block; padding: 0.5rem 1rem; border-radius: 25px; font-weight: 600; font-size: 0.9rem;
                    background: <?php 
                        echo match($order['status_code']) {
                            'delivered' => '#dcfce7',
                            'transit' => '#dbeafe',
                            'processing' => '#fef3c7',
                            default => '#f3f4f6'
                        };
                    ?>; 
                    color: <?php 
                        echo match($order['status_code']) {
                            'delivered' => '#166534',
                            'transit' => '#1e40af',
                            'processing' => '#92400e',
                            default => '#4b5563'
                        };
                    ?>;">
                    <?php echo $order['status']; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Order Status Timeline -->
    <div style="background: white; border-radius: 12px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h2 style="margin: 0 0 1.5rem; font-size: 1.25rem; color: #1f2937;">Order Status</h2>
        <div style="display: flex; justify-content: space-between; position: relative; padding: 0 1rem;">
            <!-- Progress Line -->
            <div style="position: absolute; top: 20px; left: 1rem; right: 1rem; height: 3px; background: #e5e7eb; z-index: 0;"></div>
            <div style="position: absolute; top: 20px; left: 1rem; width: <?php echo ($order['current_step'] - 1) * 33.33; ?>%; height: 3px; background: #4f46e5; z-index: 0; transition: width 0.3s;"></div>
            
            <!-- Step 1: Pending -->
            <div style="flex: 1; text-align: center; position: relative; z-index: 1;">
                <div style="width: 40px; height: 40px; border-radius: 50%; margin: 0 auto 0.75rem; display: flex; align-items: center; justify-content: center; font-weight: 600;
                    background: <?php echo $order['current_step'] >= 1 ? '#4f46e5' : '#e5e7eb'; ?>;
                    color: <?php echo $order['current_step'] >= 1 ? 'white' : '#9ca3af'; ?>;">
                    <i class="fas fa-check"></i>
                </div>
                <p style="margin: 0; font-size: 0.85rem; font-weight: 500; color: <?php echo $order['current_step'] >= 1 ? '#1f2937' : '#9ca3af'; ?>;">Confirmed</p>
            </div>
            
            <!-- Step 2: Processing -->
            <div style="flex: 1; text-align: center; position: relative; z-index: 1;">
                <div style="width: 40px; height: 40px; border-radius: 50%; margin: 0 auto 0.75rem; display: flex; align-items: center; justify-content: center; font-weight: 600;
                    background: <?php echo $order['current_step'] >= 2 ? '#4f46e5' : '#e5e7eb'; ?>;
                    color: <?php echo $order['current_step'] >= 2 ? 'white' : '#9ca3af'; ?>;">
                    <i class="fas fa-box"></i>
                </div>
                <p style="margin: 0; font-size: 0.85rem; font-weight: 500; color: <?php echo $order['current_step'] >= 2 ? '#1f2937' : '#9ca3af'; ?>;">Processing</p>
            </div>
            
            <!-- Step 3: In Transit -->
            <div style="flex: 1; text-align: center; position: relative; z-index: 1;">
                <div style="width: 40px; height: 40px; border-radius: 50%; margin: 0 auto 0.75rem; display: flex; align-items: center; justify-content: center; font-weight: 600;
                    background: <?php echo $order['current_step'] >= 3 ? '#4f46e5' : '#e5e7eb'; ?>;
                    color: <?php echo $order['current_step'] >= 3 ? 'white' : '#9ca3af'; ?>;">
                    <i class="fas fa-truck"></i>
                </div>
                <p style="margin: 0; font-size: 0.85rem; font-weight: 500; color: <?php echo $order['current_step'] >= 3 ? '#1f2937' : '#9ca3af'; ?>;">In Transit</p>
            </div>
            
            <!-- Step 4: Delivered -->
            <div style="flex: 1; text-align: center; position: relative; z-index: 1;">
                <div style="width: 40px; height: 40px; border-radius: 50%; margin: 0 auto 0.75rem; display: flex; align-items: center; justify-content: center; font-weight: 600;
                    background: <?php echo $order['current_step'] >= 4 ? '#10b981' : '#e5e7eb'; ?>;
                    color: <?php echo $order['current_step'] >= 4 ? 'white' : '#9ca3af'; ?>;">
                    <i class="fas fa-home"></i>
                </div>
                <p style="margin: 0; font-size: 0.85rem; font-weight: 500; color: <?php echo $order['current_step'] >= 4 ? '#1f2937' : '#9ca3af'; ?>;">Delivered</p>
            </div>
        </div>
    </div>
    

    <div style="display: grid; grid-template-columns: 1fr 400px; gap: 1.5rem; align-items: start;">
        <!-- Left Column: Products -->
        <div>
            <!-- Products List -->
            <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h2 style="margin: 0 0 1.5rem; font-size: 1.25rem; color: #1f2937;">Order Items</h2>
                
                <?php foreach ($order['items'] as $item): ?>
                <div style="display: flex; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid #f3f4f6;">
                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" 
                         style="width: 80px; height: 80px; object-fit: contain; border-radius: 8px; border: 1px solid #e5e7eb; background: #f9fafb;">
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 0.25rem; font-size: 1rem; color: #1f2937;"><?php echo $item['name']; ?></h3>
                        <p style="margin: 0; color: #4b5563; font-size: 0.9rem;">Quantity: <strong><?php echo $item['qty']; ?></strong></p>
                    </div>
                    <div style="text-align: right;">
                        <p style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #1f2937;">₹<?php echo number_format((float)$item['total'], 2); ?></p>
                        <p style="margin: 0.25rem 0 0; color: #6b7280; font-size: 0.85rem;">₹<?php echo number_format((float)$item['price'], 2); ?> each</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right Column: Summary & Info -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Price Summary -->
            <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="margin: 0 0 1rem; font-size: 1.1rem; color: #1f2937;">Order Summary</h3>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: #6b7280;">Subtotal</span>
                    <span style="color: #1f2937; font-weight: 500;">₹<?php echo number_format($order['subtotal'], 2); ?></span>
                </div>
                
                <?php if ($order['discount'] > 0): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: #10b981;">Discount <?php echo $order['coupon'] ? '(' . $order['coupon'] . ')' : ''; ?></span>
                    <span style="color: #10b981; font-weight: 500;">-₹<?php echo number_format($order['discount'], 2); ?></span>
                </div>
                <?php endif; ?>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: #6b7280;">Shipping</span>
                    <span style="color: #1f2937; font-weight: 500;">₹<?php echo number_format($order['shipping'], 2); ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280;">Tax (18%)</span>
                    <span style="color: #1f2937; font-weight: 500;">₹<?php echo number_format($order['tax'], 2); ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; font-size: 1.15rem;">
                    <span style="color: #1f2937; font-weight: 700;">Total</span>
                    <span style="color: #4f46e5; font-weight: 700;">₹<?php echo number_format($order['total'], 2); ?></span>
                </div>
            </div>

            <!-- Shipping Address -->
            <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="margin: 0 0 1rem; font-size: 1.1rem; color: #1f2937;">
                    <i class="fas fa-map-marker-alt" style="color: #4f46e5;"></i> Shipping Address
                </h3>
                <p style="margin: 0 0 0.5rem; font-weight: 600; color: #1f2937;"><?php echo $order['shipping_address']['name']; ?></p>
                <p style="margin: 0 0 0.25rem; color: #6b7280; font-size: 0.9rem;"><?php echo $order['shipping_address']['street']; ?></p>
                <p style="margin: 0 0 0.5rem; color: #6b7280; font-size: 0.9rem;">
                    <?php echo $order['shipping_address']['city']; ?> - <?php echo $order['shipping_address']['postcode']; ?>
                </p>
                <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">
                    <i class="fas fa-phone"></i> <?php echo $order['shipping_address']['phone']; ?>
                </p>
            </div>

            <!-- Payment Info -->
            <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="margin: 0 0 1rem; font-size: 1.1rem; color: #1f2937;">
                    <i class="fas fa-credit-card" style="color: #4f46e5;"></i> Payment
                </h3>
                <p style="margin: 0 0 0.5rem; color: #6b7280; font-size: 0.9rem;">Method</p>
                <p style="margin: 0 0 1rem; font-weight: 600; color: #1f2937;"><?php echo $order['payment_method']; ?></p>
                <p style="margin: 0 0 0.25rem; color: #6b7280; font-size: 0.9rem;">Status</p>
                <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 15px; font-size: 0.85rem; font-weight: 600;
                    background: <?php echo $order['payment_status'] == 'Paid' ? '#dcfce7' : '#fef3c7'; ?>;
                    color: <?php echo $order['payment_status'] == 'Paid' ? '#166534' : '#92400e'; ?>;">
                    <?php echo $order['payment_status']; ?>
                </span>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="invoice?id=<?php echo $order['db_id']; ?>" target="_blank" 
                   style="display: block; text-align: center; padding: 0.875rem; background: #4f46e5; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.2s;">
                    <i class="fas fa-file-invoice"></i> Download Invoice
                </a>
                <?php if ($order['status_code'] != 'delivered'): ?>
                <button onclick="alert('Track your order with ID: #track-<?php echo $order['id']; ?>\nCurrent Status: <?php echo $order['status']; ?>')" 
                        style="padding: 0.875rem; background: white; color: #4f46e5; border: 2px solid #4f46e5; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                    <i class="fas fa-truck-fast"></i> Track Order
                </button>
                <?php endif; ?>
                <a href="orders" 
                   style="display: block; text-align: center; padding: 0.875rem; background: #f3f4f6; color: #4b5563; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.2s;">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .container > div:last-child {
        grid-template-columns: 1fr !important;
    }
}
</style>
