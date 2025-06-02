<?php extend('layouts.admin'); ?>
<?php section('title') ?>
주문자 생성
<?php endSection() ?>

<?php section('content') ?>

<div class="no-form-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">주문자 생성</h1>
        </div>
        <!-- Head -->
        
        <form method="post" enctype="multipart/form-data" action="<?= route('notices.store') ?>">
            <div class="no-form-group">
                <div class="no-form-control --md">
                    <label for="countries" class="no-form-control-inner">
                        <input type="text" name="countries" id="countries" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">국가선택</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                
                <!-- FormControl -->
                <div class="no-form-control --md">
                    <label for="agent_id" class="no-form-control-inner">
                        <input type="text" name="agent_id" id="agent_id" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">대리점 선택</legend>
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
                    <label for="age" class="no-form-control-inner">
                        <input type="number" name="age" id="age" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">나이</legend>
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

                
                <div class="--flex-column">
                    <fieldset class="no-form-group">
                        <legend class="no-form-base-label">성별</legend>
                        <div class="no-form-listing">
                            <div class="no-form-radio --sm">
                                <label class="no-form-radio-pointer" for="gender_male">
                                    <input class="no-form-radio-input" type="radio" name="gender" id="gender_male" value="M">
                                    <div class="no-form-radio-ripple">
                                        <div class="no-form-radio-box">
                                            <span class="no-form-radio-icon"></span>
                                        </div>
                                    </div>
                                    <span class="no-form-radio-text">남</span>
                                </label>
                            </div>
                            <div class="no-form-radio --sm">
                                <label class="no-form-radio-pointer" for="gender_female">
                                    <input class="no-form-radio-input" type="radio" name="gender" id="gender_female" value="F">
                                    <div class="no-form-radio-ripple">
                                        <div class="no-form-radio-box">
                                            <span class="no-form-radio-icon"></span>
                                        </div>
                                    </div>
                                    <span class="no-form-radio-text">여</span>
                                </label>
                            </div>
                            <div class="no-form-radio --sm">
                                <label class="no-form-radio-pointer" for="gender_unknown">
                                    <input class="no-form-radio-input" type="radio" name="gender" id="gender_unknown" value="U">
                                    <div class="no-form-radio-ripple">
                                        <div class="no-form-radio-box">
                                            <span class="no-form-radio-icon"></span>
                                        </div>
                                    </div>
                                    <span class="no-form-radio-text">알 수 없음</span>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    <span class="no-form-control-space"></span>
                </div>


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
                    <p class="no-form-checkbox-helper-text">해당 주문자를 노출시킵니다.</p>
                    <span class="no-form-control-space"></span>
                </div>

            </div>
            
            <div class="no-form-action">
                <a href="<?= route('guests.index') ?>" class="no-btn-primary-outline --sm">
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
