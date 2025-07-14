<?php extend('layouts.admin'); ?>
<?php section('controller', 'dealer') ?>
<?php section('action', 'create') ?>
<?php section('title', '대리점 생성') ?>

<?php section('content') ?>
<div class="no-form-container">
    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">대리점 생성</h1>
        </div>

        <form id="frm" method="post" enctype="multipart/form-data" action="<?= route('admin.dealers.store') ?>">
            <?= csrf_field() ?>

            <div class="no-form-group">
                <!-- 이름 -->
                <div class="no-form-control --md">
                    <label for="name" class="no-form-control-inner">
                        <input type="text" name="name" id="name" class="no-form-control-input" required placeholder="">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이름</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 이메일 -->
                <div class="no-form-control --md">
                    <label for="email" class="no-form-control-inner">
                        <input type="email" name="email" id="email" class="no-form-control-input" required placeholder="">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이메일</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                 <!-- 전화번호 -->
                <div class="no-form-control --md">
                    <label for="phone" class="no-form-control-inner">
                        <input type="tel" name="phone" id="phone" class="no-form-control-input" placeholder="">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">전화번호</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 비밀번호 -->
                <div class="no-form-control --md">
                    <label for="password" class="no-form-control-inner">
                        <input type="password" name="password" id="password" class="no-form-control-input" required placeholder="">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">비밀번호</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 국가 -->
                <div 
                    data-view-type="country-select" 
                    data-view-props='<?= e(json_encode([
                        "name" => "dealer[country]",
                        "label" => "국가 선택",
                        "value" => '',
                        "options" => __('system.countries'),
                    ])) ?>'>
                </div>

                
                <?php
                    $props = [
                        'label' => '제품군 선택',
                        'name' => 'dealer[category_id]',
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

                <!-- 코드 -->
                <div class="no-form-control --md">
                    <label for="code" class="no-form-control-inner">
                        <input type="text" name="dealer[code]" id="code" class="no-form-control-input" placeholder="">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">코드</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 주소 -->
                <div class="no-form-control --md">
                    <label for="address" class="no-form-control-inner">
                        <input type="text" name="dealer[address]" id="address" class="no-form-control-input" placeholder="">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">주소</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 설명 -->
                <div class="no-form-control --textarea">
                    <label for="description" class="no-form-control-inner">
                        <textarea name="dealer[description]" id="description" class="no-form-control-input" rows="5" placeholder=""></textarea>
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
                <button type="submit" class="no-btn-primary --sm">
                    <span>저장</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php end_section() ?>
