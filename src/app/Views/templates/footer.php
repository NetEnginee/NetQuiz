        </div>
    </main>

    <!-- Global Minimalist Geist Footer -->
    <footer style="border-top: 1px solid var(--color-border, #E5E7EB); padding: 1.5rem 0; background-color: rgba(255, 255, 255, 0.85); backdrop-filter: blur(8px); margin-top: auto; position: relative; z-index: 10;">
        <div class="student-shell-container" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="nav-brand-text" style="font-size: 0.875rem; font-weight: 700; color: #18181B;">Net<span class="nav-brand-accent" style="color: #18181B;">Quiz</span><span class="brand-cursor">_</span></span>
                <span style="font-size: 0.8rem; color: #71717A;">&copy; <?= date('Y') ?> RouterOS Academy. All rights reserved.</span>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.75rem; color: #71717A;" class="font-mono">
                <span>Versi 2.4.0 (Stable)</span>
                <span>•</span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span class="live-dot" style="width: 6px; height: 6px;"></span>
                    <span>System Normal</span>
                </span>
            </div>
        </div>
    </footer>

    <!-- Global Icon Initializer -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
