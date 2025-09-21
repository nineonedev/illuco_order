<?php

use App\Domains\Product\Entities\ProductTemplate;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'product_template') ?>
<?php section('action', 'edit') ?>
<?php section('title', '제품 템플릿 수정') ?>

<?php section('content') ?>
<!-- <div class="no-page-flex"> -->
<div class="no-form-container">


    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">제품 템플릿 수정</h1>
        </div>

        <form  
            method="post" 
            id="frm" 
            enctype="multipart/form-data" 
            action="<?= route('admin.product_templates.update', ['id' => $template->id]) ?>"
            data-show-action="<?=route('admin.product_templates.show', ['id' => $template->id])?>"
        >
            <?= csrf_field() ?>
            <?= put_field() ?>

            <div class="no-form-inner">
                <div class="no-form-group">

                    <!-- 제품 이름 -->
                    <div class="no-form-control --md">
                        <label for="name" class="no-form-control-inner">
                            <input type="text" name="name" id="name" class="no-form-control-input"
                                   value="<?= e(old('name', $template->name)) ?>" required>
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">제품 이름</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <!-- 제품 코드 -->
                    <div class="no-form-control --md">
                        <label for="code" class="no-form-control-inner">
                            <input type="text" name="code" id="code" class="no-form-control-input"
                                   value="<?= e(old('code', $template->code)) ?>" required>
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">제품 코드</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <!-- 모델명 -->
                    <div class="no-form-control --md">
                        <label for="model" class="no-form-control-inner">
                            <input type="text" name="model" id="model" class="no-form-control-input"
                                   value="<?= e(old('model', $template->model)) ?>" required>
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">모델명</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <!-- 🔢 시리얼번호 규칙(모델약어/특수약자/리비전) -->
                    <div class="no-form-divider --lg">
                        <h3 class="no-heading-xs">시리얼번호 규칙</h3>
                        <p class="no-text-secondary no-mt-4">
                            모델약어 - 특수약자 - 연도 - 연속번호 - 리비전 (예: <code>DSNNN25000003A</code>)<br>
                            연도/연속번호는 자동 부여됩니다.
                        </p>
                    </div>

                    <!-- 모델 약어 -->
                    <div class="no-form-control --md">
                        <label for="serial_abbr" class="no-form-control-inner">
                            <input 
                                type="text" 
                                name="serial_abbr" 
                                id="serial_abbr" 
                                class="no-form-control-input" 
                                placeholder="예: DS"
                                maxlength="8"
                                value="<?= e(old('serial_abbr', $template->serial_abbr ?? '')) ?>"
                            >
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">모델 약어</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <!-- 특수약자 (A~Z) -->
                    <?php
                        $alphabet = array_map(fn($c) => ['label' => $c, 'value' => $c], range('A', 'Z'));
                        array_unshift($alphabet, ['label' => '선택', 'value' => '']);
                        $specialProps = [
                            'label'   => '특수약자',
                            'name'    => 'serial_special',
                            'value'   => old('serial_special', $template->serial_special ?? ''),
                            'options' => $alphabet,
                        ];
                    ?>
                    <div
                        class="no-form-field"
                        data-view-type="select"
                        data-view-props='<?= e(json_encode($specialProps)) ?>'
                    ></div>

                    <!-- 리비전 (A~Z) -->
                    <?php
                        $revisionProps = [
                            'label'   => '리비전',
                            'name'    => 'serial_revision',
                            'value'   => old('serial_revision', $template->serial_revision ?? ''),
                            'options' => $alphabet,
                        ];
                    ?>
                    <div
                        class="no-form-field"
                        data-view-type="select"
                        data-view-props='<?= e(json_encode($revisionProps)) ?>'
                    ></div>


                    <!-- 단가 -->
                    <div class="no-form-control --md">
                        <label for="price" class="no-form-control-inner">
                            <input 
                                type="number" 
                                name="price" 
                                id="price" 
                                class="no-form-control-input"
                                value="<?= e(old('price', $template->price)) ?>" 
                                step="0.01" 
                                min="0"
                                required
                            >
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">단가 (USD)</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <!-- 카테고리 ID -->
                    <?php
                        $categoryProps = [
                            'label' => '카테고리',
                            'name' => 'category_id',
                            'value' => $template->category->id ?? '',
                            'options' => array_merge(
                                [['label' => '선택', 'value' => '']],
                                array_map(fn($category) => [
                                    'label' => $category->label,
                                    'value' => $category->id,
                                ], $categories)
                            ),
                        ];
                    ?>
                    <div
                        class="no-form-field"
                        data-view-type="select"
                        data-view-props='<?= e(json_encode($categoryProps)) ?>'
                    ></div>

                    <!-- 대표 이미지 -->
                    <?php $mainImage = $template->fileattachment->props('main_image', ['label' => '메인 이미지']) ?? '{}'; ?>
                    <div data-view-type="file" data-view-props='<?= $mainImage ?>'></div>

                    <!-- 정렬 순서 -->
                    <div class="no-form-control --md">
                        <label for="sort_order" class="no-form-control-inner">
                            <input type="number" name="sort_order" id="sort_order" class="no-form-control-input"
                                   value="<?= e(old('sort_order', $template->sort_order ?? 0)) ?>">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">정렬 순서</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>
                    

                    <!-- 제품 설명 -->
                    <div class="no-form-control --textarea">
                        <label for="description" class="no-form-control-inner">
                            <textarea name="description" id="description" class="no-form-control-input" rows="8"><?= e(old('description', $template->description)) ?></textarea>
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">제품 설명</legend>
                            </fieldset>
                        </label>
                    </div>

                </div>

                <div class="no-form-action">
                    <a href="<?= route('admin.product_templates.index') ?>" class="no-btn-primary-outline --sm" data-action="cancel">
                        <span>취소</span>
                    </a>

                    <button type="button" class="no-btn-error-outline --sm" data-action="delete">
                        <span>삭제</span>
                    </button>
                    <button type="submit" class="no-btn-primary --sm" data-action="put">
                        <span>수정</span>
                    </button>
                </div>

            </div>
        </form>
    </div>

</div>


<?php end_section() ?>

<?php section('portal') ?>
<div id="modal-hook"></div>
<?php end_section() ?>