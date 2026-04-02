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
            <div style="font-weight: 600; color: #333; font-size: 0.9rem;">
                Order Status: 
                <span class="status-badge <?= strtolower($order['status']) ?>" style="margin-left: 0.5rem;">
                    <?= ucfirst($order['status']) ?>
                </span>
            </div>
        </div>
    </div>

    <div class="admin-grid" style="grid-template-columns: 2fr 1fr; margin: 0 2rem 2rem;">
        <!-- Left Column: Items -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
                <h3 style="margin: 0; color: #0f172a; font-size: 1.1rem;">Your Order Items</h3>
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
            <!-- Address -->
            <?php if($order['firstname'] ?? false): ?>
            <div class="card">
                <h3 style="margin-bottom: 1rem;">Shipping Address</h3>
                <p style="margin: 0 0 0.5rem; font-weight: 600;"><?= htmlspecialchars(($order['firstname'] ?? '') . ' ' . ($order['lastname'] ?? '')) ?></p>
                <p style="margin: 0; color: #64748b; line-height: 1.5;">
                    <?= htmlspecialchars($order['street'] ?? '') ?><br>
                    <?= htmlspecialchars($order['city'] ?? '') ?>, <?= htmlspecialchars($order['postcode'] ?? '') ?><br>
                    Phone: <?= htmlspecialchars($order['telephone'] ?? '') ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
