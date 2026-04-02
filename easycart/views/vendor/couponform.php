<?php
// views/vendor/couponform.php
?>
<div class="admin-container">
    <div class="dashboard-header" style="text-align: left; padding: 1.5rem 2rem;">
        <h1 style="font-size: 1.8rem; margin: 0;"><?= htmlspecialchars($title) ?></h1>
        <p style="font-size: 0.95rem; opacity: 0.9;">Configure your promotional offer</p>
    </div>

    <div class="product-form-container" style="margin: 0 2rem; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 2rem;">
        <form action="<?= BASE_URL ?>vendor/couponsave" method="POST">
            <?php if (isset($coupon['id'])): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($coupon['id']) ?>">
            <?php endif; ?>

            <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                
                <!-- Code -->
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Coupon Code</label>
                    <input type="text" name="code" value="<?= htmlspecialchars($coupon['code'] ?? '') ?>" required placeholder="e.g. SUMMER50" style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-weight: 700; text-transform: uppercase;">
                    <small style="color: #64748b;">Customers will enter this code at checkout.</small>
                </div>

                <!-- Discount Type -->
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Discount Type</label>
                    <select name="discount_type" required style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <option value="percent" <?= (isset($coupon['discount_type']) && $coupon['discount_type'] === 'percent') ? 'selected' : '' ?>>Percentage (%)</option>
                        <option value="fixed" <?= (isset($coupon['discount_type']) && $coupon['discount_type'] === 'fixed') ? 'selected' : '' ?>>Fixed Amount (₹)</option>
                    </select>
                </div>

                <!-- Discount Value -->
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Discount Value</label>
                    <input type="number" step="0.01" min="0" name="discount_value" value="<?= htmlspecialchars($coupon['discount_value'] ?? '') ?>" required style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>

                <!-- Minimum Order -->
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Minimum Order Amount (₹)</label>
                    <input type="number" step="0.01" min="0" name="min_order_amount" value="<?= htmlspecialchars($coupon['min_order_amount'] ?? '0') ?>" style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>

                <!-- Expiry Date -->
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Valid Until</label>
                    <input type="datetime-local" name="valid_until" value="<?= isset($coupon['valid_until']) && $coupon['valid_until'] ? date('Y-m-d\TH:i', strtotime($coupon['valid_until'])) : '' ?>" style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <small style="color: #64748b;">Leave blank if it never expires.</small>
                </div>

                <!-- Active Toggle -->
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="checkbox-container" style="font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" name="is_active" <?= (!isset($coupon) || $coupon['is_active']) ? 'checked' : '' ?>>
                        <span class="checkmark"></span> Enable this coupon immediately
                    </label>
                </div>

            </div>

            <div class="form-actions" style="margin-top: 2rem; border-top: 1px solid #e2e8f0; padding-top: 1.5rem; display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="<?= BASE_URL ?>vendor/coupons" class="btn-outline" style="padding: 0.75rem 2rem; font-size: 1rem; border: 1px solid #cbd5e1; border-radius: 8px; text-decoration: none; color: #475569;">Cancel</a>
                <button type="submit" class="btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem; border-radius: 8px; border: none; cursor: pointer;">Save Coupon</button>
            </div>
        </form>
    </div>
</div>
