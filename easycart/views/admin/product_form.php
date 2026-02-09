<div class="admin-container">
    <div class="dashboard-header" style="text-align: left; padding: 1.5rem 2rem;">
        <h1 style="font-size: 1.8rem; margin: 0;"><?= htmlspecialchars($title) ?></h1>
    </div>

    <div class="export-options" style="margin: 0 2rem;">
        <form action="<?= BASE_URL ?>admin/product-save" method="POST" enctype="multipart/form-data" style="max-width: 800px; margin: 0 auto;">
            <?php if ($product): ?>
                <input type="hidden" name="entity_id" value="<?= $product['entity_id'] ?>">
                <input type="hidden" name="current_image" value="<?= $product['image'] ?? '' ?>">
            <?php endif; ?>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Product Image</label>
                <?php if (!empty($product['image'])): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="<?= BASE_URL ?>public/images/<?= $product['image'] ?>" alt="Current Image" style="max-height: 100px; border-radius: 4px; border: 1px solid #ddd;">
                    </div>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*" style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                <small style="color: #64748b;">Leave blank to keep current image</small>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Product Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">SKU</label>
                    <input type="text" name="sku" value="<?= htmlspecialchars($product['sku'] ?? '') ?>" required style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Price (₹)</label>
                    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price'] ?? '') ?>" required style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Category</label>
                    <select name="category_id" style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <option value="">Select Category</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['entity_id'] ?>" <?= ($product['category_id'] ?? '') == $cat['entity_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Brand</label>
                    <select name="brand_id" style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <option value="">Select Brand</option>
                        <?php foreach($brands as $brand): ?>
                            <option value="<?= $brand['entity_id'] ?>" <?= ($product['brand_id'] ?? '') == $brand['entity_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($brand['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Stock Qty</label>
                <input type="number" name="stock_qty" value="<?= htmlspecialchars($product['stock_qty'] ?? '') ?>" required style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Description</label>
                <textarea name="description" rows="4" style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px;"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_featured" <?= ($product['is_featured'] ?? 'f') === 't' ? 'checked' : '' ?>>
                    <span style="font-weight: 600;">Featured Product</span>
                </label>
            </div>

            <div class="form-actions" style="display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="<?= BASE_URL ?>admin/products" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-export">Save Product</button>
            </div>
        </form>
    </div>
</div>
