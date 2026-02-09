<?php
// views/admin/orders.php
?>
<div class="admin-container">
    <div class="dashboard-header" style="text-align: left; display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem;">
        <div>
            <h1 style="font-size: 1.8rem; margin: 0;">Order Management</h1>
            <p style="font-size: 0.95rem; opacity: 0.9;">View and manage customer orders</p>
        </div>
        <div class="stat-card" style="padding: 0.75rem 1.5rem; border: none; background: rgba(255,255,255,0.1); box-shadow: none; margin: 0;">
            <div style="text-align: right;">
                <span style="display: block; font-size: 0.75rem; opacity: 0.8;">Total Orders</span>
                <span style="font-size: 1.5rem; font-weight: 800;"><?= $totalItems ?></span>
            </div>
        </div>
    </div>

    <!-- Order List -->
    <div class="export-section" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="padding-left: 2rem;">Order #</th>
                        <th>Customer</th>
                        <th>Date Purchased</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="no-data">No orders have been placed yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td style="padding-left: 2rem;">
                                <span style="font-weight: 700; color: #0891b2;">#<?= $order['increment_id'] ?: $order['order_id'] ?></span>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($order['customer_name'] ?? 'Guest Customer') ?></div>
                                <div class="text-muted"><?= htmlspecialchars($order['customer_email'] ?? 'No email') ?></div>
                            </td>
                            <td>
                                <div><?= date('M d, Y', strtotime($order['created_at'])) ?></div>
                                <div class="text-muted"><?= date('h:i A', strtotime($order['created_at'])) ?></div>
                            </td>
                            <td>
                                <span class="status-badge <?= strtolower($order['status']) ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge" style="background: #f1f5f9; color: #475569; box-shadow: none;">
                                    <?= $order['total_item_count'] ?? '-' ?> items
                                </span>
                            </td>
                            <td style="font-weight: 700; color: #0f172a;">
                                ₹<?= number_format($order['grand_total'], 2) ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>admin/order-view?id=<?= $order['order_id'] ?>" class="action-btn btn-sm" style="background: white; color: #0891b2; border: 1px solid #bae6fd; box-shadow: none;">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-container">
                <div style="font-size: 0.9rem; color: #64748b;">
                    Showing <?= min($totalItems, $offset + 1) ?> to <?= min($totalItems, $offset + $limit) ?> of <?= $totalItems ?> orders
                </div>
                <div class="pagination-links">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 ?>" class="pagination-btn"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>

                    <?php
                    $start = max(1, $currentPage - 2);
                    $end = min($totalPages, $currentPage + 2);
                    
                    if ($start > 1) {
                        echo '<a href="?page=1" class="pagination-btn">1</a>';
                        if ($start > 2) echo '<span style="padding: 0.5rem; color: #94a3b8;">...</span>';
                    }

                    for ($i = $start; $i <= $end; $i++): ?>
                        <a href="?page=<?= $i ?>" class="pagination-btn <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1) echo '<span style="padding: 0.5rem; color: #94a3b8;">...</span>'; ?>
                        <a href="?page=<?= $totalPages ?>" class="pagination-btn"><?= $totalPages ?></a>
                    <?php endif; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?>" class="pagination-btn"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
