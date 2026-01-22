<?php
$title = "My Orders - EasyCart";
$base_path = "../";
$page = "orders";
$extra_css = "orders.css";
include '../includes/header.php';
?>

    <div class="container">
        <div class="page-content">
            <h1>My Orders</h1>
            <div class="orders-list">
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #12345</h3>
                            <p>Placed on: January 15, 2026</p>
                        </div>
                        <div>
                            <span class="status-badge status-delivered">Delivered</span>
                        </div>
                    </div>
                    <p>Items: Wireless Headphones, Smart Watch</p>
                    <p><strong>Total: $299.98</strong></p>
                </div>

                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #12344</h3>
                            <p>Placed on: January 10, 2026</p>
                        </div>
                        <div>
                             <span class="status-badge status-transit">In Transit</span>
                        </div>
                    </div>
                    <p>Items: Laptop Stand, Mechanical Keyboard</p>
                    <p><strong>Total: $179.98</strong></p>
                </div>

                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #12343</h3>
                            <p>Placed on: January 5, 2026</p>
                        </div>
                        <div>
                             <span class="status-badge status-processing">Processing</span>
                        </div>
                    </div>
                    <p>Items: USB-C Hub, Gaming Mouse</p>
                    <p><strong>Total: $99.98</strong></p>
                </div>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>