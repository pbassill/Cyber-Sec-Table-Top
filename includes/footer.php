    </main>

    <footer class="dnd-footer text-center py-4 mt-5">
        <div class="container">
            <div class="footer-divider mb-3">⚜️ ═══════════════════ ⚜️</div>
            <p class="footer-text mb-1">Cyber Quest — Incident Response Tabletop Exercise Framework</p>
            <p class="footer-subtext mb-0">Forged in the fires of cybersecurity preparedness</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
    // Theme toggle (Dark D&D / Light Boardroom)
    (function() {
        var toggle = document.getElementById('themeToggle');
        var icon = document.getElementById('themeIcon');
        var html = document.documentElement;
        var body = document.body;

        var saved = localStorage.getItem('cyberquest-theme') || 'dark';
        applyTheme(saved);

        toggle?.addEventListener('click', function() {
            var current = body.classList.contains('light-theme') ? 'light' : 'dark';
            var next = current === 'dark' ? 'light' : 'dark';
            applyTheme(next);
            localStorage.setItem('cyberquest-theme', next);
        });

        function applyTheme(theme) {
            if (theme === 'light') {
                body.classList.add('light-theme');
                html.setAttribute('data-bs-theme', 'light');
                if (icon) { icon.className = 'bi bi-moon-fill'; }
            } else {
                body.classList.remove('light-theme');
                html.setAttribute('data-bs-theme', 'dark');
                if (icon) { icon.className = 'bi bi-sun-fill'; }
            }
        }
    })();
    </script>
</body>
</html>
