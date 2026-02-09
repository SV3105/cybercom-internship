<!-- views/admin/export.php -->
<!-- Main Content Area -->

            <div style="display: flex; flex-direction: column; gap: 2rem;">
                
                <!-- Page Header Removed -->

                <!-- Export Options Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    
                    <!-- All Products -->
                    <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center; border: 2px solid #e2e8f0; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.borderColor='#0891b2'; this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 30px rgba(8, 145, 178, 0.2)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)';">
                        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                            <i class="fas fa-box-open" style="font-size: 2rem; color: #0891b2;"></i>
                        </div>
                        <h3 style="margin: 0 0 0.75rem; font-size: 1.2rem; font-weight: 700; color: #0f172a; font-family: 'Montserrat', sans-serif;">All Products</h3>
                        <p style="margin: 0 0 1.5rem; color: #64748b; font-size: 0.9rem; line-height: 1.6;">Export complete product catalog with all details</p>
                        <a href="<?= BASE_URL ?>admin/exportdownload" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 0.9rem; transition: all 0.3s; box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3); text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-download"></i>
                            Export All
                        </a>
                    </div>

                    <!-- Low Stock -->
                    <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center; border: 2px solid #e2e8f0; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.borderColor='#f59e0b'; this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 30px rgba(245, 158, 11, 0.2)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)';">
                        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #f59e0b;"></i>
                        </div>
                        <h3 style="margin: 0 0 0.75rem; font-size: 1.2rem; font-weight: 700; color: #0f172a; font-family: 'Montserrat', sans-serif;">Low Stock Only</h3>
                        <p style="margin: 0 0 1.5rem; color: #64748b; font-size: 0.9rem; line-height: 1.6;">Export products with stock quantity below 10</p>
                        <a href="<?= BASE_URL ?>admin/exportdownload?filter=low_stock" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 0.9rem; transition: all 0.3s; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-download"></i>
                            Export Low Stock
                        </a>
                    </div>

                    <!-- Featured Products -->
                    <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center; border: 2px solid #e2e8f0; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.borderColor='#8b5cf6'; this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 30px rgba(139, 92, 246, 0.2)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)';">
                        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                            <i class="fas fa-star" style="font-size: 2rem; color: #8b5cf6;"></i>
                        </div>
                        <h3 style="margin: 0 0 0.75rem; font-size: 1.2rem; font-weight: 700; color: #0f172a; font-family: 'Montserrat', sans-serif;">Featured Products</h3>
                        <p style="margin: 0 0 1.5rem; color: #64748b; font-size: 0.9rem; line-height: 1.6;">Export only featured/highlighted products</p>
                        <a href="<?= BASE_URL ?>admin/exportdownload?filter=featured" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 0.9rem; transition: all 0.3s; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3); text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-download"></i>
                            Export Featured
                        </a>
                    </div>

                    <!-- Template -->
                    <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center; border: 2px solid #e2e8f0; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.borderColor='#64748b'; this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 30px rgba(100, 116, 139, 0.2)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)';">
                        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                            <i class="fas fa-file-alt" style="font-size: 2rem; color: #64748b;"></i>
                        </div>
                        <h3 style="margin: 0 0 0.75rem; font-size: 1.2rem; font-weight: 700; color: #0f172a; font-family: 'Montserrat', sans-serif;">CSV Template</h3>
                        <p style="margin: 0 0 1.5rem; color: #64748b; font-size: 0.9rem; line-height: 1.6;">Download empty CSV template with sample data</p>
                        <a href="<?= BASE_URL ?>admin/exportdownload?template=true" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 0.9rem; transition: all 0.3s; box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3); text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-download"></i>
                            Download Template
                        </a>
                    </div>
                </div>

                <!-- Export Information -->
                <div style="background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%); border-radius: 12px; padding: 2rem; border-left: 4px solid #0891b2; box-shadow: 0 2px 8px rgba(8, 145, 178, 0.2);">
                    <h3 style="margin: 0 0 1rem; font-size: 1.1rem; font-weight: 700; color: #0e7490; display: flex; align-items: center; gap: 0.5rem; font-family: 'Montserrat', sans-serif;">
                        <i class="fas fa-info-circle"></i>
                        Export Information
                    </h3>
                    <ul style="margin: 0; padding-left: 1.5rem; color: #155e75; line-height: 1.8;">
                        <li>All exports are generated in <strong>CSV format</strong></li>
                        <li>Files include headers with column names</li>
                        <li>Timestamps are added to filenames automatically</li>
                        <li>Category and brand are exported as <strong>slugs</strong></li>
                        <li>Boolean values (is_featured) are exported as <strong>true/false</strong></li>
                        <li>Prices are exported in <strong>INR (₹)</strong></li>
                    </ul>
                </div>

                <!-- CSV Format Preview -->
                <div style="background: white; border-radius: 12px; padding: 2.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <h3 style="margin: 0 0 1.5rem; font-size: 1.3rem; font-weight: 700; color: #0f172a; font-family: 'Montserrat', sans-serif;">📋 CSV Format Preview</h3>
                    
                    <div style="background: #0f172a; padding: 2rem; border-radius: 12px; overflow-x: auto; box-shadow: 0 4px 15px rgba(15, 23, 42, 0.3);">
                        <pre style="margin: 0;"><code style="color: #e0f2fe; font-family: 'Courier New', monospace; font-size: 0.9rem; line-height: 1.8;">sku,name,price,old_price,category,brand,description,image,is_featured,rating,review_count,stock_qty
LAPTOP001,Dell Inspiron 15,45000,50000,electronics,dell,15.6" laptop with 8GB RAM,laptop.jpg,false,4.5,120,25
SHIRT001,Cotton T-Shirt,499,699,fashion,nike,Comfortable cotton tee,shirt.jpg,true,4.2,85,100</code></pre>
                    </div>

                    <div style="margin-top: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px; border-left: 4px solid #10b981;">
                        <h4 style="margin: 0 0 1rem; font-size: 1rem; font-weight: 700; color: #0f172a; font-family: 'Montserrat', sans-serif;">💡 Usage Tips</h4>
                        <ul style="margin: 0; padding-left: 1.5rem; color: #64748b; line-height: 1.8;">
                            <li>Use exported CSV as a template for bulk imports</li>
                            <li>Modify data in Excel or Google Sheets</li>
                            <li>Re-import to update products in bulk</li>
                            <li>Keep SKUs unique to avoid conflicts</li>
                            <li>Ensure category and brand slugs match existing ones</li>
                        </ul>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center;">
                    <h3 style="margin: 0 0 1.5rem; font-size: 1.1rem; font-weight: 700; color: #0f172a; font-family: 'Montserrat', sans-serif;">Need to Import Products?</h3>
                    <p style="margin: 0 0 1.5rem; color: #64748b;">After exporting and modifying your CSV, you can import it back to update products.</p>
                    <a href="<?= BASE_URL ?>admin/import" style="display: inline-flex; align-items: center; gap: 0.75rem; background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%); color: white; padding: 1rem 2rem; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 1rem; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3); text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-file-import"></i>
                        Go to Import Page
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-nav-link:hover {
    background: #f1f5f9 !important;
    color: #1e293b !important;
}

.admin-nav-link.active {
    background: #ecfeff !important;
    color: #0891b2 !important;
}

@media (max-width: 1024px) {
    .container .page-content > div {
        grid-template-columns: 1fr !important;
    }
    
    .container .page-content > div > div:first-child {
        position: static !important;
    }
}
</style>
