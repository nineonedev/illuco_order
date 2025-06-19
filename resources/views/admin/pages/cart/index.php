<?php extend('layouts.admin'); ?>
<?php section('controller', 'cart') ?>
<?php section('action', 'index') ?>
<?php section('title', '주문') ?>

<?php section('content') ?>
<div class="no-page-flex">
    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">주문</h1>
            <p>해당 대리점에 등록된 사용자의 주문을 대행 할 수 있습니다.</p>
        </div>


        <?php
            $customerProps = json([
                'name' => 'customer_id',
                'label' => '고객',
                'fallback' => true,
                'options' => $customers
            ]);

            $templateProps = json([
                'name' => 'template_id',
                'label' => '제품',
                'fallback' => true,
                'options' => $templates
            ]);
        ?>
        <form  
            method="post" 
            id="frm" 
            enctype="multipart/form-data" 
            action="<?= route('admin.cart.store') ?>"
        >
            <div class="no-form-inner">
                <div class="no-form-group">
                    <div 
                        id="customer_id" 
                        data-component-type="select" 
                        data-component-props='<?=$customerProps?>'>
                    </div>
                </div>
                <div class="no-form-group">
                    <div 
                        id="template_id" 
                        data-component-type="select" 
                        data-component-props='<?=$templateProps?>'>
                    </div>
                </div>
            </div>

            <hr class="no-hr --xl">

            <div class="no-form-action">
                <button type="button" class="no-btn-error-outline --sm" data-action="delete">
                    <span>삭제</span>
                </button>
                <button type="submit" class="no-btn-primary --sm" data-action="put">
                    <span>수정</span>
                </button>
            </div>
        </form>
    </div>

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">장바구니 (0)</h1>
        </div>

        <div 
            id="cart-hook" 
            data-component-props='{
            "action": "<?=route("admin.cart.store")?>"
        }'></div>
    </div>
</div>

<?php end_section() ?>

<?php section('portal') ?>
<div id="modal-hook"></div>
<?php end_section() ?>