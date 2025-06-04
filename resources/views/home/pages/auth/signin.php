<?php 
    use App\User\Models\User
?>
<?php extend('layouts.auth') ?>

<?php section('title'); ?>
로그인
<?php end_section() ?>

<?php section('content') ?>
<?php
    dump(User::withRelations(['posts'])::all());
?>
<section class="no-section-xl no-auth-layout">
    <div class="no-auth-container">
        <div class="no-auth-inner">
            <header>
                <div class="no-auth-logo">
                    <img src="<?= asset_path('img/meta/logo-white.svg') ?>" alt="ILLUCO" class="no-logo-img">
                </div>
                <h1 class="no-heading-md">Sign in</h1>
                <p>
                    일루코 오더 관리 시스템에 오신 것을 환영합니다.<br>
                    아이디와 비밀번호를 입력하여 로그인하세요.
                </p>
            </header>
            <div>
                <form method="post" action="<?=route('auth.login')?>" id="auth-form">
                    <div class="no-form-control">
                        <label for="username" class="no-form-control-inner">
                            <input 
                                type="text" 
                                name="username" 
                                id="username" 
                                class="no-form-control-input" 
                                placeholder="" 
                                required
                            >
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">아이디</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>
                    <!-- FormControl -->
                    
                    <div class="no-form-control">
                        <label for="password" class="no-form-control-inner">
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                class="no-form-control-input" 
                                placeholder="" 
                                required
                            >
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">비밀번호</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>
                    <!-- FormControl -->

                    <div class="no-form-checkbox --sm">
                        <label for="auto_signin" class="no-form-checkbox-pointer">
                            <input type="checkbox" name="auto_signin" id="auto_signin" class="no-form-checkbox-input">
                            <div class="no-form-checkbox-ripple">
                                <span class="no-form-checkbox-box">
                                    <div class="no-form-checkbox-icon">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </span>
                            </div>
                            <span class="no-form-checkbox-text">자동로그인</span>
                        </label>
                    </div>
                    <!-- FormControl -->

                    <button type="submit" class="no-btn-primary no-btn-submit">로그인</button>

                </form>
            </div>
        </div>
    </div>
</section>

<?php end_section() ?>

<?php section('script') ?>
<script>
    const frm = document.querySelector('form'); 

    frm.addEventListener('submit', async (e) => {
        e.preventDefault(); 
        
        const fd = new FormData(e.target); 

        const response = await fetch(e.target.action, {
            method: e.target.method,
            body: fd,
        }); 
        const resData = await response.json(); 

        console.log(resData);
        
    })
</script>
<?php end_section() ?>