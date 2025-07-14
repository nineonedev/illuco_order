<?php 

use App\Domains\Product\Entities\ProductTemplate;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'product_template') ?>
<?php section('action', 'create') ?>
<?php section('title', '제품 템플릿 생성') ?>

<?php section('content') ?>
<div class="no-form-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">제품 템플릿 생성</h1>
        </div>

        <form  
            method="post" 
            id="frm" 
            enctype="multipart/form-data" 
            action="<?= route('admin.product_templates.store') ?>"
        >
            <?= csrf_field() ?>
            
            <div class="no-form-inner">
                <div class="no-form-group">
                    <div class="no-form-control --md">
                        <label for="name" class="no-form-control-inner">
                            <input type="text" name="name" id="name" class="no-form-control-input" placeholder="" required>
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">제품 이름</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --md">
                        <label for="code" class="no-form-control-inner">
                            <input type="text" name="code" id="code" class="no-form-control-input" placeholder="" required>
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">제품 코드</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --md">
                        <label for="model" class="no-form-control-inner">
                            <input type="text" name="model" id="model" class="no-form-control-input" placeholder="" required>
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">모델명</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --md">
                        <label for="price" class="no-form-control-inner">
                            <input 
                                type="number" 
                                name="price" 
                                id="price" 
                                class="no-form-control-input" 
                                placeholder="" 
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
                    
                    <?php
                        $categoryProps = [
                            'label' => '카테고리',
                            'name' => 'category_id',
                            'value' => '',
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


                    <div data-component-type="file" data-component-props='{"file_key": "main_image", "label": "대표 이미지"}'></div>

                    <div class="no-form-control --md">
                        <label for="sort_order" class="no-form-control-inner">
                            <input type="number" name="sort_order" id="sort_order" class="no-form-control-input" placeholder="0">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">정렬 순서</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --textarea">
                        <label for="description" class="no-form-control-inner">
                            <textarea type="text" name="description" id="description" class="no-form-control-input" placeholder="" rows="8"></textarea>
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">제품 설명</legend>
                            </fieldset>
                        </label>
                    </div>

                </div>

                <div class="no-form-action">
                    <a href="<?= route('admin.product_templates.index') ?>" data-action="cancel" class="no-btn-primary-outline --sm">
                        <span>취소</span>
                    </a>
                    <button type="submit" class="no-btn-primary --sm">
                        <span>저장</span>
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>

<?php end_section() ?>
