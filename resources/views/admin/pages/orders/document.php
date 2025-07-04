<?php

use App\Domains\Order\Entities\Order;
use App\Domains\Order\Enums\OrderStatus;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'orderDocument') ?>
<?php section('action', 'edit') ?>
<?php section('title', '주문 문서 수정') ?>

<?php section('content') ?>
<div class="no-page-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">문서 편집</h1>
        </div>

        <!-- START CONTENT -->
        <div>
            <?= include_view('admin.pages.orders.docs.'.$document->type, ['document' => $document]) ?>
        </div>

        <!-- END CONTENT -->
    </div>

</div>
<?php end_section() ?>
