<?php 

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'category') ?>
<?php section('action', 'index') ?>
<?php section('title', '카테고리 목록') ?>

<?php section('content') ?>
<div class="no-form-outer no-cart">

    <div class="no-cart-grid">
        <div class="no-cart-area">
            <div class="no-page-row">
                <div class="no-page-head">
                    <h1 class="no-heading-sm">카테고리</h1>
                    <p class="no-text-secondary">등록된 카테고리를 확인하실 수 있습니다.</p> 
                </div>
                <!-- Head -->

                <div>
                    <!-- <div class="no-category-menu">
                        Category Items
                    </div> -->

                    <?php if ($categories) : ?>
                        <ol class="no-category-items" id="category-list">
                            <?php foreach ($categories as $category): ?>
                            <li class="no-category-item">
                                <form action="<?= route('admin.product_categories.update', ['id' => $category->id]) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <?= put_field() ?>
                                    <div class="no-category-item-block">
                                        <div class="no-category-item-head">
                                            <div class="no-category-item-head-text">
                                                <div class="no-form-control">
                                                    <label for="category_label_<?=$category->id?>" class="no-form-control-inner">
                                                        <input type="text" name="label" id="category_label_<?=$category->id?>" value="<?= $category->label ?>" class="no-form-control-input " placeholder="" required="">
                                                        <fieldset class="no-form-control-label">
                                                            <legend class="no-form-control-text">이름</legend>
                                                        </fieldset>
                                                    </label>
                                                </div>
                                                <div class="no-form-control">
                                                    <label for="category_order<?=$category->id?>" class="no-form-control-inner">
                                                        <input type="number" name="sort_order" id="category_order<?=$category->id?>" value="<?= $category->sort_order ?>" class="no-form-control-input " placeholder="" required="">
                                                        <fieldset class="no-form-control-label">
                                                            <legend class="no-form-control-text">순서</legend>
                                                        </fieldset>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="no-form-action" style="margin-top: 0;">
                                            <button type="submit" data-item-action="edit" class="no-btn-primary-outline --xs">
                                                <span>수정</span>
                                            </button>
                                            <a href="<?= route('admin.product_categories.destroy', ['id' => $category->id]) ?>" data-item-action="delete" class="no-btn-error-outline --xs">
                                                <span>삭제</span>
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else: ?>
                    <div class="no-form-empty-fallback">
                        <p>등록된 카테고리가 없습니다.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Area -->
        <div class="no-cart-area">
            <div class="no-page-row">
                <div class="no-page-head">
                    <h1 class="no-heading-sm">카테고리 추가</h1>
                </div>
                
                <div id="category-form"></div>
            </div>
        </div>
        <!-- Area -->
    </div>
</div>

<?php end_section() ?>