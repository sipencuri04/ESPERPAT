    </main>
    
    <!-- Scripts Only (No Visual Footer for Modern Mobile UI) -->
    
    <!-- Frameworks -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    
    <script>
        // Smooth alert handling
        setTimeout(() => {
            document.querySelectorAll('.alert-floating').forEach(el => {
                el.style.transform = 'translateY(-20px)';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 400);
            });
        }, 4000);
    </script>
</body>
</html>
