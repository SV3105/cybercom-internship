            </div>
        </main>
    </div>
    
    <script>
        // Mobile sidebar toggle
        const sidebar = document.querySelector('.admin-sidebar');
        const toggleBtn = document.querySelector('.sidebar-toggle');
        
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }
    </script>
</body>
</html>
