<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? App\Helpers::e($pageTitle) . ' | ' : '' ?>Shortlink Manager</title>
    <link rel="stylesheet" href="<?= App\Helpers::url($config, 'assets/css/app.css') ?>">
    <?php if (!empty($includeChart)): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <?php endif; ?>
</head>
<body>
    <?= $content ?? '' ?>
</body>
</html>
