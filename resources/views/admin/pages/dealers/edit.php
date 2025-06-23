<?php extend('layouts.admin'); ?>
<?php section('controller', 'dealer') ?>
<?php section('action', 'edit') ?>
<?php section('title', '대리점 수정') ?>

<?php section('content') ?>
<div class="no-form-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">대리점 수정</h1>
        </div>
        
        <form id="frm" method="post" enctype="multipart/form-data" action="<?= route('admin.dealers.update', ['id' => $dealer->id]) ?>">
            <?= csrf_field() ?>
            <?= method_field('patch') ?>

            <div class="no-form-group">
                <div class="no-form-control --md">
                    <label for="name" class="no-form-control-inner">
                        <input type="text" name="name" id="name" class="no-form-control-input" value="<?= e($dealer->user->name) ?> " required>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이름</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --md">
                    <label for="email" class="no-form-control-inner">
                        <input type="email" name="email" id="email" class="no-form-control-input" value="<?= e($dealer->user->email) ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이메일</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                
                <div class="no-form-control --md">
                    <label for="password" class="no-form-control-inner">
                        <input type="password" name="password" id="password" class="no-form-control-input" placeholder="">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">비밀번호</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-helper-text">비밀번호는 변경 시 입력해주세요.</span>
                    <span class="no-form-control-space"></span>
                </div>
                
                <div 
                    id="country-hook" 
                    data-component-type="country-select" 
                    data-component-props='{
                        "name": "country",
                        "label": "국가 선택",
                        "value": "<?=e($dealer->country) ?? 'KR'?>"
                    }'>
                    <!-- <div class="no-form-control --md">
                        <label for="country" class="no-form-control-inner">
                            <input type="text" name="country" id="country" class="no-form-control-input">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">국가 선택</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div> -->
                </div>

                <div class="no-form-control --md">
                    <label for="code" class="no-form-control-inner">
                        <input type="text" name="code" id="code" class="no-form-control-input" value="<?= e($dealer->code) ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">코드</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --md">
                    <label for="phone_number" class="no-form-control-inner">
                        <input type="tel" name="phone_number" id="phone_number" class="no-form-control-input" value="<?= e($dealer->phone_number ?? '') ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">연락처</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --md">
                    <label for="address" class="no-form-control-inner">
                        <input type="text" name="address" id="address" class="no-form-control-input" value="<?= e($dealer->address ?? '') ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">주소</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --textarea">
                    <label for="description" class="no-form-control-inner">
                        <textarea name="description" id="description" class="no-form-control-input" rows="6"><?= e($dealer->description ?? '') ?></textarea>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">설명</legend>
                        </fieldset>
                    </label>
                </div>

            </div>

            <div class="no-form-action">
                <a href="<?= route('admin.dealers.index') ?>" data-action="cancel" class="no-btn-primary-outline --sm">
                    <span>취소</span>
                </a>
                <?php if(can('dealer.delete')) : ?>
                <button type="button" class="no-btn-error-outline --sm" data-action="delete">
                    <span>삭제</span>
                </button>
                <?php endif; ?>
                <button type="submit" class="no-btn-primary --sm">
                    <span>저장</span>
                </button>
            </div>

        </form>
    </div>
</div>
<?php end_section() ?>
