// public/js/search.js

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('input[name="search"]');
    const searchForm = document.querySelector('.nav-search');
    
    if (!searchInput || !searchForm) return; // Exit if not found
    
    // Create Results Container
    const resultsContainer = document.createElement('div');
    resultsContainer.className = 'search-results-dropdown';
    resultsContainer.style.display = 'none';
    searchForm.appendChild(resultsContainer);
    
    // Add CSS for dropdown dynamically
    const style = document.createElement('style');
    style.innerHTML = `
        .search-results-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
            max-height: 400px;
            overflow-y: auto;
        }
        .search-result-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            color: inherit;
        }
        .search-result-item:last-child { border-bottom: none; }
        .search-result-item:hover { background: #f9fafb; }
        .search-result-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 12px;
        }
        .search-result-info { flex: 1; }
        .search-result-title { font-size: 0.9rem; font-weight: 500; color: #1e293b; display: block; }
        .search-result-meta { font-size: 0.8rem; color: #64748b; }
        .search-result-price { font-weight: 600; color: #000; }
    `;
    document.head.appendChild(style);

    let debounceTimer;

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        
        clearTimeout(debounceTimer);
        
        if (query.length < 2) {
            resultsContainer.style.display = 'none';
            resultsContainer.innerHTML = '';
            return;
        }
        
        debounceTimer = setTimeout(() => {
            fetchResults(query);
        }, 300); // 300ms delay
    });
    
    // Hide when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchForm.contains(e.target)) {
            resultsContainer.style.display = 'none';
        }
    });
    
    // Re-open if clicking back into input with value
    searchInput.addEventListener('focus', () => {
        if (searchInput.value.trim().length >= 2 && resultsContainer.innerHTML !== '') {
            resultsContainer.style.display = 'block';
        }
    });

    async function fetchResults(query) {
        try {
            const response = await fetch(`${BASE_URL}productsearch?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            renderResults(data);
        } catch (error) {
            console.error('Search error:', error);
        }
    }
    
    function renderResults(products) {
        if (products.length === 0) {
            resultsContainer.style.display = 'none';
            return;
        }
        
        resultsContainer.innerHTML = products.map(p => `
            <a href="${BASE_URL}productdetails?id=${p.id}" class="search-result-item">
                <img src="${BASE_URL}images/${p.image}" alt="${p.title}" class="search-result-img">
                <div class="search-result-info">
                    <span class="search-result-title">${p.title}</span>
                    <span class="search-result-meta">${p.category}</span>
                </div>
                <span class="search-result-price">₹${p.price}</span>
            </a>
        `).join('');
        
        // Add "See all results" link if needed
        resultsContainer.innerHTML += `
            <a href="${BASE_URL}products?search=${encodeURIComponent(searchInput.value.trim())}" class="search-result-item" style="justify-content: center; color: #3b82f6; font-weight: 500;">
                See all results
            </a>
        `;
        
        resultsContainer.style.display = 'block';
    }
});
