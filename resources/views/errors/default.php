<?php extend('layouts.auth') ?>

<?php section('title') ?>
<?= $title ?? ($code ?? '오류') . ' 오류' ?>
<?php end_section() ?>

<?php section('content') ?>
<div class="no-section-xl no-auth-layout">
    <div class="no-error-wrap">
        <h1><?= $code ?? 'Error' ?></h1>
        <h2><?= $message ?? '알 수 없는 오류가 발생했습니다.' ?></h2>
        <p>
            <?= $description ?? '잠시 후 다시 시도해 주시기 바랍니다. 문제가 지속되면 관리자에게 문의해주세요.' ?><br>
            홈페이지로 돌아가시려면 
            <a href="<?= route('home') ?>" class="--underline">여기</a>를 클릭하세요.
        </p>
    </div>
</div>
<?php end_section() ?>
