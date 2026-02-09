<?php
// views/admin/dashboard.php
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

    <!-- Users Card -->
    <div class="dashboard-stat-card stat-card-users">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <h3 class="stat-value"><?= number_format($stats['total_users'] ?? 0) ?></h3>
        <p class="stat-label">Total Users</p>
    </div>

    <!-- Low Stock Alert -->
    <div class="dashboard-stat-card stat-card-alert">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
        <h3 class="stat-value"><?= number_format($stats['low_stock'] ?? 0) ?></h3>
        <p class="stat-label">Low Stock Items</p>
        <?php if (($stats['low_stock'] ?? 0) > 0): ?>
            <span class="stat-badge danger">Action Required</span>
        <?php endif; ?>
    </div>
</div>

<!-- Main Content Grid -->
<div class="dashboard-grid">
    <!-- Recent Orders -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2>📋 Recent Orders</h2>
            <a href="<?= BASE_URL ?>admin/orders" class="view-all">View All →</a>
        </div>
        <div style="max-height: 400px; overflow-y: auto;">
            <?php if (!empty($stats['recent_orders'])): ?>
                <table class="data-table">
                    <tbody>
                    <?php foreach ($stats['recent_orders'] as $order): ?>
                        <tr>
                            <td>
                                <strong style="color: #0f172a;">#<?= htmlspecialchars($order['increment_id']) ?></strong>
                            </td>
                            <td>
                                <div style="font-weight: 500; color: #334155;"><?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></div>
                                <div class="text-muted"><?= date('M d, Y', strtotime($order['created_at'])) ?></div>
                            </td>
                            <td>
                                <span class="status-badge <?= strtolower($order['status']) ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 600; color: #10b981;">
                                ₹<?= number_format($order['grand_total'], 2) ?>
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

    <!-- Top Selling Products -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2>🏆 Top Selling Products</h2>
        </div>
        <div class="top-products">
            <?php if (!empty($stats['top_products'])): ?>
                <?php foreach ($stats['top_products'] as $product): ?>
                    <div class="product-item">
                        <img src="<?= BASE_URL ?>public/images/<?= htmlspecialchars($product['image']) ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             onerror="this.src='<?= BASE_URL ?>public/images/placeholder.svg'">
                        <div class="product-info">
                            <h4><?= htmlspecialchars($product['name']) ?></h4>
                            <div class="sales-info">
                                <span class="quantity"><?= number_format($product['total_sold']) ?> sold</span>
                                <span class="revenue">₹<?= number_format($product['revenue'], 2) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-chart-bar" style="font-size: 2.5rem; opacity: 0.3; margin-bottom: 1rem;"></i>
                    <p class="text-muted">No sales data yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Inventory Stock Chart -->
    <div class="dashboard-card" style="grid-column: span 2;">
        <div class="card-header">
            <h2>📊 Inventory Stock by Category</h2>
        </div>
        <div class="chart-container-wrapper">
            <div class="chart-container">
                <canvas id="inventoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="dashboard-card" style="grid-column: span 2;">
        <div class="card-header">
            <h2>⚠️ Low Stock Alert</h2>
        </div>
        <div class="low-stock-list">
            <?php if (!empty($stats['low_stock_products'])): ?>
                <div class="options-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
                    <?php foreach ($stats['low_stock_products'] as $product): ?>
                        <div class="stock-item" style="border: 1px solid #e2e8f0; border-radius: 8px;">
                            <div class="stock-info">
                                <h4><?= htmlspecialchars($product['name']) ?></h4>
                                <p class="sku">SKU: <?= htmlspecialchars($product['sku']) ?></p>
                            </div>
                            <span class="stock-quantity <?= ($product['stock_qty'] ?? 0) == 0 ? 'out-of-stock' : 'low' ?>">
                                <?= $product['stock_qty'] ?? 0 ?> left
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-data success">
                    <i class="fas fa-check-circle" style="font-size: 2.5rem; margin-bottom: 1rem;"></i>
                    <p>All products are well stocked!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('inventoryChart').getContext('2d');
    const categoryData = <?= json_encode($stats['category_stock'] ?? []) ?>;
    
    const labels = categoryData.map(item => item.category || 'Uncategorized');
    const data = categoryData.map(item => parseInt(item.total_stock));
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Stock Quantity',
                data: data,
                backgroundColor: 'rgba(8, 145, 178, 0.6)',
                borderColor: 'rgba(8, 145, 178, 1)',
                borderWidth: 1,
                borderRadius: 8,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: '#f1f5f9'
                    },
                    ticks: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 12
                        },
                        color: '#64748b'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 12,
                            weight: '500'
                        },
                        color: '#1e293b'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    padding: 12,
                    backgroundColor: '#0f172a',
                    titleFont: {
                        family: "'Montserrat', sans-serif",
                        size: 14,
                        weight: '700'
                    },
                    bodyFont: {
                        family: "'Inter', sans-serif",
                        size: 13
                    },
                    cornerRadius: 8,
                    displayColors: false
                }
            }
        }
    });
});
</script>
