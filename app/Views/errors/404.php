<?php
use App\Helpers;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>صفحه یافت نشد</title>
    <link rel="stylesheet" href="<?= Helpers::assetUrl($config, 'css/app.css') ?>">
</head>
<body>
    <div class="redirect-page">
        <div class="card redirect-card">
            <div class="redirect-icon" style="background: var(--destructive);">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
            <h1 class="redirect-title">صفحه یافت نشد</h1>
            <p class="redirect-message">لینک مورد نظر وجود ندارد یا غیرفعال شده است.</p>
        </div>
    </div>
</body>
</html>
