<?php
use App\Helpers;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>در حال انتقال...</title>
    <link rel="stylesheet" href="<?= Helpers::assetUrl($config, 'css/app.css') ?>">
</head>
<body>
    <div class="redirect-page">
        <div class="card redirect-card">
            <div class="redirect-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                </svg>
            </div>
            <h1 class="redirect-title">در حال انتقال هستید</h1>
            <p class="redirect-message" id="message">لطفاً چند لحظه صبر کنید...</p>
            <div class="countdown" id="countdown"><?= (int) $delay ?></div>
            <div class="progress-bar">
                <div class="progress-fill" id="progress"></div>
            </div>
            <div class="vpn-notice" id="vpn-notice">
                لطفاً VPN را خاموش کنید
            </div>
        </div>
    </div>
    <script>
        window.REDIRECT_DELAY = <?= (int) $delay ?>;
        window.REDIRECT_TARGET = <?= json_encode($link->original_url, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    </script>
    <script src="<?= Helpers::assetUrl($config, 'js/redirect.js') ?>"></script>
</body>
</html>
