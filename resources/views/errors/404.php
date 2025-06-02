
<?php extend('layouts.auth') ?>
<?php section('title') ?>
404 페이지를 찾을 수 없습니다.
<?php end_section() ?>
<?php section('content') ?>
    <div class="no-section-xl no-auth-layout">
        <div class="no-error-wrap">
            <h1>404</h1>
            <h2>페이지를 찾을 수 없습니다.</h2>
            <p>요청하신 페이지가 존재하지 않거나 이동했을 수 있습니다. 
            <br>홈페이지로 돌아가시려면 <a href="<?= route('home') ?>" class="--underline">여기</a>를 클릭하세요.</p>
        </div>
    </div>
<?php end_section() ?>
