/* ═══════════════════════════════════════════════════
   SmartCatalog — app.js
   All shared JavaScript, bundled via Vite.
   ═══════════════════════════════════════════════════ */

(function () {
    'use strict';

    /* ── Lucide Icons ─────────────────────────────── */
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    /* ── Global helpers ───────────────────────────── */
    window.formatRp = function (n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    };

    window.formatNumber = function (n) {
        return Number(n).toLocaleString('id-ID');
    };

    /* ── Sidebar toggle ───────────────────────────── */
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var btnToggle = document.getElementById('btnSidebarToggle');

    if (btnToggle && sidebar && overlay) {
        btnToggle.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sc-sidebar', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
            }
        });

        overlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        });

        if (window.innerWidth >= 992 && localStorage.getItem('sc-sidebar') === 'collapsed') {
            sidebar.classList.add('collapsed');
        }

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('show');
            }
        });
    }

    /* ── Clock ────────────────────────────────────── */
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    function updateClock() {
        var now = new Date();
        var clockDate = document.getElementById('clockDate');
        var clockTime = document.getElementById('clockTime');
        if (clockDate) {
            clockDate.textContent = String(now.getDate()).padStart(2, '0') + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
        }
        if (clockTime) {
            clockTime.textContent = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ':' + String(now.getSeconds()).padStart(2, '0');
        }
    }
    updateClock();
    setInterval(updateClock, 1000);

    /* ── Flash alert auto-dismiss ─────────────────── */
    setTimeout(function () {
        document.querySelectorAll('.flash-alert').forEach(function (el) {
            var delay = el.classList.contains('alert-danger') ? 10000 : 4000;
            setTimeout(function() {
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    bootstrap.Alert.getOrCreateInstance(el).close();
                } else {
                    el.remove();
                }
            }, delay);
        });
    }, 100);

    /* ── Password toggle (shared) ─────────────────── */
    window.togglePassword = function (fieldId, btn) {
        var input = document.getElementById(fieldId);
        if (!input) return;
        var icon = btn.querySelector('[data-lucide]');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            if (icon) icon.setAttribute('data-lucide', 'eye');
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    };

    /* ── Password strength (shared) ───────────────── */
    window.scorePassword = function (val) {
        var s = 0;
        if (val.length >= 8) s++;
        if (/[A-Z]/.test(val)) s++;
        if (/[a-z]/.test(val)) s++;
        if (/[0-9]/.test(val)) s++;
        if (/[@$!%*#?&]/.test(val)) s++;
        return s;
    };

    window.updateStrengthBar = function (barId, hintId, val) {
        var bar = document.getElementById(barId);
        var hint = document.getElementById(hintId);
        if (!bar || !hint) return;
        var s = window.scorePassword(val);
        bar.className = 'password-strength-bar';
        if (!val) { hint.textContent = ''; hint.className = 'validation-hint'; return; }
        if (s <= 2) { bar.classList.add('strength-weak'); hint.textContent = 'Lemah'; hint.className = 'validation-hint invalid'; }
        else if (s <= 4) { bar.classList.add('strength-medium'); hint.textContent = 'Sedang'; hint.className = 'validation-hint'; }
        else { bar.classList.add('strength-strong'); hint.textContent = 'Kuat'; hint.className = 'validation-hint valid'; }
    };

    /* ── Auth input-toggle (auto-bind) ────────────── */
    document.querySelectorAll('.input-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = this.previousElementSibling;
            var icon = this.querySelector('[data-lucide]');
            if (!input) return;
            if (input.type === 'password') { input.type = 'text'; if (icon) icon.setAttribute('data-lucide', 'eye-off'); }
            else { input.type = 'password'; if (icon) icon.setAttribute('data-lucide', 'eye'); }
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    });

    /* ── Auth form submit loading ─────────────────── */
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            var btn = this.querySelector('.btn-submit');
            if (btn) { btn.classList.add('loading'); btn.disabled = true; }
        });
    });

    /* ── Re-init lucide for dynamic content ───────── */
    window.reinitIcons = function () {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    };

    /* ── Logout confirmation ──────────────────────── */
    window.confirmLogout = function () {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Keluar?',
                text: 'Anda akan keluar dari sistem.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal'
            }).then(function (result) {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        } else {
            document.getElementById('logout-form').submit();
        }
    };
})();
