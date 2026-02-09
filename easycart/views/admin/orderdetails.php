<div class="admin-container">
    <div class="dashboard-header" style="text-align: left; display: flex; justify-content: space-between; align-items: start; padding: 1.5rem 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.8rem; margin: 0 0 0.5rem;"><?= htmlspecialchars($title) ?></h1>
            <p style="font-size: 0.95rem; opacity: 0.9; margin: 0;">
                Customer: <strong><?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></strong> | 
                Date: <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?>
            </p>
        </div>
        
        <div class="status-manager" style="background: white; padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <form action="<?= BASE_URL ?>admin/orderstatusupdate" method="POST" style="display: flex; gap: 0.5rem; align-items: center;" onsubmit="return confirm('Are you sure you want to update the status?');">
                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                <label style="font-weight: 600; color: #333; font-size: 0.9rem;">Status:</label>
                <?php
                $currentStatus = strtolower($order['status']);
                $isDelivered = ($currentStatus == 'delivered');
                ?>
                <select name="status" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 500;" <?= $isDelivered ? 'disabled' : '' ?>>
                    <?php 
                    $statuses = ['pending', 'processing', 'transit', 'delivered'];
                    foreach($statuses as $st): 
                        // Logic: If Transit, cannot go back to Processing
                        $isDisabledOption = ($currentStatus == 'transit' && $st == 'processing');
                    ?>
                        <option value="<?= $st ?>" <?= $currentStatus == $st ? 'selected' : '' ?> <?= $isDisabledOption ? 'disabled' : '' ?>>
                            <?= ucfirst($st) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!$isDelivered): ?>
                    <button type="submit" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Update</button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="admin-grid" style="grid-template-columns: 2fr 1fr; margin: 0 2rem 2rem;">
        <!-- Left Column: Items -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
                <h3 style="margin: 0; color: #0f172a; font-size: 1.1rem;">Order Items</h3>
            </div>
            <div style="padding: 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f1f5f9; text-align: left; font-size: 0.85rem; color: #64748b;">
                            <th style="padding: 0.75rem 1.5rem;">Product</th>
                            <th style="padding: 0.75rem 1.5rem;">SKU</th>
                            <th style="padding: 0.75rem 1.5rem;">Price</th>
                            <th style="padding: 0.75rem 1.5rem;">Qty</th>
                            <th style="padding: 0.75rem 1.5rem; text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 1rem 1.5rem; display: flex; align-items: center; gap: 1rem;">
                                <?php if(!empty($item['image'])): ?>
                                    <img src="<?= BASE_URL ?>public/images/<?= $item['image'] ?>" alt="Img" style="width: 40px; height: 40px; object-fit: contain; border: 1px solid #e2e8f0; border-radius: 4px;">
                                <?php endif; ?>
                                <span style="font-weight: 500; color: #0f172a;"><?= htmlspecialchars($item['name']) ?></span>
                            </td>
                            <td style="padding: 1rem 1.5rem; color: #64748b; font-size: 0.9rem;"><?= htmlspecialchars($item['sku'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem 1.5rem;">₹<?= number_format($item['price'], 2) ?></td>
                            <td style="padding: 1rem 1.5rem;"><?= $item['quantity'] ?></td>
                            <td style="padding: 1rem 1.5rem; text-align: right; font-weight: 600;">₹<?= number_format($item['total_price'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: Info -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Financials -->
            <div class="card">
                <h3 style="margin-bottom: 1rem;">Order Summary</h3>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #64748b;">
                    <span>Subtotal</span>
                    <span>₹<?= number_format($order['subtotal'], 2) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #64748b;">
                    <span>Shipping</span>
                    <span>₹<?= number_format($order['shipping_amount'], 2) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #64748b;">
                    <span>Tax</span>
                    <span>₹<?= number_format($order['tax_amount'], 2) ?></span>
                </div>
                 <?php if (($order['discount_amount'] ?? 0) > 0): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #10b981;">
                    <span>Discount</span>
                    <span>-₹<?= number_format($order['discount_amount'], 2) ?></span>
                </div>
                <?php endif; ?>
                <div style="border-top: 1px solid #e2e8f0; margin-top: 0.5rem; padding-top: 0.5rem; display: flex; justify-content: space-between; font-weight: 700; color: #0f172a; font-size: 1.1rem;">
                    <span>Grand Total</span>
                    <span>₹<?= number_format($order['grand_total'], 2) ?></span>
                </div>
            </div>

            <!-- Address -->
            <?php if($address): ?>
            <div class="card">
                <h3 style="margin-bottom: 1rem;">Shipping Address</h3>
                <p style="margin: 0 0 0.5rem; font-weight: 600;"><?= htmlspecialchars(($address['firstname'] ?? '') . ' ' . ($address['lastname'] ?? '')) ?></p>
                <p style="margin: 0; color: #64748b; line-height: 1.5;">
                    <?= htmlspecialchars($address['street'] ?? '') ?><br>
                    <?= htmlspecialchars($address['city'] ?? '') ?>, <?= htmlspecialchars($address['postcode'] ?? '') ?><br>
                    Phone: <?= htmlspecialchars($address['telephone'] ?? '') ?>
                </p>
            </div>
            <?php endif; ?>

            <!-- Payment -->
            <div class="card">
                <h3 style="margin-bottom: 1rem;">Payment Information</h3>
                <p style="margin: 0 0 0.5rem;">Method: <strong><?= ucfirst($payment['method'] ?? 'Unknown') ?></strong></p>
                
                <?php
                $method = strtolower($payment['method'] ?? '');
                $st = strtolower($order['status']);
                $payStatus = 'Pending';
                $bg = '#fef3c7'; $color = '#92400e'; 

                if ($method == 'cash') {
                    if ($st == 'delivered') { $payStatus = 'Paid'; $bg = '#dcfce7'; $color = '#166534'; }
                } else {
                    // For online payments, assuming processing/transit/delivered = Paid
                    if ($st == 'processing' || $st == 'transit' || $st == 'delivered') { $payStatus = 'Paid'; $bg = '#dcfce7'; $color = '#166534'; }
                }
                ?>
                <p style="margin: 0 0 0.5rem;">Status: 
                    <span style="font-weight: 600; padding: 0.25rem 0.75rem; border-radius: 20px; background: <?= $bg ?>; color: <?= $color ?>; font-size: 0.85rem;">
                        <?= $payStatus ?>
                    </span>
                </p>

                <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                    <?= htmlspecialchars($payment['payment_info'] ?? '') ?>
                </p>
            </div>

            <!-- Admin Actions & Notes -->
            <div class="card">
                <h3 style="margin-bottom: 1rem;">Admin Actions</h3>
                
                <div style="margin-bottom: 1.5rem;">
                    <a href="<?= BASE_URL ?>invoice?id=<?= $order['order_id'] ?>" target="_blank" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; padding: 0.5rem 1rem; font-size: 0.85rem;">
                        <i class="fas fa-file-invoice"></i> Download Invoice
                    </a>
                </div>

                <div class="order-notes-section" style="padding: 0; background: transparent; border: none; margin-top: 0;">
                    <h4 style="margin-bottom: 0.5rem; font-size: 0.95rem; color: #334155;">Internal Notes</h4>
                    <form action="<?= BASE_URL ?>admin/ordersavenotes" method="POST">
                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                        <textarea name="admin_notes" class="notes-textarea" placeholder="Add notes about this order..."><?= htmlspecialchars($order['admin_notes'] ?? '') ?></textarea>
                        <button type="submit" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Save Notes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
