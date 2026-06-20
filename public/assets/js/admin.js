(function () {
    'use strict';

    initMobileNav();
    initCopyButtons();
    initCharts();

    function initMobileNav() {
        var menuBtn = document.getElementById('mobileMenuBtn');
        var closeBtn = document.getElementById('sidebarCloseBtn');
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');

        if (!menuBtn || !sidebar || !overlay) {
            return;
        }

        function openSidebar() {
            sidebar.classList.add('is-open');
            overlay.classList.add('is-visible');
            overlay.setAttribute('aria-hidden', 'false');
            menuBtn.setAttribute('aria-expanded', 'true');
            document.body.classList.add('sidebar-open');
        }

        function closeSidebar() {
            sidebar.classList.remove('is-open');
            overlay.classList.remove('is-visible');
            overlay.setAttribute('aria-hidden', 'true');
            menuBtn.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('sidebar-open');
        }

        menuBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);

        sidebar.querySelectorAll('.nav-item').forEach(function (link) {
            link.addEventListener('click', closeSidebar);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeSidebar();
        });
    }

    function initCopyButtons() {
        document.querySelectorAll('[data-copy]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var text = btn.getAttribute('data-copy');
                if (!text) return;

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(showCopied.bind(null, btn));
                } else {
                    var ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    try {
                        document.execCommand('copy');
                        showCopied(btn);
                    } catch (e) {}
                    document.body.removeChild(ta);
                }
            });
        });
    }

    function showCopied(btn) {
        var original = btn.textContent;
        btn.textContent = 'کپی شد!';
        setTimeout(function () {
            btn.textContent = original;
        }, 1500);
    }

    function initCharts() {
        if (typeof Chart === 'undefined' || !window.CHART_DATA) {
            return;
        }

        var defaults = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Vazirmatn', size: 10 },
                        maxRotation: 45,
                        minRotation: 0
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { family: 'Vazirmatn', size: 10 }
                    }
                }
            }
        };

        if (window.CHART_DATA.daily) {
            var dailyCtx = document.getElementById('dailyChart');
            if (dailyCtx) {
                new Chart(dailyCtx, {
                    type: 'line',
                    data: {
                        labels: window.CHART_DATA.daily.labels,
                        datasets: [{
                            label: 'کلیک',
                            data: window.CHART_DATA.daily.values,
                            borderColor: '#18181b',
                            backgroundColor: 'rgba(24,24,27,0.08)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 3,
                            pointBackgroundColor: '#18181b'
                        }]
                    },
                    options: defaults
                });
            }
        }

        if (window.CHART_DATA.hourly) {
            var hourlyCtx = document.getElementById('hourlyChart');
            if (hourlyCtx) {
                new Chart(hourlyCtx, {
                    type: 'bar',
                    data: {
                        labels: window.CHART_DATA.hourly.labels,
                        datasets: [{
                            label: 'کلیک',
                            data: window.CHART_DATA.hourly.values,
                            backgroundColor: 'rgba(24,24,27,0.8)',
                            borderRadius: 4
                        }]
                    },
                    options: defaults
                });
            }
        }
    }
})();
