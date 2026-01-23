<?php
$title = "My Orders - EasyCart";
$base_path = "../";
$page = "orders";
$extra_css = "orders.css";
include '../includes/products_data.php';
include '../includes/header.php';
?>

    <div class="container">
        <div class="page-content">
            <h1>My Orders</h1>
            <div class="orders-list">
                <?php foreach($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <p>Placed on: <?php echo $order['date']; ?></p>
                        </div>
                        <div>
                             <span class="status-badge status-<?php echo $order['status_code']; ?>"><?php echo $order['status']; ?></span>
                        </div>
                    </div>
                    <p>Items: <?php echo implode(', ', $order['items']); ?></p>
                    <p><strong>Total: ₹<?php echo $order['total']; ?></strong></p>
                    <div style="margin-top: 1rem; border-top: 1px solid #eee; padding-top: 1rem;">
                        <button onclick="openOrderModal(<?php echo $order['id']; ?>)" class="btn-text" style="color: var(--accent-color); font-weight: 600; background: none; border: none; cursor: pointer; padding: 0;">Track Order <i class="fas fa-arrow-right" style="font-size: 0.8rem;"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeOrderModal()">&times;</span>
            <div id="modalBody">
                <!-- Data will be injected here -->
            </div>
        </div>
    </div>

    <script>
        const ordersData = <?php echo json_encode($orders); ?>;

        function openOrderModal(orderId) {
            const order = ordersData.find(o => o.id == orderId);
            if (!order) return;

            const modalBody = document.getElementById('modalBody');
            const statusLevels = {
                'processing': 1,
                'transit': 2,
                'delivered': 3
            };
            const currentLevel = statusLevels[order.status_code] || 0;

            modalBody.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <div>
                        <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem;">Order Details</h1>
                        <p style="color: #666;">ID: #${order.id} | Placed on ${order.date}</p>
                    </div>
                    <span class="status-badge status-${order.status_code}">
                        ${order.status}
                    </span>
                </div>

                <div class="tracking-container">
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.1rem;">Tracking Status</h3>
                    <div class="tracking-steps">
                        <div class="tracking-step">
                            <div class="step-icon active"><i class="fas fa-check"></i></div>
                            <p class="step-label active">Ordered</p>
                        </div>
                        <div class="tracking-step">
                            <div class="step-icon ${currentLevel >= 1 ? 'active' : ''}"><i class="fas fa-box-open"></i></div>
                            <p class="step-label ${currentLevel >= 1 ? 'active' : ''}">Processed</p>
                        </div>
                        <div class="tracking-step">
                            <div class="step-icon ${currentLevel >= 2 ? 'active' : ''}"><i class="fas fa-shipping-fast"></i></div>
                            <p class="step-label ${currentLevel >= 2 ? 'active' : ''}">Shipped</p>
                        </div>
                        <div class="tracking-step">
                            <div class="step-icon ${currentLevel >= 3 ? 'active' : ''}"><i class="fas fa-home"></i></div>
                            <p class="step-label ${currentLevel >= 3 ? 'active' : ''}">Delivered</p>
                        </div>
                    </div>
                </div>

                <div class="items-list">
                    <h3 style="margin-bottom: 1rem; font-size: 1.1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">Items Ordered</h3>
                    ${order.items.map(item => `
                        <div class="item-row">
                            <span>${item}</span>
                            <span style="font-weight: 600;">1 x (Included)</span>
                        </div>
                    `).join('')}
                </div>

                <div style="text-align: right;">
                    <p style="font-size: 1.2rem; font-weight: 700;">Total Amount: ₹${order.total}</p>
                </div>
            `;

            document.getElementById('orderModal').style.display = 'block';
            document.body.style.overflow = 'hidden'; 
        }

        function closeOrderModal() {
            document.getElementById('orderModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('orderModal');
            if (event.target == modal) {
                closeOrderModal();
            }
        }
    </script>
<?php include '../includes/footer.php'; ?>