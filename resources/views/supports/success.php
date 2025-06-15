<!doctype html>
<html data-theme="dark">
<head>
    <meta charset="utf-8">
    <title><?= e($title ?? 'Success') ?></title>
    <link rel="stylesheet" href="<?= asset_path('css/admin.min.css?v=' . time()) ?>"/>
</head>
<body>
    <div class="no-section-xl no-auth-layout">
        <div class="no-error-wrap" style="--clr-error-main: var(--clr-success-main, #00e676);">
            <h1><?= e($title ?? 'Success') ?></h1>
            <h2><?= e($message ?? '작업이 성공적으로 완료되었습니다.') ?></h2>
            <p>
                <?= e($description ?? '계속 진행하시려면 아래 버튼을 클릭하세요.') ?><br>
                <a href="<?= route('home') ?>" class="--underline">홈페이지로 이동</a>
            </p>

            <?php if (!empty($meta)): ?>
                <hr>
                <div class="meta-info">
                    <b>추가 정보:</b>
                    <pre><?= e(print_r($meta, true)) ?></pre>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>