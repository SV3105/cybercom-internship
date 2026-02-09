<div class="admin-container">
    <div class="dashboard-header" style="text-align: left; padding: 1.5rem 2rem;">
        <h1 style="font-size: 1.8rem; margin: 0 0 0.5rem;"><?= htmlspecialchars($title) ?></h1>
        <p style="font-size: 0.95rem; opacity: 0.9; margin: 0;">Add a new administrator to manage the store.</p>
    </div>

    <div class="admin-grid" style="margin: 0 2rem 2rem; max-width: 600px;">
        <div class="card">
            <form action="<?= BASE_URL ?>admin/process-create-admin" method="POST">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Full Name</label>
                    <input type="text" name="name" required class="admin-input" placeholder="e.g. John Doe" 
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email Address</label>
                    <input type="email" name="email" required class="admin-input" placeholder="e.g. john@example.com"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Password</label>
                    <input type="password" name="password" required class="admin-input" placeholder="Min 8 characters"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>

                <div class="form-group" style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Confirm Password</label>
                    <input type="password" name="confirm_password" required class="admin-input" placeholder="Repeat password"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn-primary" style="padding: 0.75rem 2rem; border-radius: 8px; font-weight: 600;">
                        Create Admin Account
                    </button>
                    <a href="<?= BASE_URL ?>admin" class="btn-secondary" style="padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; text-align: center;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.admin-input:focus {
    border-color: #3b82f6 !important;
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>
