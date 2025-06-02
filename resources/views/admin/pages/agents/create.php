<?php extend('layouts.admin'); ?>
<?php section('title') ?>
대리점 생성
<?php endSection() ?>

<?php section('content') ?>

<div class="no-form-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">대리점 생성</h1>
        </div>
        <!-- Head -->
        
        <form method="post" enctype="multipart/form-data" action="<?= route('notices.store') ?>">
            <div class="no-form-group">
                <div class="no-form-control --md">
                    <label for="name" class="no-form-control-inner">
                        <input type="text" name="name" id="name" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">대리점명</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                <!-- FormControl -->

                <div class="no-form-control --md">
                    <label for="manager_name" class="no-form-control-inner">
                        <input type="text" name="manager_name" id="manager_name" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">대표자</legend>
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
                    <label for="address" class="no-form-control-inner">
                        <input type="text" name="address" id="address" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">주소</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                <!-- FormControl -->

                <div class="no-form-checkbox --sm">
                    <label for="is_visible" class="no-form-checkbox-pointer">
                        <input type="checkbox" name="is_visible" id="is_visible" class="no-form-checkbox-input" checked>
                        <div class="no-form-checkbox-ripple">
                            <span class="no-form-checkbox-box">
                                <div class="no-form-checkbox-icon">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </span>
                        </div>
                        <span class="no-form-checkbox-text">노출여부</span>
                    </label>
                    <p class="no-form-checkbox-helper-text">해당 대리점을 노출시킵니다.</p>
                    <span class="no-form-control-space"></span>
                </div>

            </div>
            
            <div class="no-form-action">
                <a href="<?=route('agents.index')?>" class="no-btn-primary-outline --sm">
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
