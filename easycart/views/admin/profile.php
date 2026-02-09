<div class="profile-container">
    <div class="dashboard-card profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h2 class="profile-name">Admin Profile</h2>
                <p class="profile-subtitle">Manage your administrative account settings.</p>
            </div>
        </div>

        <div class="profile-info-grid">
            <div class="info-group">
                <label class="info-label">Account Name</label>
                <div class="info-value"><?= htmlspecialchars($user['name'] ?? 'Administrator') ?></div>
            </div>

            <div class="info-group">
                <label class="info-label">Email Address</label>
                <div class="info-value"><?= htmlspecialchars($user['email'] ?? 'admin@easycart.com') ?></div>
            </div>

            <div class="info-group">
                <label class="info-label">Role</label>
                <div class="role-badge">
                    <i class="fas fa-shield-alt"></i>
                    Super Admin
                </div>
            </div>
            
            <hr class="profile-divider">

            <div class="profile-actions">
                <a href="<?= BASE_URL ?>logout" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Sign Out
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.profile-container {
    padding: 1rem;
    animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.info-group {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 12px;
    border: 1px solid #f1f5f9;
}
</style>
