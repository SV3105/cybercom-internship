
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

        <!-- Profile Layout Container -->
        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem; max-width: 1400px; margin: 0 auto;">
            
            <!-- Left Sidebar Navigation -->
            <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); height: fit-content; position: sticky; top: 20px;">
                <!-- Account Header -->
                <div style="padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0; margin-bottom: 1rem;">
                    <h3 style="margin: 0 0 0.25rem; font-size: 0.875rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Account</h3>
                    <p style="margin: 0; font-size: 1rem; color: #1e293b; font-weight: 600;"><?php echo htmlspecialchars($user['name']); ?></p>
                </div>

                <!-- Navigation Menu -->
                <nav style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <a href="#overview" class="profile-nav-link active" data-section="overview" style="padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; color: #0891b2; background: #ecfeff; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
                        <i class="fas fa-th-large" style="width: 16px;"></i>
                        <span>Overview</span>
                    </a>
                    
                    <a href="#quick-stats" class="profile-nav-link" data-section="quick-stats" style="padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
                        <i class="fas fa-chart-pie" style="width: 16px;"></i>
                        <span>Quick Stats</span>
                    </a>
                    
                    <a href="#spending-history" class="profile-nav-link" data-section="spending-history" style="padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
                        <i class="fas fa-chart-line" style="width: 16px;"></i>
                        <span>Spending History</span>
                    </a>
                    
                    <a href="#recent-activity" class="profile-nav-link" data-section="recent-activity" style="padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
                        <i class="fas fa-history" style="width: 16px;"></i>
                        <span>Recent Activity</span>
                    </a>

                    <a href="#personal-info" class="profile-nav-link" data-section="personal-info" style="padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
                        <i class="fas fa-user-circle" style="width: 16px;"></i>
                        <span>Personal Info</span>
                    </a>

                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                        <p style="margin: 0 0 0.5rem; font-size: 0.75rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; padding: 0 1rem;">Quick Actions</p>
                        <a href="#edit-modal" class="profile-nav-link" style="padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
                            <i class="fas fa-edit" style="width: 16px;"></i>
                            <span>Edit Profile</span>
                        </a>
                        <a href="#password-modal" class="profile-nav-link" style="padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
                            <i class="fas fa-key" style="width: 16px;"></i>
                            <span>Change Password</span>
                        </a>
                        <a href="orders.php" class="profile-nav-link" style="padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
                            <i class="fas fa-box" style="width: 16px;"></i>
                            <span>View All Orders</span>
                        </a>
                        <a href="wishlist.php" class="profile-nav-link" style="padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
                            <i class="fas fa-heart" style="width: 16px;"></i>
                            <span>My Wishlist</span>
                        </a>
                    </div>
                </nav>
            </div>

            <!-- Main Content Area -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                
                <!-- Profile Header Card (Overview Section) -->
                <div id="overview" style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; scroll-margin-top: 20px;">
                    <div style="display: flex; align-items: center; gap: 2rem;">
                        <div style="width: 120px; height: 120px; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user" style="font-size: 3rem; color: #94a3b8;"></i>
                        </div>
                        <div>
                            <h2 style="margin: 0 0 0.5rem; font-size: 1.75rem; font-weight: 700; color: #1e293b;"><?php echo htmlspecialchars($user['name']); ?></h2>
                            <p style="margin: 0 0 0.25rem; color: #64748b;"><i class="fas fa-envelope" style="margin-right: 0.5rem;"></i><?php echo htmlspecialchars($user['email']); ?></p>
                            <p style="margin: 0; color: #64748b;"><i class="fas fa-calendar-alt" style="margin-right: 0.5rem;"></i>Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>
                    <a href="#edit-modal" style="padding: 0.75rem 1.5rem; background: #0891b2; color: white; text-decoration: none; border-radius: 8px; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s;">
                        <i class="fas fa-edit"></i>
                        <span>EDIT PROFILE</span>
                    </a>
                </div>

                <!-- Quick Stats Grid -->
                <div id="quick-stats" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; scroll-margin-top: 20px;">
                    <!-- Orders Card -->
                    <a href="orders.php" style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-decoration: none; transition: all 0.3s; border: 2px solid transparent;">
                        <div style="width: 60px; height: 60px; background: #eff6ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <i class="fas fa-box" style="font-size: 1.75rem; color: #3b82f6;"></i>
                        </div>
                        <h3 style="margin: 0 0 0.5rem; font-size: 1.5rem; font-weight: 700; color: #1e293b;"><?php echo $total_orders; ?></h3>
                        <p style="margin: 0; color: #64748b; font-weight: 500;">Orders</p>
                        <p style="margin: 0.5rem 0 0; color: #94a3b8; font-size: 0.875rem;">Check your order status</p>
                    </a>

                    <!-- Wishlist Card -->
                    <a href="wishlist.php" style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-decoration: none; transition: all 0.3s; border: 2px solid transparent;">
                        <div style="width: 60px; height: 60px; background: #fef2f2; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <i class="fas fa-heart" style="font-size: 1.75rem; color: #ef4444;"></i>
                        </div>
                        <h3 style="margin: 0 0 0.5rem; font-size: 1.5rem; font-weight: 700; color: #1e293b;">0</h3>
                        <p style="margin: 0; color: #64748b; font-weight: 500;">Wishlist</p>
                        <p style="margin: 0.5rem 0 0; color: #94a3b8; font-size: 0.875rem;">All your curated products</p>
                    </a>

                    <!-- Total Spent Card -->
                    <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div style="width: 60px; height: 60px; background: #f0fdf4; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <i class="fas fa-wallet" style="font-size: 1.75rem; color: #22c55e;"></i>
                        </div>
                        <h3 style="margin: 0 0 0.5rem; font-size: 1.5rem; font-weight: 700; color: #1e293b;">₹<?php echo number_format($total_spent); ?></h3>
                        <p style="margin: 0; color: #64748b; font-weight: 500;">Total Spent</p>
                        <p style="margin: 0.5rem 0 0; color: #94a3b8; font-size: 0.875rem;">Lifetime spending</p>
                    </div>
                </div>

                <!-- Spending History Chart -->
                <div id="spending-history" style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); scroll-margin-top: 20px;">
                    <h3 style="margin: 0 0 1.5rem; font-size: 1.25rem; font-weight: 700; color: #1e293b;">Spending History</h3>
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

                <!-- Recent Activity -->
                <div id="recent-activity" style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); scroll-margin-top: 20px;">
                    <h3 style="margin: 0 0 1.5rem; font-size: 1.25rem; font-weight: 700; color: #1e293b;">Recent Activity</h3>
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

                <!-- Personal Information -->
                <div id="personal-info" style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); scroll-margin-top: 20px;">
                    <h3 style="margin: 0 0 1.5rem; font-size: 1.25rem; font-weight: 700; color: #1e293b;">Personal Information</h3>
                    <div style="display: grid; gap: 1rem;">
                        <div style="padding: 1rem; background: #f8fafc; border-radius: 8px; display: grid; grid-template-columns: 150px 1fr; gap: 1rem;">
                            <span style="color: #64748b; font-weight: 500;">Full Name</span>
                            <span style="color: #1e293b; font-weight: 600;"><?php echo htmlspecialchars($user['name']); ?></span>
                        </div>
                        <div style="padding: 1rem; background: white; border-radius: 8px; display: grid; grid-template-columns: 150px 1fr; gap: 1rem;">
                            <span style="color: #64748b; font-weight: 500;">Email</span>
                            <span style="color: #1e293b; font-weight: 600;"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div style="padding: 1rem; background: #f8fafc; border-radius: 8px; display: grid; grid-template-columns: 150px 1fr; gap: 1rem;">
                            <span style="color: #64748b; font-weight: 500;">Phone</span>
                            <span style="color: #1e293b; font-weight: 600;"><?php echo htmlspecialchars($user['phone']); ?></span>
                        </div>
                        <div style="padding: 1rem; background: white; border-radius: 8px; display: grid; grid-template-columns: 150px 1fr; gap: 1rem;">
                            <span style="color: #64748b; font-weight: 500;">Location</span>
                            <span style="color: #1e293b; font-weight: 600;"><?php echo htmlspecialchars($user['location']); ?></span>
                        </div>
                        <div style="padding: 1rem; background: #f8fafc; border-radius: 8px; display: grid; grid-template-columns: 150px 1fr; gap: 1rem;">
                            <span style="color: #64748b; font-weight: 500;">Member Since</span>
                            <span style="color: #1e293b; font-weight: 600;"><?php echo date('F d, Y', strtotime($user['created_at'])); ?></span>
                        </div>
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
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
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

