<?php
use App\Helpers;
ob_start();
?>
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">S</div>
            Shortlink
        </div>
        <nav class="sidebar-nav">
            <a href="<?= Helpers::url($config, 'admin/dashboard') ?>" class="nav-item <?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                داشبورد
            </a>
            <a href="<?= Helpers::url($config, 'admin/links') ?>" class="nav-item <?= ($activeNav ?? '') === 'links' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                لینک‌ها
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= Helpers::url($config, 'admin/logout') ?>" class="nav-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                خروج
            </a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title"><?= Helpers::e($pageTitle ?? '') ?></h1>
            <?php if (!empty($pageSubtitle)): ?>
            <p class="page-subtitle"><?= Helpers::e($pageSubtitle) ?></p>
            <?php endif; ?>
        </div>
        <?= $innerContent ?? '' ?>
    </main>
</div>

<script src="<?= Helpers::assetUrl($config, 'js/admin.js') ?>"></script>
<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/base.php';
