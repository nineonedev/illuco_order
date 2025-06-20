<?php extend('layouts.admin'); ?>
<?php section('controller', 'cart') ?>
<?php section('action', 'index') ?>
<?php section('title', '주문') ?>

<?php section('content') ?>
<div class="no-page-row">
    <div class="no-page-head">
        <h1 class="no-heading-sm">대상 선택</h1>
        <p>등록된 사용자의 주문을 대행 할 수 있습니다.</p>
    </div>

    <div class="no-page-flex">
        <div id="template-hook"></div>
        <div id="cart-hook"></div>
    </div>
</div>


<?php end_section() ?>

<?php section('portal') ?>
<div id="modal-hook"></div>
<?php end_section() ?>