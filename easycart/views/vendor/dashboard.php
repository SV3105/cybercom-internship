<?php
// views/vendor/dashboard.php
?>

<!-- Statistics Cards Grid -->
<div class="stats-grid">
    <!-- Products Card -->
    <div class="dashboard-stat-card stat-card-products">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
        </div>
        <h3 class="stat-value"><?= number_format($stats['total_products'] ?? 0) ?></h3>
        <p class="stat-label">Total Products</p>
    </div>

    <!-- Orders Card -->
    <div class="dashboard-stat-card stat-card-orders">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
        <h3 class="stat-value"><?= number_format($stats['total_orders'] ?? 0) ?></h3>
        <p class="stat-label">Total Orders</p>
        <?php if (($stats['pending_orders'] ?? 0) > 0): ?>
            <span class="stat-badge warning"><?= $stats['pending_orders'] ?> Pending</span>
        <?php endif; ?>
    </div>

    <!-- Revenue Card -->
    <div class="dashboard-stat-card stat-card-revenue">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="fas fa-rupee-sign"></i>
            </div>
        </div>
        <h3 class="stat-value">₹<?= number_format($stats['total_revenue'] ?? 0, 2) ?></h3>
        <p class="stat-label">Total Revenue</p>
    </div>
</div>

<!-- Main Content Grid -->
<div class="dashboard-grid">
    <!-- Recent Orders -->
    <div class="dashboard-card" style="grid-column: span 2;">
        <div class="card-header">
            <h2>📋 Recent Orders Involving Your Products</h2>
            <a href="<?= BASE_URL ?>vendor/orders" class="view-all">View All →</a>
        </div>
        <div style="max-height: 400px; overflow-y: auto;">
            <?php if (!empty($stats['recent_orders'])): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stats['recent_orders'] as $order): ?>
                        <tr>
                            <td>
                                <strong style="color: #0f172a;">#<?= htmlspecialchars($order['increment_id'] ?: $order['order_id']) ?></strong>
                            </td>
                            <td>
                                <div style="font-weight: 500; color: #334155;"><?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></div>
                            </td>
                            <td>
                                <span class="status-badge <?= strtolower($order['status']) ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td class="text-muted">
                                <?= date('M d, Y', strtotime($order['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-inbox" style="font-size: 2.5rem; opacity: 0.3; margin-bottom: 1rem;"></i>
                    <p class="text-muted">No orders yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
