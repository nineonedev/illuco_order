<?php 

use App\Domains\User\Entities\Dealer;?>
<?php extend('layouts.admin'); ?>
<?php section('controller', 'customer') ?>
<?php section('action', 'edit') ?>
<?php section('title', '주문자 수정') ?>

<?php section('content') ?>
<div class="no-form-container">
    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">주문자 수정</h1>
        </div>

        <form id="frm" method="post" enctype="multipart/form-data" action="<?= route('admin.customers.update', ['id' => $customer->id]) ?>">
            <?= csrf_field() ?>
            <?= put_field() ?>
            <div class="no-form-group">
                <div 
                    id="country-hook" 
                    data-component-type="country-select" 
                    data-component-props='{
                        "name": "country",
                        "label": "국가 선택",
                        "value": "<?= $customer->country ?? "KR" ?>"
                    }'>
                </div>

                <?php if (user()->userable instanceof Dealer) : ?>
                <input type="hidden" name="dealer_id" value="<?=user()->userable->id?>">
                <?php endif; ?>

                <div class="no-form-control --md">
                    <label for="name" class="no-form-control-inner">
                        <input type="text" name="name" id="name" class="no-form-control-input" value="<?= e($customer->name) ?>" required>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이름</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --md">
                    <label for="phone" class="no-form-control-inner">
                        <input type="tel" name="phone" id="phone" class="no-form-control-input" value="<?= e($customer->phone) ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">연락처</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --md">
                    <label for="email" class="no-form-control-inner">
                        <input type="email" name="email" id="email" class="no-form-control-input" value="<?= e($customer->email) ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이메일</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --md">
                    <label for="age" class="no-form-control-inner">
                        <input type="number" step="1" min="1" max="100" name="age" id="age" class="no-form-control-input" placeholder="" value="<?= e($customer->age) ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">나이</legend>
                        </fieldset>
                    </label>
                    
                    <span class="no-form-control-space"></span>
                </div>
                <div class="no-form-control --md">
                    <label for="address" class="no-form-control-inner">
                        <input type="text" name="address" id="address" class="no-form-control-input" placeholder="" value="<?= e($customer->address) ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">주소</legend>
                        </fieldset>
                    </label>
                    
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --textarea">
                    <label for="description" class="no-form-control-inner">
                        <textarea name="description" id="description" class="no-form-control-input" rows="8"><?= e($customer->description) ?></textarea>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">설명</legend>
                        </fieldset>
                    </label>
                </div>
            </div>

            <div class="no-form-action">
                <a href="<?= route('admin.customers.index') ?>" data-action="cancel" class="no-btn-primary-outline --sm">
                    <span>취소</span>
                </a>
                <button type="button" class="no-btn-error-outline --sm" data-action="delete">
                    <span>삭제</span>
                </button>
                <button type="submit" class="no-btn-primary --sm">
                    <span>저장</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php end_section() ?>
