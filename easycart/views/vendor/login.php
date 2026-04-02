
    <div class="auth-page-wrapper">
        <div class="auth-container">
            <!-- Left Side: Branding/Image -->
            <div class="auth-left">
                <div class="auth-content">
                    <h2>Vendor Portal</h2>
                    <p>Manage your products, track orders, and grow your sales.</p>
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
                    <button class="active">Vendor Login</button>
                    <a href="<?= BASE_URL ?>vendor/register" style="text-decoration:none;"><button>Sign Up</button></a>
                </div>

                <!-- Login Form -->
                <form action="<?= BASE_URL ?>vendor/login-process" method="POST" class="auth-form">
                    <h3>Sign In to Vendor Panel</h3>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn">Sign In</button>
                </form>
            </div>
        </div>
    </div>
