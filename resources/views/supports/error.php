<!doctype html>
<html data-theme="dark">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title ?? ($code ?? '오류') . ' 오류') ?></title>
    <link rel="stylesheet" href="<?=asset_path('css/admin.min.css?v='.time())?>"/>
</head>
<body>
    <div class="no-section-xl no-auth-layout">
        <div class="no-error-wrap">
            <h1><?= $code ?? 'Error' ?></h1>
            <h2><?= $details['message'] ?? $message ?? '알 수 없는 오류가 발생했습니다.' ?></h2>
            <p>
                <?= $description ?? '잠시 후 다시 시도해 주세요. 문제가 계속되면 관리자에게 문의하세요.' ?><br>
                홈페이지로 돌아가시려면
                <a href="<?= route('home') ?>" class="--underline">여기</a>를 클릭하세요.
            </p>
            <?php if (!empty($meta)): ?>
                <hr>
                <div class="meta-info">
                    <b>추가 정보:</b>
                    <pre><?= escape(print_r($meta, true)) ?></pre>
                </div>
            <?php endif; ?>

            <?php if (!empty($debug) && !empty($details)): ?>
                <hr>
                <div class="debug-info">
                    <h3>디버그 정보</h3>
                    <b>예외 타입:</b> <?= escape($details['exception']) ?><br>
                    <b>파일:</b> <?= escape($details['file']) ?>:<?= $details['line'] ?><br>
                    <b>코드:</b> <?= escape($details['code']) ?><br>
                    <b>메시지:</b> <?= escape($details['message']) ?><br>
                    <b>트레이스:</b>
                    <pre><?= escape($details['trace']) ?></pre>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>



