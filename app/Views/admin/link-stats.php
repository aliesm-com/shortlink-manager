<?php
use App\Helpers;

$dailyLabels = array_map(function ($row) {
    return date('m/d', strtotime($row['date']));
}, $dailyChart);
$dailyValues = array_map(function ($row) {
    return $row['count'];
}, $dailyChart);

$hourlyLabels = array_map(function ($row) {
    return $row['hour'];
}, $hourlyChart);
$hourlyValues = array_map(function ($row) {
    return $row['count'];
}, $hourlyChart);

$pageSubtitle = Helpers::shortUrl($config, $link->slug);

ob_start();
?>

<div class="stats-grid mb-6">
    <div class="card stat-card">
        <div class="stat-label">کد لینک</div>
        <div class="stat-value" style="font-size: 1.25rem;" dir="ltr"><?= Helpers::e($link->slug) ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">کل کلیک‌ها</div>
        <div class="stat-value"><?= (int) $link->click_count ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">وضعیت</div>
        <div class="stat-value" style="font-size: 1.25rem;">
            <?= $link->is_active ? 'فعال' : 'غیرفعال' ?>
        </div>
    </div>
</div>

<div class="mb-4">
    <a href="<?= Helpers::url($config, 'admin/links') ?>" class="btn btn-secondary btn-sm">بازگشت به لیست</a>
    <button type="button" class="btn btn-primary btn-sm copy-btn" data-copy="<?= Helpers::e(Helpers::shortUrl($config, $link->slug)) ?>">کپی لینک</button>
</div>

<div class="grid-2 mb-6">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">کلیک روزانه (۱۴ روز)</h2>
        </div>
        <div class="card-content">
            <div class="chart-container">
                <canvas id="dailyChart"></canvas>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">کلیک ساعتی (امروز)</h2>
        </div>
        <div class="card-content">
            <div class="chart-container">
                <canvas id="hourlyChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">آخرین کلیک‌ها</h2>
    </div>
    <div class="card-content" style="padding-top: 0;">
        <?php if (empty($recentClicks)): ?>
        <p class="text-muted text-sm" style="padding: 2rem; text-align: center;">هنوز کلیکی ثبت نشده است.</p>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>زمان</th>
                        <th>مرورگر</th>
                        <th>ارجاع</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentClicks as $click): ?>
                    <tr>
                        <td><?= Helpers::e($click->clicked_at) ?></td>
                        <td class="truncate text-sm" title="<?= Helpers::e($click->user_agent ?: '') ?>">
                            <?= Helpers::e($click->user_agent ? substr($click->user_agent, 0, 60) : '—') ?>
                        </td>
                        <td class="truncate text-sm" dir="ltr">
                            <?= Helpers::e($click->referer ?: '—') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
window.CHART_DATA = {
    daily: {
        labels: <?= json_encode($dailyLabels, JSON_UNESCAPED_UNICODE) ?>,
        values: <?= json_encode($dailyValues) ?>
    },
    hourly: {
        labels: <?= json_encode($hourlyLabels, JSON_UNESCAPED_UNICODE) ?>,
        values: <?= json_encode($hourlyValues) ?>
    }
};
</script>

<?php
$innerContent = ob_get_clean();
$includeChart = true;
require APP_PATH . '/Views/layouts/admin.php';
