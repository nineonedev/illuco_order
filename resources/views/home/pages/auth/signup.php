<?php

use App\Domains\User\Entities\Admin;
use App\User\Repositories\PostRepository;
use App\User\Repositories\UserRepository;
use App\User\Resources\UserResource;
use Framework\Database\ORM\RelationMap;

?>
<?php extend('layouts.auth') ?>

<?php section('title'); ?>
로그인
<?php end_section() ?>

<?php section('content') ?>

<section class="no-section-xl no-auth-layout">
    <div class="no-auth-container">
        <div class="no-auth-inner">
            <header>
                <div class="no-auth-logo">
                    <img src="<?= asset_path('img/meta/logo-white.svg') ?>" alt="ILLUCO" class="no-logo-img">
                </div>
                <h1 class="no-heading-md">Sign up</h1>
                <p>
                    일루코 오더 관리 시스템에 오신 것을 환영합니다.<br>
                    계정을 생성하여 서비스를 이용해보세요.
                </p>
            </header>
            <div>
                <form method="post" id="auth-form">
                    <?= csrf_field() ?>
                    <div class="no-form-control">
                        <label for="name" class="no-form-control-inner">
                            <input 
                                type="name" 
                                name="name" 
                                id="name" 
                                class="no-form-control-input" 
                                placeholder="" 
                                required
                            >
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">이름</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>
                    
                    <!-- FormControl -->
                    <div class="no-form-control">
                        <label for="email" class="no-form-control-inner">
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                class="no-form-control-input" 
                                placeholder="" 
                                required
                            >
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">이메일</legend>
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

                    <button type="submit" class="no-btn-primary no-btn-submit">회원가입</button>
                    <span class="no-form-control-space"></span>
                    <p class="--tac">
                        이미 회원이신가요? <a href="<?=route('auth.signin')?>" class="--underline">로그인</a>
                    </p>

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
            headers: {
                'Accept': 'application/json',
            },
            method: e.target.method,
            body: fd,
        }); 
        const resData = await response.json(); 

        console.log(resData);
        
    })
</script>
<?php end_section() ?>