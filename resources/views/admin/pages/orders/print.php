<?php

use App\Domains\Order\Entities\Order;
use App\Domains\Order\Enums\OrderStatus;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'orderDocument') ?>
<?php section('action', 'print') ?>
<?php section('title', '주문 문서 출력') ?>

<?php section('content') ?>
<div class="no-page-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">주문 문서 편집</h1>
        </div>

        <!-- START CONTENT -->
        <div>
            <?= include_view('admin.pages.orders.prints.'.$document->type, ['document' => $document]) ?>
        </div>

        <div class="--flex-center">
            <a href="<?= route('admin.orders.edit', ['orderNo' => $document->order->order_no]) ?>" class="no-btn-primary-outline">목록</a>
            <button type="button" id="print-btn" class="no-btn-primary">PDF 다운로드</button>
        </div>

        <!-- END CONTENT -->
    </div>

</div>
<?php end_section() ?>
