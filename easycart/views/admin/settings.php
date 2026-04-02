<?php
// views/admin/settings.php
?>
<div class="admin-container">
    <div class="dashboard-header" style="text-align: left; padding: 1.5rem 2rem;">
        <h1 style="font-size: 1.8rem; margin: 0;"><?= htmlspecialchars($page_title) ?></h1>
        <p style="font-size: 0.95rem; opacity: 0.9;">Manage Storefront Appearance and Theme</p>
    </div>

    <div class="export-options" style="margin: 0 2rem;">
        <form action="<?= BASE_URL ?>admin/settings" method="POST" style="max-width: 800px; margin: 0 auto;">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Store Name</label>
                    <input type="text" name="store_name" value="<?= htmlspecialchars($settings['store_name'] ?? '') ?>" required style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Hero Banner Text</label>
                    <input type="text" name="hero_banner_text" value="<?= htmlspecialchars($settings['hero_banner_text'] ?? '') ?>" required style="width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Primary Color</label>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <input type="color" name="primary_color" value="<?= htmlspecialchars($settings['primary_color'] ?? '#0ea5e9') ?>" style="width: 50px; height: 50px; padding: 0; border: none; border-radius: 8px; cursor: pointer;">
                        <code style="background: #f1f5f9; padding: 0.5rem 1rem; border-radius: 4px;"><?= htmlspecialchars($settings['primary_color'] ?? '#0ea5e9') ?></code>
                    </div>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Secondary Color</label>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <input type="color" name="secondary_color" value="<?= htmlspecialchars($settings['secondary_color'] ?? '#f1f5f9') ?>" style="width: 50px; height: 50px; padding: 0; border: none; border-radius: 8px; cursor: pointer;">
                        <code style="background: #f1f5f9; padding: 0.5rem 1rem; border-radius: 4px;"><?= htmlspecialchars($settings['secondary_color'] ?? '#f1f5f9') ?></code>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 2rem; border-top: 1px solid #e2e8f0; padding-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">Save Settings</button>
            </div>
        </form>
    </div>
    
    <div style="margin: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
        <h3 style="margin-top: 0; display: flex; align-items: center; gap: 0.5rem;"><i class="fas fa-lightbulb" style="color: #eab308;"></i> Pro Tip</h3>
        <p style="margin-bottom: 0; color: #475569;">
            These settings directly affect the customer storefront. By saving changes here, the colors will be automatically injected as CSS variables into the global stylesheet rendering engine. 
        </p>
    </div>
</div>
