<?php
// views/vendor/coupons.php
?>
<div class="admin-container">
    <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem;">
        <div>
            <h1 style="font-size: 1.8rem; margin: 0;"><?= htmlspecialchars($title) ?></h1>
            <p style="font-size: 0.95rem; opacity: 0.9;">Manage your store's promotional codes.</p>
        </div>
        <a href="<?= BASE_URL ?>vendor/couponedit" class="btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; border-radius: 8px;">
            <i class="fas fa-plus"></i> Create Coupon
        </a>
    </div>

    <div class="export-section" style="padding: 0; overflow: hidden; margin: 0 2rem;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="padding-left: 2rem;">Code</th>
                        <th>Discount</th>
                        <th>Min Order</th>
                        <th>Status</th>
                        <th>Expiry</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($coupons)): ?>
                        <tr>
                            <td colspan="6" class="no-data" style="text-align: center; padding: 2rem;">No coupons found. Create one to run a promotion!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($coupons as $coupon): ?>
                        <tr>
                            <td style="padding-left: 2rem;">
                                <span class="badge" style="font-weight: 700; color: #0891b2; background: #cffafe; border: 1px dashed #06b6d4; font-size: 1.1rem; padding: 0.4rem 0.8rem;"><?= htmlspecialchars($coupon['code']) ?></span>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;">
                                    <?php if($coupon['discount_type'] === 'percent'): ?>
                                        <?= number_format($coupon['discount_value']) ?>% OFF
                                    <?php else: ?>
                                        ₹<?= number_format($coupon['discount_value'], 2) ?> OFF
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="text-muted">₹<?= number_format($coupon['min_order_amount'], 2) ?></div>
                            </td>
                            <td>
                                <?php if ($coupon['is_active']): ?>
                                    <span class="status-badge delivered">Active</span>
                                <?php else: ?>
                                    <span class="status-badge cancelled">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?= $coupon['valid_until'] ? date('M d, Y', strtotime($coupon['valid_until'])) : 'Never Expires' ?></div>
                            </td>
                            <td class="table-actions">
                                <a href="<?= BASE_URL ?>vendor/couponedit?id=<?= $coupon['id'] ?>" class="action-view" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="<?= BASE_URL ?>vendor/coupondelete?id=<?= $coupon['id'] ?>" class="action-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this coupon?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
