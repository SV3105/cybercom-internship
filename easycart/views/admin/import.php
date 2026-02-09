<!-- views/admin/import.php -->
<!-- Main Content Area -->

            <div style="display: flex; flex-direction: column; gap: 2rem;">
                
                <!-- Page Header Removed -->

                <!-- Important Notes -->
                <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 12px; padding: 2rem; border-left: 4px solid #f59e0b; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);">
                    <h3 style="margin: 0 0 1rem; font-size: 1.1rem; font-weight: 700; color: #92400e; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Important Notes
                    </h3>
                    <ul style="margin: 0; padding-left: 1.5rem; color: #78350f; line-height: 1.8;">
                        <li>CSV file must have headers matching the required format</li>
                        <li>Products with existing SKUs will be <strong>updated</strong></li>
                        <li>Products with new SKUs will be <strong>created</strong></li>
                        <li>Invalid rows will be skipped and reported</li>
                        <li>Maximum file size: <strong>5MB</strong></li>
                        <li>Supported currencies: INR (₹) and USD ($) - USD will be auto-converted</li>
                    </ul>
                </div>

                <!-- Upload Section -->
                <div style="background: white; border-radius: 12px; padding: 2.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <form id="importForm" enctype="multipart/form-data">
                        <div style="margin-bottom: 2rem;">
                            <input type="file" id="csv_file" name="csv_file" accept=".csv" required style="display: none;">
                            <label for="csv_file" id="fileLabel" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; padding: 3.5rem 2rem; border: 3px dashed #0891b2; border-radius: 16px; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); cursor: pointer; transition: all 0.3s ease;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <span style="font-size: 1.3rem; color: #0891b2; font-weight: 700; font-family: 'Montserrat', sans-serif;">Choose CSV File or Drag & Drop</span>
                                <span id="fileName" style="font-size: 0.85rem; color: #64748b; font-weight: 500;">Supported format: .csv (max 5MB)</span>
                            </label>
                        </div>
                        
                        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                            <button type="submit" id="importBtn" style="display: inline-flex; align-items: center; gap: 0.75rem; background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%); color: white; padding: 1rem 2rem; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 1rem; transition: all 0.3s ease; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3); text-transform: uppercase; letter-spacing: 0.5px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Import Products
                            </button>
                            
                            <a href="<?= BASE_URL ?>admin/exportdownload?template=true" style="display: inline-flex; align-items: center; gap: 0.75rem; background: white; color: #0f172a; padding: 1rem 2rem; border-radius: 12px; text-decoration: none; font-weight: 700; transition: all 0.3s ease; border: 2px solid #e2e8f0; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                Download Template
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Import Results (Hidden by default) -->
                <div id="importResults" style="display: none;">
                    <div id="resultsContent"></div>
                </div>

                <!-- CSV Format Guide -->
                <div style="background: white; border-radius: 12px; padding: 2.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <h3 style="margin: 0 0 1.5rem; font-size: 1.3rem; font-weight: 700; color: #0f172a; font-family: 'Montserrat', sans-serif;">📋 Required CSV Format</h3>
                    
                    <div style="background: #0f172a; padding: 2rem; border-radius: 12px; overflow-x: auto; margin-bottom: 2rem; box-shadow: 0 4px 15px rgba(15, 23, 42, 0.3);">
                        <pre style="margin: 0;"><code style="color: #e0f2fe; font-family: 'Courier New', monospace; font-size: 0.9rem; line-height: 1.8;">sku,name,price,old_price,category,brand,description,image,is_featured,rating,review_count,stock_qty