<style>
html {
    scroll-behavior: smooth;
}

.profile-nav-link:hover {
    background: #f1f5f9 !important;
    color: #1e293b !important;
}

.profile-nav-link.active {
    background: #ecfeff !important;
    color: #0891b2 !important;
}

a[href="orders.php"]:hover,
a[href="wishlist.php"]:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
    border-color: #0891b2 !important;
}
</style>

<script>
// Smooth scroll and active navigation highlighting
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.profile-nav-link[data-section]');
    
    // Handle click on navigation links
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all links
            navLinks.forEach(l => {
                l.classList.remove('active');
                l.style.background = '';
                l.style.color = '#64748b';
            });
            
            // Add active class to clicked link
            this.classList.add('active');
            this.style.background = '#ecfeff';
            this.style.color = '#0891b2';
        });
    });
    
    // Highlight active section on scroll
    const sections = document.querySelectorAll('[id]');
    const observerOptions = {
        root: null,
        rootMargin: '-100px 0px -66%',
        threshold: 0
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                const correspondingLink = document.querySelector(`a[data-section="${id}"]`);
                
                if (correspondingLink) {
                    navLinks.forEach(l => {
                        l.classList.remove('active');
                        l.style.background = '';
                        l.style.color = '#64748b';
                    });
                    
                    correspondingLink.classList.add('active');
                    correspondingLink.style.background = '#ecfeff';
                    correspondingLink.style.color = '#0891b2';
                }
            }
        });
    }, observerOptions);
    
    sections.forEach(section => {
        if (section.hasAttribute('id') && section.id) {
            observer.observe(section);
        }
    });
});
</script>
