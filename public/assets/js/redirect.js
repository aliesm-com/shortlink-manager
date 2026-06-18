(function () {
    'use strict';

    var delay = window.REDIRECT_DELAY || 10;
    var targetUrl = window.REDIRECT_TARGET || '/';
    var countdownEl = document.getElementById('countdown');
    var messageEl = document.getElementById('message');
    var progressEl = document.getElementById('progress');
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
        if (vpnNotice) {
            vpnNotice.classList.add('visible');
        }
        if (countdownEl) {
            countdownEl.textContent = '';
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
