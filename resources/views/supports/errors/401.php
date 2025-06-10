<?php extend('layouts.auth') ?>

<?php section('title') ?>
401 인증이 필요합니다.
<?php end_section() ?>

<?php section('content') ?>
    <div class="no-section-xl no-auth-layout">
        <div class="no-error-wrap">
            <h1>401</h1>
            <h2>인증이 필요합니다.</h2>
            <p>이 페이지에 접근하려면 로그인이 필요합니다. 
            <br>로그인 하시려면 <a href="<?= route('auth.signin') ?>" class="--underline">여기</a>를 클릭하세요.</p>
        </div>
    </div>
<?php end_section() ?>
