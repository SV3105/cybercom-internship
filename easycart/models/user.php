<?php
// models/User.php
// User model - handles user authentication and database operations

class User {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("User Find Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Authenticate user
     */
    public function login($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user) {
            $passwordValid = false;
            
            // Check if hash matches (BCRYPT)
            if (password_verify($password, $user['password'])) {
                $passwordValid = true;
            } 
            // Fallback for legacy plain text passwords (remove this in production after migration!)
            elseif ($password === $user['password']) {
                $passwordValid = true;
            }
            
            if ($passwordValid) {
                return $user;
            }
        }
        
        return false;
    }
    
    /**
     * Register new user
     */
    public function register($data) {
        try {
            // Check if email already exists
            if ($this->findByEmail($data['email'])) {
                return ['success' => false, 'message' => 'Email already registered.'];
            }
            
            // Hash Password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert new user
            $stmtInsert = $this->pdo->prepare("INSERT INTO users (name, email, password, phone, location) VALUES (?, ?, ?, ?, ?)");
            
            if ($stmtInsert->execute([$data['name'], $data['email'], $hashedPassword, $data['phone'], $data['location']])) {
                return ['success' => true];
            } else {
                return ['success' => false, 'message' => 'Registration failed.'];
            }
            
            
        } catch (PDOException $e) {
            error_log("Signup Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'System error occurred.'];
        }
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile($userId, $name, $phone, $location) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET name = ?, phone = ?, location = ? WHERE id = ?");
            return $stmt->execute([$name, $phone, $location, $userId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Change user password
     */
    public function changePassword($userId, $newPassword) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$hashedPassword, $userId]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
