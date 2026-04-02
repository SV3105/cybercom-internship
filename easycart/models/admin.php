<?php
// models/Admin.php

class Admin {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getPDO() {
        return $this->pdo;
    }

    /**
     * Authenticate an admin user
     */
    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }
        return false;
    }

    /**
     * Check if a user ID has admin privileges
     */
    public function isAdmin($adminId) {
        $stmt = $this->pdo->prepare("SELECT id FROM admins WHERE id = ?");
        $stmt->execute([$adminId]);
        return (bool)$stmt->fetchColumn();
    }

    public function getDashboardStats() {
        $stats = [];
        try {
            // Total users
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
            $stats['total_users'] = $stmt->fetchColumn();
            
            // Total vendors
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM vendors");
            $stats['total_vendors'] = $stmt->fetchColumn();
            
            // Recent users
            $stmt = $this->pdo->query("
                SELECT id, name, email, created_at 
                FROM users 
                ORDER BY created_at DESC 
                LIMIT 5
            ");
            $stats['recent_users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Recent vendors
            $stmt = $this->pdo->query("
                SELECT id, name, store_name, email, created_at 
                FROM vendors 
                ORDER BY created_at DESC 
                LIMIT 5
            ");
            $stats['recent_vendors'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Dashboard stats error: " . $e->getMessage());
        }
        return $stats;
    }

    public function getUsers($limit = 10, $offset = 0) {
        $stmt = $this->pdo->prepare("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalUsersCount() {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    public function getVendors($limit = 10, $offset = 0) {
        $stmt = $this->pdo->prepare("SELECT id, name, store_name, email, created_at FROM vendors ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalVendorsCount() {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM vendors")->fetchColumn();
    }

    public function getSiteSettings() {
        $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM site_settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function updateSiteSettings($settings) {
        $stmt = $this->pdo->prepare("
            INSERT INTO site_settings (setting_key, setting_value, updated_at) 
            VALUES (?, ?, NOW()) 
            ON CONFLICT (setting_key) 
            DO UPDATE SET setting_value = EXCLUDED.setting_value, updated_at = NOW()
        ");
        
        $success = true;
        foreach ($settings as $key => $value) {
            if (!$stmt->execute([$key, $value])) {
                $success = false;
            }
        }
        return $success;
    }

    /**
     * Create a new administrator account
     */
    public function createAdmin($name, $email, $password) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
            return $stmt->execute([$name, $email, $hashedPassword]);
        } catch (PDOException $e) {
            error_log("Admin creation error: " . $e->getMessage());
            return false;
        }
    }
}
?>
