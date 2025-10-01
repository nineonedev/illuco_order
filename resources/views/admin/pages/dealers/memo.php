<?php extend('layouts.admin'); ?>
<?php section('controller', 'dealerMemo') ?>
<?php section('action', 'edit') ?>
<?php section('title', '대리점 메모') ?>

<?php section('content') ?>
<div class="no-form-container">
    <div class="no-page-row">
        <div class="dealer-price-header" data-component="dealer-price-header">
            <div class="dealer-price-header__title">
                <h1 class="dealer-price-header__h1">대리점 메모</h1>
            </div>

            <dl class="dealer-price-header__meta">
                <div class="dealer-price-header__item">
                <dt class="dealer-price-header__label">대리점</dt>
                <dd class="dealer-price-header__value"><?= e($dealer->name ?? '') ?></dd>
                </div>

                <div class="dealer-price-header__item">
                <dt class="dealer-price-header__label">코드</dt>
                <dd class="dealer-price-header__value"><?= e($dealer->dealer->code ?? '') ?></dd>
                </div>
            </dl>
        </div>


        <form id="frm" method="post" enctype="multipart/form-data"
              action="<?= route('admin.dealer-memo.save', ['id' => $dealer->id]) ?>">
            <?= csrf_field() ?>

            <div class="no-form-group">
                <!-- 일반 메모 -->
                <div class="no-form-control --textarea">
                    <label for="memo_general" class="no-form-control-inner">
                        <textarea name="memo_general" id="memo_general" class="no-form-control-input" rows="8"
                                  placeholder="대리점과의 일반 메모를 입력하세요."><?= e($memo->memo_general ?? '') ?></textarea>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">일반 메모</legend>
                        </fieldset>
                    </label>
                </div>

                <!-- 일반 메모 핀 고정 -->
                <div class="no-form-block" style="margin-top:8px;">
                    <div class="no-form-checkbox --sm">
                        <label for="pin_general" class="no-form-checkbox-pointer">
                            <input
                                type="checkbox"
                                name="is_pinned_general"
                                id="pin_general"
                                class="no-form-checkbox-input"
                                value="1"
                                <?= !empty($memo) && !empty($memo->is_pinned_general) ? 'checked' : '' ?>
                            >
                            <div class="no-form-checkbox-ripple">
                                <span class="no-form-checkbox-box">
                                    <div class="no-form-checkbox-icon">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </span>
                            </div>
                            <span class="no-form-checkbox-text">일반 메모 상단 고정</span>
                        </label>
                    </div>
                </div>

                

                <!-- 생산팀 메모 -->
                <div class="no-form-control --textarea" style="margin-top:18px;">
                    <label for="memo_production" class="no-form-control-inner">
                        <textarea name="memo_production" id="memo_production" class="no-form-control-input" rows="8"
                                  placeholder="생산팀과 공유할 메모를 입력하세요."><?= e($memo->memo_production ?? '') ?></textarea>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">생산팀 메모</legend>
                        </fieldset>
                    </label>
                </div>

                <!-- 생산팀 메모 핀 고정 -->
                <div class="no-form-block" style="margin-top:8px;">
                    <div class="no-form-checkbox --sm">
                        <label for="pin_prod" class="no-form-checkbox-pointer">
                            <input
                                type="checkbox"
                                name="is_pinned_prod"
                                id="pin_prod"
                                class="no-form-checkbox-input"
                                value="1"
                                <?= !empty($memo) && !empty($memo->is_pinned_prod) ? 'checked' : '' ?>
                            >
                            <div class="no-form-checkbox-ripple">
                                <span class="no-form-checkbox-box">
                                    <div class="no-form-checkbox-icon">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </span>
                            </div>
                            <span class="no-form-checkbox-text">생산팀 메모 상단 고정</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="no-form-action">
                <a href="<?= route('admin.dealers.index') ?>" data-action="cancel" class="no-btn-primary-outline --sm">
                    <span>취소</span>
                </a>
                <button type="submit" class="no-btn-primary --sm">
                    <span>저장</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php end_section() ?>
