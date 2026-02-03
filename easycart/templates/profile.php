
<div class="container">
    <div class="page-content">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success" style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; text-align: center;">
                Profile updated successfully!
            </div>
        <?php endif; ?>

        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-img-wrapper">
                    <div class="profile-img-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="edit-img-btn">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <div class="profile-info-header">
                    <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                    <p>Member since Jan 2024</p>
                </div>
            </div>

            <div class="profile-grid">
                <!-- Left Column: Menu & Actions -->
                <div class="profile-sidebar">
                    <div class="profile-section-card">
                        <h3><i class="fas fa-user-cog"></i> Account</h3>
                        <div class="profile-actions">
                            <a href="#edit-modal" class="btn-profile-action"><i class="fas fa-user-edit"></i> Edit Profile</a>
                            <a href="orders.php" class="btn-profile-action"><i class="fas fa-box"></i> My Orders</a>
                            <a href="wishlist.php" class="btn-profile-action"><i class="fas fa-heart"></i> Wishlist</a>
                            <a href="#" class="btn-profile-action" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Details -->
                <div class="profile-main">
                    <div class="profile-section-card">
                        <h3><i class="fas fa-info-circle"></i> Personal Information</h3>
                        <div class="info-row">
                            <span class="info-label">Full Name</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['name']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email Address</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone Number</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['phone']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Location</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['location']); ?></span>
                        </div>
                    </div>

                    <div class="profile-section-card">
                        <h3><i class="fas fa-history"></i> Recent Activity</h3>
                        <?php if (!empty($recent_orders)): ?>
                            <?php foreach($recent_orders as $order): ?>
                            <div class="order-summary-item" onclick="window.location.href='invoice.php?id=<?php echo $order['order_id']; ?>'" style="cursor: pointer;">
                                <div class="order-summary-icon">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <div class="order-summary-info">
                                    <h4>Order #<?php echo htmlspecialchars($order['increment_id']); ?></h4>
                                    <p>Placed on <?php echo date('M d, Y', strtotime($order['created_at'])); ?> • ₹<?php echo number_format($order['grand_total'], 2); ?></p>
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>" style="font-size: 0.75rem; padding: 2px 6px; border-radius: 4px; background: #eee;"><?php echo ucfirst($order['status']); ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color: #666; font-style: italic;">No recent orders found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div id="edit-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Profile</h3>
            <a href="#" class="close-modal">&times;</a>
        </div>
        <form action="profile.php" method="POST" class="edit-profile-form">
            <input type="hidden" name="action" value="update_profile">
            
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>
            
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($user['location']); ?>">
            </div>
            
            <div class="modal-footer">
                <a href="#" class="btn btn-outline" style="width: auto; text-decoration: none; display: flex; align-items: center; justify-content: center;">Cancel</a>
                <button type="submit" class="btn" style="width: auto;">Save Changes</button>
            </div>
        </form>
    </div>
</div>
