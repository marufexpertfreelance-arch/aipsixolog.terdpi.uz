            </div>
            
            <!-- Footer -->
            <footer class="main-footer">
                <div class="footer-container">
                    <p class="footer-text">TerDPI talabalar psixologik xizmati &copy; <?= date('Y') ?></p>
                    <p class="footer-links">
                        <a href="https://student.terdpi.uz" target="_blank" rel="noopener">HEMIS</a>
                    </p>
                </div>
            </footer>
        </main>
    </div>

    <!-- Mobile menu toggle -->
    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
    <div class="sidebar-overlay" onclick="toggleMobileMenu()"></div>

    <script>
    function toggleMobileMenu() {
        document.querySelector('.admin-sidebar').classList.toggle('open');
        document.querySelector('.sidebar-overlay').classList.toggle('active');
    }
    </script>
    <?php if (!empty($extraScripts)): ?>
        <?= $extraScripts ?>
    <?php endif; ?>
</body>
</html>
