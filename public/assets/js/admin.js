(function () {
    'use strict';

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

    function showCopied(btn) {
        var original = btn.textContent;
        btn.textContent = 'کپی شد!';
        setTimeout(function () {
            btn.textContent = original;
        }, 1500);
    }

    if (typeof Chart !== 'undefined' && window.CHART_DATA) {
        initCharts();
    }

    function initCharts() {
        var defaults = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Vazirmatn' } }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { family: 'Vazirmatn' }
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
                            pointRadius: 4,
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
