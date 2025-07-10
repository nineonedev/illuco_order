<?php extend('layouts.mail'); ?>

<?php section('title', '[일루코] 주문 상태 변경 알림') ?>

<?php section('style') ?>
<style>
    body {
        margin: 0;
        padding: 0;
        background: #f5f6fa;
    }

    .no-order-mail {
        background: #ffffff;
        border: 1px solid #e1e5eb;
        border-radius: 8px;
        padding: 24px;
        max-width: 800px;
        margin: 40px auto;
        font-family: 'Noto Sans KR', sans-serif;
        color: #333333;
    }

    .no-order-mail__title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 16px;
        color: #0052cc;
    }

    .no-order-mail__box {
        background: #f9fafb;
        border: 1px solid #e1e5eb;
        border-radius: 6px;
        padding: 16px;
        margin-bottom: 20px;
    }

    .no-order-mail__box p {
        margin: 8px 0;
        font-size: 14px;
    }

    .no-order-mail__box .label {
        display: inline-block;
        width: 120px;
        font-weight: 600;
        color: #555;
    }

    .no-order-mail__box .value {
        color: #222;
    }

    .no-order-mail__table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }

    .no-order-mail__table th,
    .no-order-mail__table td {
        border: 1px solid #e1e5eb;
        padding: 8px;
        font-size: 13px;
        text-align: left;
        vertical-align: top;
    }

    .no-order-mail__table th {
        background: #f1f3f5;
        font-weight: 600;
    }

    .no-order-mail__total {
        text-align: right;
        margin-top: 12px;
        font-weight: 700;
        font-size: 14px;
    }

    .no-order-mail__action {
        text-align: center;
        margin-top: 24px;
    }

    .no-order-mail__btn {
        display: inline-block;
        background: #0052cc;
        color: #ffffff !important;
        text-decoration: none;
        padding: 12px 24px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        transition: background 0.3s;
    }

    .no-order-mail__btn:hover {
        background: #003d99;
    }

    img.no-order-mail__product-img {
        max-width: 60px;
        border-radius: 4px;
    }
</style>
<?php end_section() ?>

<?php section('content') ?>
<div class="no-order-mail">
    <h1 class="no-order-mail__title">주문 상태 변경 알림</h1>
    
    <p style="margin-bottom: 16px;">
        주문 상태가 변경되었습니다. 현재 상태는
        <strong><?= __('system.order.status.'.$order->order_status) ?></strong> 입니다.
    </p>

    <!-- 주문자 정보 -->
    <div class="no-order-mail__box">
        <h2 style="font-size:16px; font-weight:600; margin-bottom:10px; color:#0052cc;">주문자 정보</h2>
        <p><span class="label">주문자</span> <span class="value"><?= e($order->orderer_name) ?></span></p>
        <p><span class="label">이메일</span> <span class="value"><?= e($order->orderer_email) ?></span></p>
        <p><span class="label">전화번호</span> <span class="value"><?= e($order->orderer_phone ?? '-') ?></span></p>
    </div>

    <!-- 대리점 정보 -->
    <div class="no-order-mail__box">
        <h2 style="font-size:16px; font-weight:600; margin-bottom:10px; color:#0052cc;">대리점 정보</h2>
        <p><span class="label">대리점명</span> <span class="value"><?= e($order->dealer->name ?? '-') ?></span></p>
        <p><span class="label">이메일</span> <span class="value"><?= e($order->dealer->email ?? '-') ?></span></p>
        <p><span class="label">전화번호</span> <span class="value"><?= e($order->dealer->phone ?? '-') ?></span></p>
    </div>

    <!-- 고객 정보 -->
    <div class="no-order-mail__box">
        <h2 style="font-size:16px; font-weight:600; margin-bottom:10px; color:#0052cc;">고객 정보</h2>
        <p><span class="label">고객명</span> <span class="value"><?= e($order->customer->name ?? '-') ?></span></p>
        <p><span class="label">이메일</span> <span class="value"><?= e($order->customer->email ?? '-') ?></span></p>
        <p><span class="label">전화번호</span> <span class="value"><?= e($order->customer->phone ?? '-') ?></span></p>
    </div>

    <table class="no-order-mail__table">
        <thead>
            <tr>
                <th>이미지</th>
                <th>제품명</th>
                <th>모델명</th>
                <th>수량</th>
                <th>단가</th>
                <th>소계</th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0; ?>
            <?php foreach ($order->items as $item): ?>
                <?php
                    $product = $item->product;
                    $img = $product->template->fileattachment[0]->upload_url ?? null;
                    $subtotal = $item->quantity * $product->price;
                    $total += $subtotal;
                ?>
                <tr>
                    <td>
                        <?php if ($img): ?>
                            <img src="<?= e($img) ?>" alt="제품 이미지" class="no-order-mail__product-img">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= e($product->name) ?></td>
                    <td><?= e($product->model) ?></td>
                    <td><?= e($item->quantity) ?></td>
                    <td><?= number_format($product->price, 2) ?> USD</td>
                    <td><?= number_format($subtotal, 2) ?> USD</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="no-order-mail__total">
        총 주문 금액: <?= number_format($total, 2) ?> USD
    </div>

    <div class="no-order-mail__action">
        <a href="<?= request()->http()->origin() . route('admin.orders.edit', ['orderNo' => $order->order_no]) ?>" class="no-order-mail__btn">
            주문 상세 보기
        </a>
    </div>
</div>
<?php end_section() ?>
