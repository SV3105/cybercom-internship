<?php
// models/Cart.php
// Cart model - handles shopping cart database operations and calculations

class Cart {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Get active cart ID for user
     */
    public function getActiveCartId($user_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM sales_cart WHERE user_id = ? AND is_active = TRUE");
            $stmt->execute([$user_id]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Get Cart ID Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Load cart items from database
     */
    public function loadCartFromDb($user_id) {
        $db_cart = [];
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.product_id, p.quantity 
                FROM sales_cart_products p
                JOIN sales_cart c ON p.cart_id = c.id
                WHERE c.user_id = ? AND c.is_active = TRUE
            ");
            $stmt->execute([$user_id]);
            while ($row = $stmt->fetch()) {
                $db_cart[$row['product_id']] = $row['quantity'];
            }
        } catch (PDOException $e) {
            error_log("Cart Load Error: " . $e->getMessage());
        }
        return $db_cart;
    }

    /**

     * Sync session cart to database
     */
    public function syncCartToDb($user_id, $cart, $session_id_val, $promo_code = null, $shipping_method = null) {
        try {
            $cart_id = false;
            
            // 1. Get or Create Active Cart
            if ($user_id) {
                // Logged in: Match by User ID
                $stmt = $this->pdo->prepare("SELECT id FROM sales_cart WHERE user_id = ? AND is_active = TRUE");
                $stmt->execute([$user_id]);
                $cart_id = $stmt->fetchColumn();
                
                // If we found a cart, update its session_id to current one
                if ($cart_id) {
                    $this->pdo->prepare("UPDATE sales_cart SET session_id = ? WHERE id = ?")->execute([$session_id_val, $cart_id]);
                }
            } else {
                // Guest: Match by Session ID
                $stmt = $this->pdo->prepare("SELECT id FROM sales_cart WHERE session_id = ? AND is_active = TRUE AND user_id IS NULL");
                $stmt->execute([$session_id_val]);
                $cart_id = $stmt->fetchColumn();
            }
    
            if (!$cart_id) {
                if (empty($cart)) return; // Don't create empty cart record
                
                if ($user_id) {
                    $stmtCreate = $this->pdo->prepare("INSERT INTO sales_cart (user_id, session_id, is_active, created_at) VALUES (?, ?, TRUE, NOW()) RETURNING id");
                    $stmtCreate->execute([$user_id, $session_id_val]);
                } else {
                    $stmtCreate = $this->pdo->prepare("INSERT INTO sales_cart (session_id, is_active, created_at) VALUES (?, TRUE, NOW()) RETURNING id");
                    $stmtCreate->execute([$session_id_val]);
                }
                $cart_id = $stmtCreate->fetchColumn();
            } 
    
            // 2. Sync Items & Calculate Total
            $this->pdo->prepare("DELETE FROM sales_cart_products WHERE cart_id = ?")->execute([$cart_id]);
    
            $calculated_subtotal = 0.00;
            $items_count = 0;
    
            if (!empty($cart)) {
                $stmtInsert = $this->pdo->prepare("INSERT INTO sales_cart_products (cart_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmtPrice = $this->pdo->prepare("SELECT price FROM catalog_product_entity WHERE entity_id = ?");
                
                foreach ($cart as $p_id => $qty) {
                     if ($qty > 0) {
                        $stmtPrice->execute([$p_id]);
                        $price = $stmtPrice->fetchColumn();
                        if ($price !== false) {
                            // Sanitize price
                            $price_val = (float)str_replace(',', '', $price);
                            $stmtInsert->execute([$cart_id, $p_id, $qty, $price_val]);
                            
                            $calculated_subtotal += ($price_val * $qty);
                            $items_count += $qty;
                        }
                    }
                }
            }
            
            // --- Calculate Breakdown ---
            
            // Shipping
            $shipping_options = [
                'standard' => 40,
                'express' => min(80, $calculated_subtotal * 0.10),
                'white_glove' => min(150, $calculated_subtotal * 0.05),
                'freight' => max(250, $calculated_subtotal * 0.03)
            ];
            
            // Determine method: Use passed method if valid, otherwise use default logic
            $selected_method = $shipping_method;
            if (!$selected_method || !isset($shipping_options[$selected_method])) {
                 // Default logic (from calculateTotals)
                 if ($calculated_subtotal <= 300) {
                     $selected_method = 'express';
                 } else {
                     $selected_method = 'freight';
                 }
            } else {
                // Auto-Upgrade Logic: Ensure method is valid for cart size
                // 1. If Cart > 300, Express isn't allowed (unless user really wants 10% capped at 80? No, express logic changes)
                // Actually, Express logic is min(80, 10%). If subtotal is 1000, 10% is 100, capped at 80.
                // But UI defaults to Freight for > 300.
                // Let's enforce the "Upgrade" if user is on a "small cart" method but now has a "big cart"
                
                if ($calculated_subtotal > 300) {
                    // Valid methods for > 300 are Freight, White Glove, Standard.
                    // 'Express' is typically for small orders in our logic.
                    // If user had 'express' selected, switch them to 'freight' automatically
                    if ($selected_method === 'express') {
                        $selected_method = 'freight';
                    }
                }
            }
            
            $shipping_cost = $shipping_options[$selected_method];
            if (empty($cart)) $shipping_cost = 0;
            
            // Smart Discount
            $smart_discount = 0;
            if ($items_count > 0) {
                $discount_percent = min($items_count, 100); 
                $smart_discount = $calculated_subtotal * ($discount_percent / 100);
            }
            
            // Promo Discount
            $promo_discount = 0;
            if ($promo_code) {
                // Fetch from DB
                $stmtCoupon = $this->pdo->prepare("SELECT percentage FROM sales_coupons WHERE promo_code = ?");
                $stmtCoupon->execute([$promo_code]);
                $coupon_percent = $stmtCoupon->fetchColumn();

                if ($coupon_percent !== false) {
                     $percent = (int)$coupon_percent;
                     // Promo applies to subtotal + shipping (per existing logic)
                     $base_for_promo = $calculated_subtotal + $shipping_cost;
                     $promo_discount = $base_for_promo * ($percent / 100);
                     
                     // If promo applied, smart discount is overridden
                     if ($promo_discount > 0) {
                         $smart_discount = 0;
                     }
                } else {
                    $promo_code = null; // Invalid code
                }
            }
            
            $total_discount = $smart_discount + $promo_discount;
            
            // Tax
            $tax_amount = ($calculated_subtotal - $total_discount + $shipping_cost) * 0.18;
            $grand_total = ($calculated_subtotal - $total_discount) + $shipping_cost + $tax_amount;
            $grand_total = max(0, $grand_total);

            // Save All
            $stmtUpdate = $this->pdo->prepare("UPDATE sales_cart SET grand_total = ?, subtotal = ?, discount_amount = ?, tax_amount = ?, shipping_amount = ?, coupon_code = ? WHERE id = ?");
            $stmtUpdate->execute([$grand_total, $calculated_subtotal, $total_discount, $tax_amount, $shipping_cost, $promo_code, $cart_id]);
    
        } catch (PDOException $e) {
            error_log("Cart Sync Error: " . $e->getMessage());
        }
    }
    
    /**
     * Calculate cart totals, discounts, shipping, tax
     */
    public function calculateTotals($cart_items, $products_data, $shipping_method = null, $promo_code = null) {
        $subtotal = 0;
        foreach($cart_items as $p_id => $qty) {
            foreach($products_data as $p) {
                if($p['id'] == $p_id) {
                    $price_val = (float)str_replace(',', '', $p['price']);
                    $subtotal += $price_val * $qty;
                    break;
                }
            }
        }
        
        // Shipping Logic
        $shipping_options = [
            'standard' => 40,
            'express' => min(80, $subtotal * 0.10),
            'white_glove' => min(150, $subtotal * 0.05),
            'freight' => max(250, $subtotal * 0.03)
        ];
        
        // ... (Shipping Method Logic Omitted for brevity, kept same) ...
        // Re-implement shipping selection logic briefly to keep context valid if replaced fully
        $selected_method = $shipping_method;
        if ($selected_method === null) {
            $selected_method = ($subtotal <= 300) ? 'express' : 'freight';
        } else {
            if ($subtotal <= 300) {
                if ($selected_method !== 'express') $selected_method = 'express';
            } else {
                if ($selected_method !== 'white_glove' && $selected_method !== 'freight') $selected_method = 'freight';
            }
        }
        
        $shipping_cost = isset($shipping_options[$selected_method]) ? $shipping_options[$selected_method] : 40;
        if (empty($cart_items)) {
            $shipping_cost = 0;
            $shipping_options = array_map(function() { return 0; }, $shipping_options);
        }
        
        // Discounts
        $smart_discount = 0;
        $reason = "";
        $item_count = array_sum($cart_items);
        if ($item_count > 0) {
            $discount_percent = min($item_count, 100); 
            $smart_discount = $subtotal * ($discount_percent / 100);
            $reason = "Quantity Discount ({$discount_percent}% off)";
        }
        
        $promo_discount = 0;
        $promo_message = "";
        if ($promo_code) {
            // DB Fetch
            // Note: calculateTotals is sometimes called without full DB context check in pure utilities, 
            // but here $this->pdo is available.
            try {
                $stmtC = $this->pdo->prepare("SELECT percentage, promo_message FROM sales_coupons WHERE promo_code = ?");
                $stmtC->execute([$promo_code]);
                $coupon = $stmtC->fetch();
                
                if ($coupon) {
                     $percent = (int)$coupon['percentage'];
                     $base_for_promo = $subtotal + $shipping_cost;
                     $promo_discount = $base_for_promo * ($percent / 100);
                     $promo_message = $coupon['promo_message']; // Use DB message
                     
                     if ($promo_discount > 0) {
                         $smart_discount = 0;
                         $reason = ""; 
                     }
                }
            } catch (PDOException $e) {
                // Ignore DB error, just no promo
            }
        }
        
        $tax = ($subtotal - $smart_discount + $shipping_cost) * 0.18;
        $total = ($subtotal - $smart_discount) + $shipping_cost + $tax - $promo_discount;
        $total = max(0, $total);
        
        return [
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping_cost,
            'tax' => $tax,
            'total' => $total,
            'shipping_options' => $shipping_options,
            'item_count' => $item_count,
            'smart_discount' => $smart_discount,
            'discount_reason' => $reason,
            'selected_method' => $selected_method,
            'promo_discount' => $promo_discount,
            'promo_code' => $promo_code,
            'promo_message' => $promo_message,
            'selected_method' => $selected_method ?? (($subtotal <= 300) ? 'express' : 'freight')
        ];
    }

    /**
     * Deactivate user's cart (used on logout)
     */
    public function deactivateUserCart($user_id) {
        try {
            // 1. Get Active Cart ID
            $stmt = $this->pdo->prepare("SELECT id FROM sales_cart WHERE user_id = ? AND is_active = TRUE");
            $stmt->execute([$user_id]);
            $cart_id = $stmt->fetchColumn();

            if ($cart_id) {
                // 2. Check if it has items
                $stmtCount = $this->pdo->prepare("SELECT COUNT(*) FROM sales_cart_products WHERE cart_id = ?");
                $stmtCount->execute([$cart_id]);
                $count = $stmtCount->fetchColumn();

                // 3. Only Deactivate if Empty
                if ($count == 0) {
                    $stmtUpdate = $this->pdo->prepare("UPDATE sales_cart SET is_active = FALSE WHERE id = ?");
                    $stmtUpdate->execute([$cart_id]);
                }
            }
        } catch (PDOException $e) {
            error_log("Cart Deactivation Error: " . $e->getMessage());
        }
    }
}
?>
