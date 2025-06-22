<?php

use App\Domains\Order\Entities\Order;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'order') ?>
<?php section('action', 'edit') ?>
<?php section('title', '주문 상세') ?>

<?php section('content') ?>
<div class="no-page-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">주문 상세</h1>
        </div>

        <!-- START CONTENT -->
        <div class="no-order-layout">

            <!-- 1. 주문 정보 -->
            <section class="no-order-summary">
                <h2 class="no-order-summary__title">주문 정보</h2>
                <div class="no-order-summary__grid">
                    <div class="no-order-summary__item"><span class="label">주문자 이름</span><span class="value"><?= e($order->orderer_name) ?></span></div>
                    <div class="no-order-summary__item"><span class="label">이메일</span><span class="value"><?= e($order->orderer_email) ?></span></div>
                    <div class="no-order-summary__item"><span class="label">전화번호</span><span class="value"><?= e($order->orderer_phone ?: '-') ?></span></div>
                    <div class="no-order-summary__item"><span class="label">고객</span><span class="value"><?= e($order->customer->name ?? '-') ?></span></div>
                </div>
            </section>

            <!-- 2. 메모 및 집계 -->
            <section class="no-order-memo">
                <h2 class="no-order-memo__title">메모 및 집계</h2>
                <div class="no-order-memo__content">
                    <div class="memo">
                        <label>메모</label>
                        <p><?= nl2br(e($order->memo ?: '-')) ?></p>
                    </div>
                    <div class="summary">
                        <label>총 주문 금액</label>
                        <p class="amount"><?= number_format($order->total_amount, 2) ?> USD</p>
                    </div>
                </div>
            </section>

            <!-- 3. 상품 복원 카드 -->
            <section class="no-order-restore">
                <h2 class="no-order-restore__title">다시 장바구니에 담기</h2>

                <div class="no-page-index-table-outer">
                    <table class="no-page-index-table">
                        <thead>
                            <tr>
                                <th>이미지</th>
                                <th>제품명</th>
                                <th>모델명</th>
                                <th>코드</th>
                                <th>옵션</th>
                                <th>수량</th>
                                <th>복원</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order->items as $item): ?>
                            <?php $product = $item->product; ?>
                            <tr>
                                <td style="width: 10rem">
                                    <?php if ($img = $product->template->fileattachment[0] ?? null): ?>
                                        <img src="<?= e($img->upload_path) ?>" alt="제품 이미지" style="width: 100%; max-width: 8rem; border-radius: .4rem;">
                                    <?php else: ?>
                                        <div class="no-order-restore__placeholder">No Image</div>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($product->name) ?></td>
                                <td><?= e($product->model) ?></td>
                                <td><?= e($product->code) ?></td>
                                <td>
                                    <div class="no-order-option-tags">
                                        <?php foreach ($product->values as $value): ?>
                                            <?php if ($attribute = $value->attribute ?? null): ?>
                                                <div class="no-order-option-tag">
                                                    <span class="label"><?= e($attribute->label) ?></span>
                                                    <span class="value"><?= e($value->value) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td><?= $item->quantity ?></td>
                                <td>
                                    <form 
                                        data-view="order-item-form"
                                        method="post" 
                                        action="<?= route('admin.orders.restore_item', ['id' => $item->id]) ?>"
                                    >
                                        <?= csrf_field() ?>
                                        <button type="submit" class="no-btn-success-outline --xs">복원</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>


            <!-- 4. 주문 상태 수정 -->
            <section class="no-order-update">
                <h2 class="no-order-update__title">주문 상태 수정</h2>
                <form method="post" id="frm" action="<?= route('admin.orders.update', ['id' => $order->id]) ?>">
                    <?= csrf_field() ?>
                    <?= put_field() ?>
                    <input type="hidden" name="id" value="<?=$order->id?>">
                    <div id="order_status"
                        class="no-form-control --md"
                        data-view-props='{
                            "label": "주문 상태",
                            "name": "order_status",
                            "value": "<?= $order->order_status ?>",
                            "options": [
                                { "value": "<?= Order::STATUS_RECEIVED ?>", "label": "<?= __('system.order.status.received') ?>" },
                                { "value": "<?= Order::STATUS_CONFIRMED ?>", "label": "<?= __('system.order.status.confirmed') ?>" },
                                { "value": "<?= Order::STATUS_PREPARING ?>", "label": "<?= __('system.order.status.preparing') ?>" },
                                { "value": "<?= Order::STATUS_SHIPPED ?>", "label": "<?= __('system.order.status.shipped') ?>" }
                            ]
                        }'></div>
                    <div class="no-form-action">
                        <a href="<?= route_with_query('admin.orders.index') ?>" class="no-btn-primary-outline --sm">목록</a>
                        <button type="button" class="no-btn-error --sm" data-action="delete">주문 삭제</button>
                        <button type="submit" class="no-btn-primary --sm">상태 저장</button>
                    </div>
                </form>
                <div class="no-form-action" style="margin-top: 2rem;">
                    <form method="post" action="<?= route('admin.orders.restore_all') ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="no-btn-success">전체 다시 장바구니에 담기</button>
                    </form>
                </div>
            </section>


        </div>

        <!-- END CONTENT -->
    </div>

</div>
<?php end_section() ?>
