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
                        <input type="text" name="name" id="name" class="no-form-control-input" value="<?= e($dealer->name ?? '') ?> " required>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이름</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --md">
                    <label for="email" class="no-form-control-inner">
                        <input type="email" name="email" id="email" class="no-form-control-input" value="<?= e($dealer->email ?? '') ?>" required>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이메일</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                
                <div class="no-form-control --md">
                    <label for="phone" class="no-form-control-inner">
                        <input type="tel" name="phone" id="phone" class="no-form-control-input" value="<?= e($dealer->phone ?? '') ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">연락처</legend>
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

                <?php if (can('dealer.delete')): ?>
                <div class="no-form-block">
                    <div class="no-form-checkbox --md">
                        <label for="is_active" class="no-form-checkbox-pointer">
                            <input type="checkbox" name="is_active" id="is_active" class="no-form-checkbox-input" <?= $dealer->is_active ? 'checked' : '' ?> >
                            <div class="no-form-checkbox-ripple">
                                <span class="no-form-checkbox-box">
                                    <div class="no-form-checkbox-icon">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </span>
                            </div>
                            <span class="no-form-checkbox-text">활성 여부</span>
                        </label>
                        <p class="no-form-radio-helper-text">체크 해제할 경우 해당 계정은 비활성화됩니다.</p>
                    </div>
                </div>
                <?php endif ?>

                <hr class="no-hr--xl">

                <div 
                    id="country-hook" 
                    data-view-type="country-select" 
                    data-view-props='{
                        "name": "dealer[country]",
                        "label": "국가 선택",
                        "value": "<?=e($dealer->dealer->country) ?? 'KR'?>"
                    }'>
                </div>

                <?php
                    $props = [
                        'label' => '제품군 선택',
                        'name' => 'dealer[category_id]',
                        'value' => $dealer->dealer->category_id,
                        'options' => array_merge([['label' => '전체', 'value' => '']], array_map(
                            fn($c) => [
                                'label' => $c->label, 
                                'value' => $c->id,
                            ],
                            $categories
                        )),
                    ];
                ?>
                <div 
                    class="no-form-field"
                    data-view-type="select"
                    data-view-props='<?= e(json_encode($props)) ?>'
                ></div>


                <div class="no-form-control --md">
                    <label for="code" class="no-form-control-inner">
                        <input type="text" name="dealer[code]" id="code" class="no-form-control-input" value="<?= e($dealer->dealer->code) ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">코드</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --md">
                    <label for="address" class="no-form-control-inner">
                        <input type="text" name="dealer[address]" id="address" class="no-form-control-input" value="<?= e($dealer->dealer->address ?? '') ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">주소</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --textarea">
                    <label for="description" class="no-form-control-inner">
                        <textarea name="dealer[description]" id="description" class="no-form-control-input" rows="6"><?= e($dealer->dealer->description ?? '') ?></textarea>
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
