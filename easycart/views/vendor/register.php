
    <div class="auth-page-wrapper">
        <div class="auth-container">
            <!-- Left Side: Branding/Image -->
            <div class="auth-left">
                <div class="auth-content">
                    <h2>Become a Vendor</h2>
                    <p>Start selling your products on our platform today.</p>
                </div>
            </div>

            <!-- Right Side: Forms -->
            <div class="auth-right">
                <!-- Flash Messages -->
                <?php $flash = getFlash(); if ($flash): ?>
                    <div style="padding: 10px; margin-bottom: 15px; border-radius: 4px; background-color: <?= $flash['type'] === 'error' ? '#f8d7da' : '#d4edda' ?>; color: <?= $flash['type'] === 'error' ? '#721c24' : '#155724' ?>;">
                        <?= htmlspecialchars($flash['message']) ?>
                    </div>
                <?php endif; ?>
                
                <div class="auth-toggle">
                    <a href="<?= BASE_URL ?>vendor/login" style="text-decoration:none;"><button>Vendor Login</button></a>
                    <button class="active">Sign Up</button>
                </div>

                <!-- Signup Form -->
                <form action="<?= BASE_URL ?>vendor/register-process" method="POST" class="auth-form">
                    <h3>Create Vendor Account</h3>
                    
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Full Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="store_name" placeholder="Store Name" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn">Register</button>
                </form>
            </div>
        </div>
    </div>
