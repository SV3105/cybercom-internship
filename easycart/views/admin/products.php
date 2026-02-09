<?php
// views/admin/products.php
?>
<div class="admin-container">
    <div class="dashboard-header" style="text-align: left; display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.8rem; margin: 0;">Product Management</h1>
            <p style="font-size: 0.95rem; opacity: 0.9;">Manage your product catalog</p>
        </div>
        
        <div style="display: flex; gap: 1rem; align-items: center; flex-grow: 1; justify-content: flex-end;">
            <form action="<?= BASE_URL ?>admin/products" method="GET" style="display: flex; gap: 0.5rem; flex-grow: 1; max-width: 550px; align-items: stretch; box-sizing: border-box;">
                <div style="position: relative; flex: 1; min-width: 200px; box-sizing: border-box;">
                    <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; z-index: 2;"></i>
                    <input type="text" name="search" placeholder="Search by name or SKU..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="width: 100%; padding: 0.6rem 1rem 0.6rem 2.5rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem; background: white; box-sizing: border-box; display: block; height: 100%;">
                </div>
                <button type="submit" class="btn-primary" style="padding: 0 1.5rem; white-space: nowrap; flex-shrink: 0; min-height: 40px; display: flex; align-items: center; justify-content: center; border: none; border-radius: 8px; cursor: pointer; box-sizing: border-box; font-size: 0.9rem;">Search</button>
                <?php if (!empty($_GET['search'])): ?>
                    <a href="<?= BASE_URL ?>admin/products" class="btn-secondary" style="padding: 0 1.2rem; text-decoration: none; white-space: nowrap; flex-shrink: 0; min-height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; box-sizing: border-box; font-size: 0.9rem;">Clear</a>
                <?php endif; ?>
            </form>

            <a href="<?= BASE_URL ?>admin/import" class="action-btn">
                <i class="fas fa-plus"></i> Add Products
            </a>
        </div>
    </div>

    <!-- Product List -->
    <div class="export-section" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="padding-left: 2rem;">Image</th>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" class="no-data">No products found in the catalog.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td style="padding-left: 2rem;">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?= BASE_URL ?>public/images/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="table-img">
                                <?php else: ?>
                                    <div class="table-img" style="background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($product['name']) ?></div>
                            </td>
                            <td>
                                <code style="font-size: 0.85rem; color: #64748b;"><?= htmlspecialchars($product['sku']) ?></code>
                            </td>
                            <td>
                                    <?= htmlspecialchars(ucfirst($product['category_name'] ?? 'General')) ?>
                                </span>
                            </td>
                            <td style="font-weight: 700; color: #0f172a;">
                                ₹<?= number_format($product['price'], 2) ?>
                            </td>
                            <td>
                                <span class="stock-quantity <?= ($product['stock_qty'] ?? 0) < 10 ? (($product['stock_qty'] ?? 0) == 0 ? 'out-of-stock' : 'low') : '' ?>">
                                    <?= $product['stock_qty'] ?? 0 ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="<?= BASE_URL ?>admin/productedit?id=<?= $product['entity_id'] ?>" class="action-btn btn-sm" style="background: white; color: #0891b2; border: 1px solid #bae6fd; box-shadow: none;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>admin/productdelete?id=<?= $product['entity_id'] ?>" class="action-btn btn-sm" style="background: white; color: #ef4444; border: 1px solid #fecaca; box-shadow: none;" onclick="return confirm('Are you sure you want to delete this product?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination-container" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; background: white; border-top: 1px solid #e2e8f0;">
            <div style="font-size: 0.9rem; color: #64748b;">
                Showing <?= min($totalItems, $offset + 1) ?> to <?= min($totalItems, $offset + $limit) ?> of <?= $totalItems ?> products
            </div>
            <div class="pagination-links" style="display: flex; gap: 0.5rem;">
                <?php if ($currentPage > 1): ?>
                    <a href="?search=<?= urlencode($search) ?>&page=<?= $currentPage - 1 ?>" class="pagination-btn"><i class="fas fa-chevron-left"></i></a>
                <?php endif; ?>

                <?php
                $start = max(1, $currentPage - 2);
                $end = min($totalPages, $currentPage + 2);
                
                if ($start > 1) {
                    echo '<a href="?search='.urlencode($search).'&page=1" class="pagination-btn">1</a>';
                    if ($start > 2) echo '<span style="padding: 0.5rem; color: #94a3b8;">...</span>';
                }

                for ($i = $start; $i <= $end; $i++): ?>
                    <a href="?search=<?= urlencode($search) ?>&page=<?= $i ?>" class="pagination-btn <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1) echo '<span style="padding: 0.5rem; color: #94a3b8;">...</span>'; ?>
                    <a href="?search=<?= urlencode($search) ?>&page=<?= $totalPages ?>" class="pagination-btn"><?= $totalPages ?></a>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?search=<?= urlencode($search) ?>&page=<?= $currentPage + 1 ?>" class="pagination-btn"><i class="fas fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
