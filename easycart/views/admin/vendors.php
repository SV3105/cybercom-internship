<?php
// views/admin/vendors.php
?>
<div class="admin-container">
    <div class="dashboard-header" style="text-align: left; padding: 1.5rem 2rem;">
        <h1 style="font-size: 1.8rem; margin: 0;"><?= htmlspecialchars($page_title) ?></h1>
        <p style="font-size: 0.95rem; opacity: 0.9;">Manage all registered vendors</p>
    </div>

    <div class="export-section" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="padding-left: 2rem;">Vendor ID</th>
                        <th>Store Name</th>
                        <th>Owner Name</th>
                        <th>Email</th>
                        <th>Joined Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vendors)): ?>
                        <tr>
                            <td colspan="6" class="no-data">No vendors found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($vendors as $vendor): ?>
                        <tr>
                            <td style="padding-left: 2rem;">
                                <span style="font-weight: 700; color: #0891b2;">#<?= $vendor['id'] ?></span>
                            </td>
                            <td>
                                <span class="badge" style="background: #bae6fd; color: #0369a1;"><?= htmlspecialchars($vendor['store_name']) ?></span>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($vendor['name']) ?></div>
                            </td>
                            <td>
                                <div class="text-muted"><?= htmlspecialchars($vendor['email']) ?></div>
                            </td>
                            <td>
                                <div><?= date('M d, Y', strtotime($vendor['created_at'])) ?></div>
                            </td>
                            <td>
                                <span class="status-badge delivered">Active</span>
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
                    Showing <?= min($totalItems, $offset + 1) ?> to <?= min($totalItems, $offset + $limit) ?> of <?= $totalItems ?> vendors
                </div>
                <div class="pagination-links">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 ?>" class="pagination-btn"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="pagination-btn <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?>" class="pagination-btn"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
