
<link rel="stylesheet" href="../css/wishlist.css">

    <div class="container">
        <div class="page-content layout-transparent">
            <h1 class="text-dark mb-4">Explore Collection <span id="headerCount" style="font-size: 1.5rem; color: #888; font-weight: 400;">(<?php echo count($filtered_products); ?>)</span></h1>
            
            <div class="shop-layout">
                <!-- Filters -->
                <aside class="filter-sidebar">
                    <form action="" method="GET" id="filterForm">
                        <!-- Preserve Search Query -->
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                        <!-- Hidden Page Input for Pagination -->
                        <input type="hidden" name="page" id="pageInput" value="<?php echo $page_num; ?>">

                        <div class="filter-group">
                            <h3>Categories</h3>
                            <?php 
                            // Use centralized $categories from productsdata.php
                            foreach($categories as $val => $data):
                            ?>
                            <label class="filter-option">
                                <input type="checkbox" name="category[]" value="<?php echo $val; ?>" <?php echo in_array($val, $selected_categories) ? 'checked' : ''; ?>>
                                <span class="checkmark"></span> <?php echo $data['name']; ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    
                        <div class="filter-group">
                            <h3>Brands</h3>
                            <?php 
                            // Use centralized $brands from productsdata.php
                            foreach($brands as $val => $label):
                            ?>
                            <label class="filter-option">
                                <input type="checkbox" name="brand[]" value="<?php echo $val; ?>" <?php echo in_array($val, $selected_brands) ? 'checked' : ''; ?>>
                                <span class="checkmark"></span> <?php echo $label; ?>
                            </label>
                            <?php endforeach; ?>
                        </div>

                        <a href="products.php" class="btn-clear-filters">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </form>
                </aside>

                <!-- Products Grid -->
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding: 0.5rem 1rem; background: #fff; border-radius: 12px; border: 1px solid #eee; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <span style="color: #444; font-weight: 500;">Showing <strong id="productCount" style="color: var(--accent-color);"><?php echo count($filtered_products); ?></strong> matching products</span>
                    </div>
                    <div class="products-grid" id="productGrid">
                    <?php 
                    // Initial Render
                    renderProductsGrid($paginated_products, $page_num, $total_pages);
                    ?>
                </div>
            </div>
        </div>
    </div>
    </div>
    
    <script src="../js/products.js?v=<?php echo time(); ?>"></script>
