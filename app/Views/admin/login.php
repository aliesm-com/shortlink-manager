<?php
use App\Helpers;

$flashError = Helpers::flash('error');
$error = $error ?? $flashError;

ob_start();
?>

<div class="login-page">
    <div class="card login-card">
        <div class="card-header" style="text-align: center;">
            <div class="sidebar-brand-icon" style="margin: 0 auto 1rem;">S</div>
            <h1 class="card-title">ورود به پنل مدیریت</h1>
            <p class="card-description">رمز عبور خود را وارد کنید</p>
        </div>
        <div class="card-content">
            <?php if ($error): ?>
            <div class="alert alert-error"><?= Helpers::e($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= Helpers::url($config, 'admin/login') ?>">
                <input type="hidden" name="csrf_token" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
                <div class="form-group">
                    <label class="form-label" for="password">رمز عبور</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="رمز عبور را وارد کنید"
                        required
                        <?= !empty($locked) ? 'disabled' : '' ?>
                        autofocus
                    >
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;" <?= !empty($locked) ? 'disabled' : '' ?>>
                    ورود
                </button>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'ورود';
require APP_PATH . '/Views/layouts/base.php';
