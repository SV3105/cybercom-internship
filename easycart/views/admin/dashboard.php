<?php
// views/admin/dashboard.php
?>

<!-- Statistics Cards Grid -->
<div class="stats-grid">
    <!-- Users Card -->
    <div class="dashboard-stat-card stat-card-products">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <h3 class="stat-value"><?= number_format($stats['total_users'] ?? 0) ?></h3>
        <p class="stat-label">Total Users</p>
    </div>

    <!-- Vendors Card -->
    <div class="dashboard-stat-card stat-card-orders">
        <div class="stat-header">
            <div class="stat-icon">
                <i class="fas fa-store"></i>
            </div>
        </div>
        <h3 class="stat-value"><?= number_format($stats['total_vendors'] ?? 0) ?></h3>
        <p class="stat-label">Total Vendors</p>
    </div>
</div>

<!-- Main Content Grid -->
<div class="dashboard-grid">
    <!-- Recent Users -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2><i class="fas fa-user-plus" style="margin-right: 0.5rem; color: #0f172a;"></i> Latest Users</h2>
            <a href="<?= BASE_URL ?>admin/users" class="view-all">View All →</a>
        </div>
        <div style="max-height: 400px; overflow-y: auto;">
            <?php if (!empty($stats['recent_users'])): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stats['recent_users'] as $user): ?>
                        <tr>
                            <td>
                                <strong style="color: #0f172a;"><?= htmlspecialchars($user['name']) ?></strong>
                            </td>
                            <td class="text-muted">
                                <?= htmlspecialchars($user['email']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p class="text-muted">No users yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Vendors -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2><i class="fas fa-store-alt" style="margin-right: 0.5rem; color: #0891b2;"></i> Latest Vendors</h2>
            <a href="<?= BASE_URL ?>admin/vendors" class="view-all">View All →</a>
        </div>
        <div style="max-height: 400px; overflow-y: auto;">
            <?php if (!empty($stats['recent_vendors'])): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Vendor Name</th>
                            <th>Store Name</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stats['recent_vendors'] as $vendor): ?>
                        <tr>
                            <td>
                                <strong style="color: #0f172a;"><?= htmlspecialchars($vendor['name']) ?></strong>
                            </td>
                            <td>
                                <span class="badge" style="background: #bae6fd; color: #0369a1;"><?= htmlspecialchars($vendor['store_name']) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p class="text-muted">No vendors yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
