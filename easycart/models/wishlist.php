<?php
// models/Wishlist.php

class Wishlist {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get Wishlist Items (Product IDs)
     */
    public function getWishlistIds($userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get Wishlist Count
     */
    public function getCount($userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Add to Wishlist
     */
    public function add($userId, $productId) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?) ON CONFLICT DO NOTHING");
            return $stmt->execute([$userId, $productId]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Remove from Wishlist
     */
    public function remove($userId, $productId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
            return $stmt->execute([$userId, $productId]);
        } catch (Exception $e) {
            return false;
        }
    }
}
