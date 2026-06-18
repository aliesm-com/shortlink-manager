<?php
use App\Helpers;

$dailyLabels = array_map(function ($row) {
    return date('m/d', strtotime($row['date']));
}, $chartData);
$dailyValues = array_map(function ($row) {
    return $row['count'];
}, $chartData);

ob_start();
?>

<div class="stats-grid">
    <div class="card stat-card">
        <div class="stat-label">تعداد لینک‌ها</div>
        <div class="stat-value"><?= (int) $totalLinks ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">کل کلیک‌ها</div>
        <div class="stat-value"><?= (int) $totalClicks ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">پربازدیدترین لینک</div>
        <div class="stat-value" style="font-size: 1.25rem;">
            <?php if ($topLink): ?>
                <?= Helpers::e($topLink->slug) ?>
            <?php else: ?>
                —
            <?php endif; ?>
        </div>
        <?php if ($topLink): ?>
        <div class="stat-meta"><?= (int) $topLink->click_count ?> کلیک</div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">کلیک‌های ۷ روز اخیر</h2>
        <p class="card-description">نمودار عملکرد کلی سرویس</p>
    </div>
    <div class="card-content">
        <div class="chart-container">
            <canvas id="dailyChart"></canvas>
        </div>
    </div>
</div>

<script>
window.CHART_DATA = {
    daily: {
        labels: <?= json_encode($dailyLabels, JSON_UNESCAPED_UNICODE) ?>,
        values: <?= json_encode($dailyValues) ?>
    }
};
</script>

<?php
$innerContent = ob_get_clean();
$includeChart = true;
require APP_PATH . '/Views/layouts/admin.php';
