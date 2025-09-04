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
        
        <!-- TAB MENU START -->
        <div class="no-base-tab-container">
            <ul class="no-base-tabs">
                <li class="no-base-tab">
                    <button class="no-base-tab-btn">
                        <span>기본정보</span>
                    </button>
                </li>
                <li class="no-base-tab">
                    <button class="no-base-tab-btn">
                        <span>비밀번호</span>
                    </button>
                </li>
                <li class="no-base-tab">
                    <button class="no-base-tab-btn">
                        <span>임시 비밀번호</span>
                    </button>
                </li>
            </ul>
        </div>
        <!-- TAB MENU END -->

        <!-- TAB CONTENT START -->
        <div class="no-base-tab-contents">
            <!-- 기본정보 -->
            <section>
                <form id="frm" method="post" enctype="multipart/form-data" action="<?= route('admin.dealers.update', ['id' => $dealer->id]) ?>">
                    <?= csrf_field() ?>
                    <?= method_field('put') ?>

                    <div class="no-form-group">
                        <div class="no-form-control --md">
                            <label for="name" class="no-form-control-inner">
                                <input type="text" name="name" id="name" class="no-form-control-input" value="<?= e($dealer->name ?? '') ?>" required>
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

                        <?php if (can('dealer.delete')): ?>
                        <!-- 활성여부 스위치 (필요 시 주석 해제) -->
                        <?php endif ?>

                        <hr class="no-hr--xl">

                        <div 
                            data-view-type="country-select" 
                            data-view-props='<?= e(json_encode([
                                "name" => "dealer[country]",
                                "label" => "국가 선택",
                                "value" => $dealer->dealer->country ?? '',
                                "options" => __('system.countries'),
                            ])) ?>'>
                        </div>

                        <?php if (!user()->isDealer()) :?>
                        <?php
                            $props = [
                                'label' => '제품군 선택',
                                'name'  => 'dealer[category_id]',
                                'value' => $dealer->dealer->category_id,
                                'options' => array_merge([['label' => '전체', 'value' => '']], array_map(
                                    fn($c) => ['label' => $c->label, 'value' => $c->id],
                                    $categories
                                )),
                            ];
                        ?>
                        <div 
                            class="no-form-field"
                            data-view-type="select"
                            data-view-props='<?= e(json_encode($props)) ?>'
                        ></div>
                        <?php endif; ?>

                        <?php if (!user()->isDealer()) :?>
                        <div class="no-form-control --md">
                            <label for="code" class="no-form-control-inner">
                                <input type="text" name="dealer[code]" id="code" class="no-form-control-input" value="<?= e($dealer->dealer->code) ?>">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">코드</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                        <?php endif; ?>

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
            </section>

            <!-- 비밀번호 변경 -->
            <section>
                <form 
                    id="password-frm" 
                    method="post" 
                    enctype="multipart/form-data" 
                    action="<?= route('admin.dealers.password.update', ['id' => $dealer->id]) ?>"
                >
                    <?= csrf_field() ?>
                    <?= method_field('put') ?>

                    <div class="no-form-control --md">
                        <label for="password" class="no-form-control-inner">
                            <input type="password" name="password" id="password" class="no-form-control-input" placeholder="">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">비밀번호</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --md">
                        <label for="password_confirmation" class="no-form-control-inner">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="no-form-control-input" placeholder="">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">비밀번호 확인</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <button type="submit" class="no-btn-primary --sm">
                        <span>변경</span>
                    </button>
                </form>
            </section>

            <!-- 임시 비밀번호 -->
            <section>
                <div class="">
                    <form 
                        id="temp-frm" 
                        method="post" 
                        enctype="multipart/form-data" 
                        action="<?= route('admin.dealers.temp-password.issue', ['id' => $dealer->id]) ?>"
                    >
                        <?= csrf_field() ?>
                        <?= method_field('post') ?>

                        <!-- (선택) 유효시간 조절 -->
                        <!-- <div class="no-form-control --sm" style="max-width:240px">
                            <label class="no-form-control-inner">
                                <input type="number" min="1" max="168" name="hours" value="24" class="no-form-control-input">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">유효시간(시간)</legend>
                                </fieldset>
                            </label>
                        </div> -->
                        <button type="submit" class="no-btn-primary --sm">
                            <span>임시 비밀번호 발급</span>
                        </button>

                        <div id="temp-hook"></div>
                    </form>
                    <hr>
                    
                    <!-- 임시비번 회수(무효화) -->
                    <form id="revoke-frm" method="post" action="<?= route('admin.dealers.temp-password.revoke', ['id' => $dealer->id]) ?>" onsubmit="return confirm('현재 임시 비밀번호를 무효화할까요?')">
                        <?= csrf_field() ?>
                        <?= method_field('delete') ?>
                        <button type="submit" class="no-btn-error-outline --sm">
                            <span>임시 비밀번호 회수</span>
                        </button>
                        <p class="no-help" style="margin-top:6px">발급된 임시 비밀번호는 화면에 1회만 표시됩니다.</p>
                    </form>
                </div>

            </section>
        </div>
        <!-- TAB CONTENT END -->
    </div>
</div>
<?php end_section() ?>
