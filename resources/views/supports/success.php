<?php extend('layouts.auth') ?>

<?php section('title') ?>
<?= $title ?? '성공' ?>
<?php end_section() ?>

<?php section('content') ?>
<div class="no-section-xl no-auth-layout">
    <div class="no-success-wrap">
        <h1><?= $title ?? 'Success!' ?></h1>
        <h2><?= $message ?? '요청이 정상적으로 처리되었습니다.' ?></h2>
        <p>
            <?= $description ?? '계속 진행하려면 아래 버튼을 클릭하세요.<br>홈페이지로 돌아가시려면 ' ?>
            <a href="<?= route('home') ?>" class="--underline">여기</a>를 클릭하세요.
        </p>
    </div>
</div>
<?php end_section() ?>
