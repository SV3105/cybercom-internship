
<div class="container">
    <div class="page-content">
        <h1 class="page-title">My Orders</h1>
        
        <div class="orders-list">
            <?php if(empty($orders)): ?>
            <div class="no-results" style="display:block; text-align:center; padding: 4rem 0;">
                <i class="fas fa-box-open" style="font-size: 4rem; color: #ddd; margin-bottom: 1.5rem;"></i>
                <p>You haven't placed any orders yet.</p>
                <a href="products" class="btn" style="margin-top: 1.5rem;">Start Shopping</a>
            </div>
            <?php else: ?>
            
            <?php foreach($orders as $order): ?>
            <div class="order-card" style="background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div class="order-header" style="display: flex; justify-content: space-between; border-bottom: 1px solid #f0f0f0; padding-bottom: 1rem; margin-bottom: 1rem;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; color: #333;">Order #<?php echo $order['id']; ?></h3>
                        <p style="margin: 0.25rem 0 0; color: #888; font-size: 0.9rem;">
                            <?php echo $order['date']; ?> • 
                            <i class="fas fa-truck" style="margin-left: 0.5rem;"></i> <?php echo $order['shipping_type']; ?>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <span class="order-status status-<?php echo $order['status_code']; ?>" style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; 
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
                            ?>;
                        ">
                            <?php echo $order['status']; ?>
                        </span>
                        <p style="margin: 0.5rem 0 0; font-weight: 600; color: #333;">Total: ₹<?php echo $order['total']; ?></p>
                    </div>
                </div>
                
                <div class="order-items">
                    <?php foreach($order['items'] as $item): ?>
                    <div class="order-item-row" style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.95rem;">
                        <span style="color: #444;"><?php echo $item['qty']; ?> x <?php echo $item['title']; ?></span>
                        <span style="font-weight: 500;">₹<?php echo $item['price']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="order-actions" style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #f0f0f0; display: flex; gap: 1rem; flex-wrap: wrap;">
                    <!-- Updated Link -->
                    <a href="orderdetails?id=<?php echo $order['db_id']; ?>" class="btn-action btn-primary-action" style="background: #4f46e5; color: white; border: none;">
                        <i class="fas fa-box-open"></i> View Details
                    </a>
                    <!-- NOTE: Invoice PHP likely not refactored yet, keep generic or update if needed. Keeping as is but assume relative path -->
                    <a href="invoice?id=<?php echo $order['db_id']; ?>" class="btn-action btn-outline-primary" target="_blank">
                        <i class="fas fa-file-invoice"></i> Download Invoice
                    </a>
                    <?php if($order['status_code'] != 'delivered'): ?>
                    <button class="btn-action btn-outline-primary" onclick="alert('Tracking ID: #track-<?php echo $order['id']; ?>\nStatus: <?php echo $order['status']; ?>\n\nYour order is on the way!')">
                        <i class="fas fa-truck-fast"></i> Track Order
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php endif; ?>
        </div>
    </div>
</div>