LAPTOP001,Dell Inspiron 15,45000,50000,electronics,dell,15.6" laptop with 8GB RAM,laptop.jpg,false,4.5,120,25
SHIRT001,Cotton T-Shirt,499,699,fashion,nike,Comfortable cotton tee,shirt.jpg,true,4.2,85,100</code></pre>
                    </div>
                    
                    <h4 style="margin: 0 0 1rem; font-size: 1.1rem; font-weight: 700; color: #0f172a; font-family: 'Montserrat', sans-serif;">CSV Column Guidelines:</h4>
                    <div style="display: grid; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px; padding: 1rem; background: #f8fafc; border-radius: 12px; border-left: 4px solid #ef4444; transition: all 0.2s;">
                            <span style="font-family: 'Courier New', monospace; font-weight: 700; width: 130px; color: #0f172a; font-size: 0.95rem;">sku</span>
                            <span style="font-size: 0.7rem; padding: 0.3rem 0.7rem; border-radius: 12px; color: white; background: #ef4444; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Required*</span>
                            <span style="font-size: 0.9rem; color: #64748b; line-height: 1.5;">Unique product identifier. (Or Name required)</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; padding: 1rem; background: #f8fafc; border-radius: 12px; border-left: 4px solid #ef4444; transition: all 0.2s;">
                            <span style="font-family: 'Courier New', monospace; font-weight: 700; width: 130px; color: #0f172a; font-size: 0.95rem;">name</span>
                            <span style="font-size: 0.7rem; padding: 0.3rem 0.7rem; border-radius: 12px; color: white; background: #ef4444; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Required*</span>
                            <span style="font-size: 0.9rem; color: #64748b; line-height: 1.5;">Product name. (Or SKU required)</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; padding: 1rem; background: white; border-radius: 12px; border-left: 4px solid #ef4444; transition: all 0.2s;">
                            <span style="font-family: 'Courier New', monospace; font-weight: 700; width: 130px; color: #0f172a; font-size: 0.95rem;">price</span>
                            <span style="font-size: 0.7rem; padding: 0.3rem 0.7rem; border-radius: 12px; color: white; background: #ef4444; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Required</span>
                            <span style="font-size: 0.9rem; color: #64748b; line-height: 1.5;">Numeric price (e.g., 1200.00 or $15.99)</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; padding: 1rem; background: #f8fafc; border-radius: 12px; border-left: 4px solid #10b981; transition: all 0.2s;">
                            <span style="font-family: 'Courier New', monospace; font-weight: 700; width: 130px; color: #0f172a; font-size: 0.95rem;">old_price</span>
                            <span style="font-size: 0.7rem; padding: 0.3rem 0.7rem; border-radius: 12px; color: white; background: #10b981; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Optional</span>
                            <span style="font-size: 0.9rem; color: #64748b; line-height: 1.5;">Original price for discount display</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; padding: 1rem; background: white; border-radius: 12px; border-left: 4px solid #10b981; transition: all 0.2s;">
                            <span style="font-family: 'Courier New', monospace; font-weight: 700; width: 130px; color: #0f172a; font-size: 0.95rem;">category</span>
                            <span style="font-size: 0.7rem; padding: 0.3rem 0.7rem; border-radius: 12px; color: white; background: #10b981; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Optional</span>
                            <span style="font-size: 0.9rem; color: #64748b; line-height: 1.5;">Category slug (auto-created if not exists)</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; padding: 1rem; background: #f8fafc; border-radius: 12px; border-left: 4px solid #10b981; transition: all 0.2s;">
                            <span style="font-family: 'Courier New', monospace; font-weight: 700; width: 130px; color: #0f172a; font-size: 0.95rem;">brand</span>
                            <span style="font-size: 0.7rem; padding: 0.3rem 0.7rem; border-radius: 12px; color: white; background: #10b981; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Optional</span>
                            <span style="font-size: 0.9rem; color: #64748b; line-height: 1.5;">Brand slug (auto-created if not exists)</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; padding: 1rem; background: white; border-radius: 12px; border-left: 4px solid #10b981; transition: all 0.2s;">
                            <span style="font-family: 'Courier New', monospace; font-weight: 700; width: 130px; color: #0f172a; font-size: 0.95rem;">stock_qty</span>
                            <span style="font-size: 0.7rem; padding: 0.3rem 0.7rem; border-radius: 12px; color: white; background: #10b981; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Optional</span>
                            <span style="font-size: 0.9rem; color: #64748b; line-height: 1.5;">Available stock (default: 0)</span>
                        </div>
                    </div>
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

#fileLabel:hover,
#fileLabel.drag-over {
    border-color: #0e7490;
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    transform: scale(1.02);
}

#importBtn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(8, 145, 178, 0.4);
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

<script>
// File upload handling
const fileInput = document.getElementById('csv_file');
const fileLabel = document.getElementById('fileLabel');
const fileName = document.getElementById('fileName');
const importForm = document.getElementById('importForm');
const importBtn = document.getElementById('importBtn');

// File selection
fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size exceeds 5MB limit!');
            fileInput.value = '';
            return;
        }
        fileName.textContent = file.name;
        fileLabel.style.borderColor = '#10b981';
        fileLabel.style.background = 'linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)';
    }
});

// Drag and drop
fileLabel.addEventListener('dragover', (e) => {
    e.preventDefault();
    fileLabel.classList.add('drag-over');
});

fileLabel.addEventListener('dragleave', () => {
    fileLabel.classList.remove('drag-over');
});

