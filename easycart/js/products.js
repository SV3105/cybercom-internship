document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const navSearchForm = document.querySelector('.nav-search');
    const grid = document.getElementById('productGrid');
    
    if (!filterForm || !grid) return;

    const checkboxes = filterForm.querySelectorAll('input[type="checkbox"]');
    const hiddenSearchInput = filterForm.querySelector('input[name="search"]');
    const navSearchInput = navSearchForm ? navSearchForm.querySelector('input[name="search"]') : null;

    // Handle AJAX filtering
    function triggerUpdate() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        
        // Remove empty search to keep URL clean
        if (params.get('search') === '') {
            params.delete('search');
        }

        // Update Browser URL (History API)
        const newUrl = window.location.pathname + '?' + params.toString();
        window.history.pushState({}, '', newUrl);
        
        // Add AJAX flag
        params.append('ajax', '1');
        
        // Add a loading state
        grid.style.opacity = '0.5';
        
        fetch('products.php?' + params.toString())
            .then(response => response.text())
            .then(html => {
                grid.innerHTML = html;
                grid.style.opacity = '1';
                
                const count = grid.querySelectorAll('.product-card').length;
                const productCountElem = document.getElementById('productCount');
                const headerCountElem = document.getElementById('headerCount');
                if (productCountElem) productCountElem.textContent = count;
                if (headerCountElem) headerCountElem.textContent = `(${count})`;
            })
            .catch(error => {
                console.error('Error:', error);
                grid.style.opacity = '1';
            });
    }

    // Checkbox changes
    checkboxes.forEach(cb => {
        cb.addEventListener('change', triggerUpdate);
    });

    // Real-time Search Sync (Header -> Sidebar)
    if (navSearchInput && hiddenSearchInput) {
        navSearchInput.addEventListener('input', function() {
            hiddenSearchInput.value = this.value;
            // We don't triggerUpdate here to avoid too many requests while typing
            // unless the user stops typing for a bit (debounce would be better)
        });
    }

    // Header Search Submit
    if (navSearchForm) {
        navSearchForm.addEventListener('submit', function(e) {
            if (window.location.pathname.includes('products.php')) {
                e.preventDefault();
                hiddenSearchInput.value = navSearchInput.value;
                triggerUpdate();
            }
        });
    }
});


