(function () {
    'use strict';

    var parsedDelay = parseInt(window.REDIRECT_DELAY, 10);
    var delay = Number.isFinite(parsedDelay) && parsedDelay > 0 ? parsedDelay : 10;
    var targetUrl = window.REDIRECT_TARGET || '/';
    var countdownEl = document.getElementById('countdown');
    var countdownWrapEl = document.querySelector('.countdown-wrap');
    var messageEl = document.getElementById('message');
    var progressEl = document.getElementById('progress');
    var cardEl = document.querySelector('.redirect-card');
    var vpnNotice = document.getElementById('vpn-notice');
    var remaining = delay;

    function updateUI() {
        if (countdownEl) {
            countdownEl.textContent = remaining;
        }
        if (progressEl) {
            var percent = ((delay - remaining) / delay) * 100;
            progressEl.style.width = percent + '%';
        }
    }

    function showVpnNotice() {
        if (messageEl) {
            messageEl.textContent = 'در حال انتقال به مقصد...';
        }
        if (countdownWrapEl) {
            countdownWrapEl.style.display = 'none';
        }
        if (vpnNotice) {
            vpnNotice.classList.add('visible');
        }
        if (progressEl) {
            progressEl.style.width = '100%';
        }
        if (cardEl) {
            cardEl.classList.add('is-finished');
        }
    }

    function redirect() {
        window.location.href = targetUrl;
    }

    updateUI();

    var timer = setInterval(function () {
        remaining--;
        updateUI();

        if (remaining <= 0) {
            clearInterval(timer);
            showVpnNotice();
            setTimeout(redirect, 2000);
        }
    }, 1000);
})();
