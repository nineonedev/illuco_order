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
                    <div class="no-category-menu">
                        Category Items
                    </div>

                    <div id="category-list"></div>
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