<?php extend('layouts.auth') ?>

<?php section('title') ?>
403 접근이 거부되었습니다.
<?php end_section() ?>

<?php
    dump(router()->getRoutes());
?>

<?php section('content') ?>
    <div class="no-section-xl no-auth-layout">
        <div class="no-error-wrap">
            <h1>403</h1>
            <h2>접근이 거부되었습니다.</h2>
            <p>이 페이지에 접근할 권한이 없습니다.  
            <br>홈페이지로 돌아가시려면 <a href="<?= route('home') ?>" class="--underline">여기</a>를 클릭하세요.</p>
        </div>
    </div>
<?php end_section() ?>
