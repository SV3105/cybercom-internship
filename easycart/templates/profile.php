
<div class="container">
    <div class="page-content">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #166534; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 2rem; text-align: center; border: 1px solid #86efac; box-shadow: 0 2px 8px rgba(22, 101, 52, 0.1);">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                <?php 
                if ($_GET['success'] === 'password_changed') {
                    echo 'Password changed successfully!';
                } else {
                    echo 'Profile updated successfully!';
                }
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-error" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 2rem; text-align: center; border: 1px solid #fca5a5; box-shadow: 0 2px 8px rgba(153, 27, 27, 0.1);">
                <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                <?php 
                $error = $_GET['error'];
                if ($error === 'password_empty') echo 'All password fields are required!';
                elseif ($error === 'password_mismatch') echo 'New passwords do not match!';
                elseif ($error === 'password_short') echo 'Password must be at least 6 characters!';
                elseif ($error === 'password_incorrect') echo 'Current password is incorrect!';
                else echo 'An error occurred. Please try again.';
                ?>
            </div>
        <?php endif; ?>

        <div class="profile-container" style="max-width: 1200px; margin: 0 auto;">
            <!-- Profile Header -->
            <div class="profile-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2.5rem; border-radius: 20px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);">
                <div style="text-align: center; color: white;">
                    <h1 style="margin: 0 0 0.5rem; font-size: 2rem; font-weight: 700;"><?php echo htmlspecialchars($user['name']); ?></h1>
                    <p style="margin: 0; font-size: 1rem; opacity: 0.9;"><i class="fas fa-calendar-alt" style="margin-right: 0.5rem;"></i>Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <a href="#edit-modal" class="btn-profile-action" style="background: white; padding: 1rem; border-radius: 12px; text-decoration: none; color: #334155; display: flex; align-items: center; gap: 0.75rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
                    <i class="fas fa-user-edit" style="color: #6366f1; font-size: 1.25rem;"></i>
                    <span style="font-weight: 500;">Edit Profile</span>
                </a>
                <a href="#password-modal" class="btn-profile-action" style="background: white; padding: 1rem; border-radius: 12px; text-decoration: none; color: #334155; display: flex; align-items: center; gap: 0.75rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
                    <i class="fas fa-key" style="color: #8b5cf6; font-size: 1.25rem;"></i>
                    <span style="font-weight: 500;">Change Password</span>
                </a>
                <a href="orders.php" class="btn-profile-action" style="background: white; padding: 1rem; border-radius: 12px; text-decoration: none; color: #334155; display: flex; align-items: center; gap: 0.75rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
                    <i class="fas fa-box" style="color: #ec4899; font-size: 1.25rem;"></i>
                    <span style="font-weight: 500;">My Orders</span>
                </a>
                <a href="wishlist.php" class="btn-profile-action" style="background: white; padding: 1rem; border-radius: 12px; text-decoration: none; color: #334155; display: flex; align-items: center; gap: 0.75rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
                    <i class="fas fa-heart" style="color: #f43f5e; font-size: 1.25rem;"></i>
                    <span style="font-weight: 500;">Wishlist</span>
                </a>
            </div>

            <!-- Main Content -->
            <div style="display: grid; gap: 2rem;">
                <!-- Dashboard Overview -->
                <div style="background: white; padding: 2rem; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                    <h3 style="margin: 0 0 1.5rem; font-size: 1.25rem; font-weight: 700; color: #1e293b;"><i class="fas fa-chart-line" style="margin-right: 0.75rem; color: #6366f1;"></i>Dashboard Overview</h3>
                    
                    <!-- Stats Grid -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
                        <!-- Total Orders Card -->
                        <div style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); padding: 2rem; border-radius: 16px; color: white; box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);">
                            <div style="display: flex; align-items: center; gap: 1.25rem;">
                                <div style="background: rgba(255, 255, 255, 0.2); width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-shopping-bag" style="font-size: 1.75rem;"></i>
                                </div>
                                <div>
                                    <p style="margin: 0 0 0.25rem; font-size: 0.875rem; font-weight: 500; opacity: 0.9;">Total Orders</p>
                                    <h2 style="margin: 0; font-size: 2.25rem; font-weight: 700;"><?php echo $total_orders; ?></h2>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Spent Card -->
                        <div style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); padding: 2rem; border-radius: 16px; color: white; box-shadow: 0 8px 20px rgba(219, 39, 119, 0.3);">
                            <div style="display: flex; align-items: center; gap: 1.25rem;">
                                <div style="background: rgba(255, 255, 255, 0.2); width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-wallet" style="font-size: 1.75rem;"></i>
                                </div>
                                <div>
                                    <p style="margin: 0 0 0.25rem; font-size: 0.875rem; font-weight: 500; opacity: 0.9;">Total Spent</p>
                                    <h2 style="margin: 0; font-size: 2.25rem; font-weight: 700;">₹<?php echo number_format($total_spent); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Section -->
                    <div style="background: #f8fafc; padding: 2rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                        <h4 style="margin: 0 0 1.5rem; color: #334155; font-size: 1.125rem; font-weight: 600;"><i class="fas fa-chart-area" style="margin-right: 0.75rem; color: #6366f1;"></i>Spending History</h4>
                        <?php if (!empty($chart_data)): ?>
                            <div style="position: relative; height: 300px; width: 100%;">
                                <canvas id="orderChart"></canvas>
                            </div>
                        <?php else: ?>
                            <div style="height: 300px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8;">
                                <i class="fas fa-chart-line" style="font-size: 3.5rem; margin-bottom: 1.25rem; opacity: 0.3;"></i>
                                <p style="margin: 0; font-size: 1rem; font-weight: 500;">No order history yet</p>
                                <p style="margin: 0.5rem 0 0; font-size: 0.875rem;">Start shopping to see your spending trends!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Personal Information -->
                <div style="background: white; padding: 2rem; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                    <h3 style="margin: 0 0 1.5rem; font-size: 1.25rem; font-weight: 700; color: #1e293b;"><i class="fas fa-info-circle" style="margin-right: 0.75rem; color: #6366f1;"></i>Personal Information</h3>
                    <div style="display: grid; gap: 1.25rem;">
                        <div style="display: flex; justify-content: space-between; padding: 1rem; background: #f8fafc; border-radius: 10px;">
                            <span style="font-weight: 600; color: #64748b;">Full Name</span>
                            <span style="color: #1e293b;"><?php echo htmlspecialchars($user['name']); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 1rem; background: #f8fafc; border-radius: 10px;">
                            <span style="font-weight: 600; color: #64748b;">Email Address</span>
                            <span style="color: #1e293b;"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 1rem; background: #f8fafc; border-radius: 10px;">
                            <span style="font-weight: 600; color: #64748b;">Phone Number</span>
                            <span style="color: #1e293b;"><?php echo htmlspecialchars($user['phone']); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 1rem; background: #f8fafc; border-radius: 10px;">
                            <span style="font-weight: 600; color: #64748b;">Location</span>
                            <span style="color: #1e293b;"><?php echo htmlspecialchars($user['location']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div style="background: white; padding: 2rem; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                    <h3 style="margin: 0 0 1.5rem; font-size: 1.25rem; font-weight: 700; color: #1e293b;"><i class="fas fa-history" style="margin-right: 0.75rem; color: #6366f1;"></i>Recent Activity</h3>
                    <?php if (!empty($recent_orders)): ?>
                        <div style="display: grid; gap: 1rem;">
                            <?php foreach($recent_orders as $order): ?>
                            <div onclick="window.location.href='invoice.php?id=<?php echo $order['order_id']; ?>'" style="cursor: pointer; padding: 1.25rem; background: #f8fafc; border-radius: 12px; display: flex; align-items: center; gap: 1rem; transition: all 0.3s; border: 1px solid #e2e8f0;" onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                                <div style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="fas fa-shopping-bag" style="font-size: 1.25rem;"></i>
                                </div>
                                <div style="flex: 1;">
                                    <h4 style="margin: 0 0 0.25rem; font-size: 1rem; font-weight: 600; color: #1e293b;">Order #<?php echo htmlspecialchars($order['increment_id']); ?></h4>
                                    <p style="margin: 0; font-size: 0.875rem; color: #64748b;">Placed on <?php echo date('M d, Y', strtotime($order['created_at'])); ?> • ₹<?php echo number_format($order['grand_total'], 2); ?></p>
                                </div>
                                <span style="font-size: 0.75rem; padding: 0.375rem 0.75rem; border-radius: 6px; background: #dcfce7; color: #166534; font-weight: 600;"><?php echo ucfirst($order['status']); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="padding: 3rem; text-align: center; color: #94a3b8;">
                            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                            <p style="margin: 0; font-size: 1rem;">No recent orders found</p>
                        </div>
                    <?php endif; ?>
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

<!-- Change Password Modal -->
<div id="password-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-key"></i> Change Password</h3>
            <a href="#" class="close-modal">&times;</a>
        </div>
        <form action="profile.php" method="POST" class="edit-profile-form">
            <input type="hidden" name="action" value="change_password">
            
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Current Password</label>
                <input type="password" name="current_password" placeholder="Enter current password" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-key"></i> New Password</label>
                <input type="password" name="new_password" placeholder="Enter new password (min 6 characters)" required minlength="6">
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-check-circle"></i> Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            </div>
            
            <div class="modal-footer">
                <a href="#" class="btn btn-outline" style="width: auto; text-decoration: none; display: flex; align-items: center; justify-content: center;">Cancel</a>
                <button type="submit" class="btn" style="width: auto;">Change Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden element to pass chart data to JavaScript -->
<script type="application/json" id="chartData">
<?php echo json_encode($chart_data); ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/profile.js"></script>