fileLabel.addEventListener('drop', (e) => {
    e.preventDefault();
    fileLabel.classList.remove('drag-over');
    
    const file = e.dataTransfer.files[0];
    if (file && file.name.endsWith('.csv')) {
        if (file.size > 5 * 1024 * 1024) {
            alert('File size exceeds 5MB limit!');
            return;
        }
        fileInput.files = e.dataTransfer.files;
        fileName.textContent = file.name;
        fileLabel.style.borderColor = '#10b981';
        fileLabel.style.background = 'linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)';
    } else {
        alert('Please upload a CSV file');
    }
});

// Form submission
importForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const file = fileInput.files[0];
    if (!file) {
        alert('Please select a CSV file');
        return;
    }
    
    // Show loading state
    const originalText = importBtn.innerHTML;
    importBtn.innerHTML = '<span class="spinner"></span> Importing...';
    importBtn.disabled = true;
    
    const formData = new FormData();
    formData.append('csv_file', file);
    
    try {
        const response = await fetch('<?= BASE_URL ?>admin/importprocess', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        // Show results
        const resultsDiv = document.getElementById('importResults');
        const resultsContent = document.getElementById('resultsContent');
        
        if (result.success) {
            resultsContent.innerHTML = `
                <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <h3 style="margin: 0 0 1.5rem; font-size: 1.3rem; font-weight: 700; color: #0f172a; font-family: 'Montserrat', sans-serif;">Import Results</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                        <div style="text-align: center; padding: 1.5rem; border-radius: 12px; background: white; border-left: 4px solid #10b981; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <span style="display: block; font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem; font-family: 'Montserrat', sans-serif; color: #10b981;">${result.results.new_products}</span>
                            <span style="display: block; color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">New Products</span>
                        </div>
                        <div style="text-align: center; padding: 1.5rem; border-radius: 12px; background: white; border-left: 4px solid #3b82f6; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <span style="display: block; font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem; font-family: 'Montserrat', sans-serif; color: #3b82f6;">${result.results.updated_products}</span>
                            <span style="display: block; color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Updated Products</span>
                        </div>
                        <div style="text-align: center; padding: 1.5rem; border-radius: 12px; background: white; border-left: 4px solid #ef4444; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <span style="display: block; font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem; font-family: 'Montserrat', sans-serif; color: #ef4444;">${result.results.failed_rows}</span>
                            <span style="display: block; color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Failed Rows</span>
                        </div>
                    </div>
                    ${result.results.failed_rows > 0 ? `
                        <div style="background: #fee2e2; padding: 1.5rem; border-radius: 12px; border-left: 4px solid #ef4444;">
                            <h4 style="margin: 0 0 1rem; color: #991b1b; font-weight: 700; font-family: 'Montserrat', sans-serif;">Failed Rows</h4>
                            <p style="margin: 0 0 1rem; color: #7f1d1d;">Some rows could not be imported. Download the failed rows CSV to see error details.</p>
                            <a href="<?= BASE_URL ?>admin/downloadfailed?file=${result.results.failed_file}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #ef4444; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
                                <i class="fas fa-download"></i>
                                Download Failed Rows CSV
                            </a>
                        </div>
                    ` : `
                        <div style="background: #d1fae5; padding: 1.5rem; border-radius: 12px; border-left: 4px solid #10b981; text-align: center;">
                            <i class="fas fa-check-circle" style="font-size: 2.5rem; color: #10b981; margin-bottom: 0.5rem;"></i>
                            <p style="margin: 0; color: #065f46; font-weight: 600; font-size: 1.1rem;">✅ All products imported successfully!</p>
                        </div>
                    `}
                </div>
            `;
            resultsDiv.style.display = 'block';
            resultsDiv.scrollIntoView({ behavior: 'smooth' });
        } else {
            resultsContent.innerHTML = `
                <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <div style="background: #fee2e2; padding: 2rem; border-radius: 12px; border-left: 4px solid #ef4444; text-align: center;">
                        <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #ef4444; margin-bottom: 1rem;"></i>
                        <h4 style="margin: 0 0 0.5rem; color: #991b1b; font-weight: 700; font-family: 'Montserrat', sans-serif;">Import Failed</h4>
                        <p style="margin: 0; color: #7f1d1d;">${result.message}</p>
                    </div>
                </div>
            `;
            resultsDiv.style.display = 'block';
        }
    } catch (error) {
        alert('An error occurred during import: ' + error.message);
    } finally {
        importBtn.innerHTML = originalText;
        importBtn.disabled = false;
    }
});
</script>
