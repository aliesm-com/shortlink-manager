<?php
use App\Helpers;

ob_start();
?>

<?php if ($success): ?>
<div class="alert alert-success"><?= Helpers::e($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-error"><?= Helpers::e($error) ?></div>
<?php endif; ?>

<div class="grid-2 mb-6">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">ساخت لینک جدید</h2>
            <p class="card-description">آدرس مقصد را وارد کنید تا لینک کوتاه بسازید</p>
        </div>
        <div class="card-content">
            <form method="POST" action="<?= Helpers::url($config, 'admin/links') ?>">
                <input type="hidden" name="csrf_token" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
                <div class="form-group">
                    <label class="form-label" for="url">آدرس URL</label>
                    <input type="url" id="url" name="url" class="form-input" placeholder="https://example.com" required dir="ltr">
                </div>
                <div class="form-group">
                    <label class="form-label" for="title">عنوان (اختیاری)</label>
                    <input type="text" id="title" name="title" class="form-input" placeholder="مثلاً: صفحه اصلی">
                </div>
                <div class="form-group">
                    <label class="form-label" for="slug">کد لینک (اختیاری)</label>
                    <input type="text" id="slug" name="slug" class="form-input" placeholder="my-link" dir="ltr" pattern="[a-zA-Z0-9_-]{2,64}">
                    <p class="form-hint">اگر خالی بگذارید، کد به صورت خودکار ساخته می‌شود.</p>
                </div>
                <button type="submit" class="btn btn-primary">ساخت لینک</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">راهنما</h2>
        </div>
        <div class="card-content text-sm text-muted">
            <p class="mb-4">لینک کوتاه به این شکل خواهد بود:</p>
            <p dir="ltr" style="background: var(--muted); padding: 0.75rem; border-radius: var(--radius); font-family: monospace;">
                <?= Helpers::e(Helpers::baseUrl($config)) ?>/your-code
            </p>
            <p class="mt-4">با کلیک روی لینک، کاربر ۱۰ ثانیه صبر می‌کند و سپس به مقصد منتقل می‌شود.</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header flex items-center justify-between">
        <div>
            <h2 class="card-title">لیست لینک‌ها</h2>
            <p class="card-description"><?= count($links) ?> لینک ثبت شده</p>
        </div>
    </div>
    <div class="card-content" style="padding-top: 0;">
        <?php if (empty($links)): ?>
        <p class="text-muted text-sm" style="padding: 2rem; text-align: center;">هنوز لینکی ساخته نشده است.</p>
        <?php else: ?>
        <div class="table-wrap">
            <table class="responsive-table">
                <thead>
                    <tr>
                        <th>لینک کوتاه</th>
                        <th>مقصد</th>
                        <th>عنوان</th>
                        <th>کلیک</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($links as $link): ?>
                    <?php $shortUrl = Helpers::shortUrl($config, $link->slug); ?>
                    <tr>
                        <td data-label="لینک کوتاه">
                            <span dir="ltr" class="text-sm link-slug"><?= Helpers::e($link->slug) ?></span>
                        </td>
                        <td data-label="مقصد">
                            <span class="truncate text-sm" dir="ltr" title="<?= Helpers::e($link->original_url) ?>">
                                <?= Helpers::e($link->original_url) ?>
                            </span>
                        </td>
                        <td data-label="عنوان"><?= Helpers::e($link->title ?: '—') ?></td>
                        <td data-label="کلیک"><?= (int) $link->click_count ?></td>
                        <td data-label="وضعیت">
                            <?php if ($link->is_active): ?>
                            <span class="badge badge-success">فعال</span>
                            <?php else: ?>
                            <span class="badge badge-muted">غیرفعال</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="عملیات">
                            <div class="table-actions flex gap-2">
                                <button type="button" class="btn btn-secondary btn-sm copy-btn" data-copy="<?= Helpers::e($shortUrl) ?>">کپی</button>
                                <a href="<?= Helpers::url($config, 'admin/links/' . $link->id . '/stats') ?>" class="btn btn-ghost btn-sm">آمار</a>
                                <form method="POST" action="<?= Helpers::url($config, 'admin/links/' . $link->id . '/toggle') ?>" class="inline-form">
                                    <input type="hidden" name="csrf_token" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
                                    <button type="submit" class="btn btn-ghost btn-sm"><?= $link->is_active ? 'غیرفعال' : 'فعال' ?></button>
                                </form>
                                <form method="POST" action="<?= Helpers::url($config, 'admin/links/' . $link->id . '/delete') ?>" class="inline-form" onsubmit="return confirm('آیا از حذف این لینک مطمئن هستید؟');">
                                    <input type="hidden" name="csrf_token" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
                                    <button type="submit" class="btn btn-destructive btn-sm">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$innerContent = ob_get_clean();
require APP_PATH . '/Views/layouts/admin.php';
