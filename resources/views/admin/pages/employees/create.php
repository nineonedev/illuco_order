<?php extend('layouts.admin'); ?>

<?php section('title') ?>
직원 생성
<?php endSection() ?>

<?php section('content') ?>
<div class="no-form-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">직원 생성</h1>
            <p class="no-text-secondary">신규 직원을 등록합니다.</p> 
        </div>
        <!-- Head -->

        <form action="<?=route('employees.store')?>" method="post" class="no-form" enctype="multipart/form-data">
            <div class="no-form-group">
                <div class="no-form-control --md">
                    <label for="countries" class="no-form-control-inner">
                        <input type="text" name="countries" id="countries" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">국가</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                <!-- FormControl -->
                <div class="no-form-control --md">
                    <label for="agent_id" class="no-form-control-inner">
                        <input type="text" name="agent_id" id="agent_id" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">대리점</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                <!-- FormControl -->

                <div class="no-form-control --md">
                    <label for="name" class="no-form-control-inner">
                        <input type="text" name="name" id="name" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이름</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                <!-- FormControl -->

                <div class="no-form-control --md">
                    <label for="phone" class="no-form-control-inner">
                        <input type="text" name="phone" id="phone" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">연락처</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                <!-- FormControl -->

                <div class="no-form-control --md">
                    <label for="email" class="no-form-control-inner">
                        <input type="text" name="email" id="email" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이메일</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                <!-- FormControl -->

                <div class="no-form-control --md">
                    <label for="username" class="no-form-control-inner">
                        <input type="text" name="username" id="username" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">아이디</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                <!-- FormControl -->

                <div class="no-form-control --md --password">
                    <label for="password" class="no-form-control-inner">
                        <input type="password" name="password" id="password" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">비밀번호</legend>
                        </fieldset>
                        <div class="no-form-password-icon">
                            <button class="no-form-password-button no-btn-icon --xs" type="button" data-password-button="#password">
                                <i class="fa-regular fa-eye-slash"></i>
                            </button>
                        </div>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                <!-- FormControl -->


                <div class="no-form-checkbox --sm">
                    <label for="is_visible" class="no-form-checkbox-pointer">
                        <input type="checkbox" name="is_visible" id="is_visible" class="no-form-checkbox-input">
                        <div class="no-form-checkbox-ripple">
                            <span class="no-form-checkbox-box">
                                <div class="no-form-checkbox-icon">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </span>
                        </div>
                        <span class="no-form-checkbox-text">엑세스 제한</span>
                    </label>
                    <p class="no-form-checkbox-helper-text">관리자 접속을 일시적으로 제한합니다.</p>
                    <span class="no-form-control-space"></span>
                </div>

            </div>
            
            <div class="no-form-action">
                <a href="<?= route('employees.index') ?>" class="no-btn-primary-outline --sm">
                    <span>취소</span>
                </a>
                <button type="submit" class="no-btn-primary --sm">
                    <span>저장</span>
                </button>
            </div>
        </form>
    </div>
    <!-- Row -->
</div>
<?php endSection() ?>
