function openOrderModal(orderId, ordersData) {
    const order = ordersData.find(o => o.id == orderId);
    if (!order) return;

    const modalBody = document.getElementById('modalBody');
    if (!modalBody) return;

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

    const modal = document.getElementById('orderModal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden'; 
    }
}

function closeOrderModal() {
    const modal = document.getElementById('orderModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    window.onclick = function(event) {
        const modal = document.getElementById('orderModal');
        if (event.target == modal) {
            closeOrderModal();
        }
    }
});
