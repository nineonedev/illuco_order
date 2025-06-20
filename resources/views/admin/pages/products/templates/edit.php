<?php

use App\Domains\Product\Entities\ProductTemplate;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'product_template') ?>
<?php section('action', 'edit') ?>
<?php section('title', '제품 템플릿 수정') ?>

<?php section('content') ?>
<div class="no-page-flex">

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
                    <div 
                        class="no-form-control --md" 
                        data-component-type="select" 
                        data-component-props='{
                            "label": "카테고리", 
                            "name": "category_id", 
                            "value": "<?=$template->category_id?>"
                        }'
                    ></div>

                    <!-- 대표 이미지 -->
                    <?php $mainImage = $template->fileattachment->props('main_image', ['label' => '메인 이미지']) ?? '{}'; ?>
                    <div data-component-type="file" data-component-props='<?= $mainImage ?>'></div>

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

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">제품 속성 관리</h1>
        </div>

        <div 
            id="attr-hook" 
            data-component-props='{
            "template_id": <?=route_param('id')?>,
            "action": "<?=route("admin.product_attributes.store")?>"
        }'></div>
    </div>
</div>


<?php end_section() ?>

<?php section('portal') ?>
<div id="modal-hook"></div>
<?php end_section() ?>