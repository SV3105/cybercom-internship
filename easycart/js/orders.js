function buyAgain(productIds) {
    if (!productIds || productIds.length === 0) return;

    if (!confirm('Add these items to your cart again?')) return;

    let promises = productIds.map(id => {
        const formData = new FormData();
        formData.append('action', 'update_qty');
        formData.append('product_id', id);
        formData.append('change', 1);

        return fetch('cart.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(res => res.json());
    });

    Promise.all(promises)
        .then(results => {
            const allSuccess = results.every(r => r.success);
            if (allSuccess) {
                if (confirm('Items added to cart! Go to cart now?')) {
                    window.location.href = 'cart.php';
                }
            } else {
                alert('Some items could not be added. Please try again.');
            }
        })
        .catch(err => {
            console.error('Error adding items to cart:', err);
            alert('An error occurred. Please try again.');
        });
}
