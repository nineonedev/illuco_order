<?php

use App\Domains\Order\Entities\Order;
use App\Domains\Order\Enums\OrderStatus;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'order') ?>
<?php section('action', 'show') ?>
<?php section('title', '주문 정보') ?>

<?php section('content') ?>
<div class="no-page-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">주문 정보</h1>

            <?php if ($order->isCanceled()): ?>
            <span>주문취소일: <?= $order->canceled_at ?></span>
            <?php endif; ?>
        </div>

        <div class="no-section-container">

            <!-- 상세정보 -->
            <section>
                <div class="no-order-summary">
                    <h2 class="no-order-summary__title">주문 정보</h2>
                    <div class="no-order-summary__grid">
                        <div class="no-order-summary__item">
                            <span class="label">주문 상태</span>
                            <span class="value"><?= __('system.order.status.' . $order->order_status) ?></span>
                        </div>
                        <div class="no-order-summary__item">
                            <span class="label">주문자 이름</span>
                            <span class="value"><?= e($order->orderer_name) ?></span>
                        </div>
                        <div class="no-order-summary__item">
                            <span class="label">이메일</span>
                            <span class="value"><?= e($order->orderer_email) ?></span>
                        </div>
                        <div class="no-order-summary__item">
                            <span class="label">전화번호</span>
                            <span class="value"><?= e($order->orderer_phone ?: '-') ?></span>
                        </div>
                        <div class="no-order-summary__item">
                            <span class="label">고객</span>
                            <span class="value"><?= e($order->customer->name ?? '-') ?></span>
                        </div>
                    </div>
                </div>

                <div class="no-order-memo">
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
                </div>

                <div class="no-form-action">
                    <a href="<?= route_with_query('admin.orders.index') ?>" class="no-btn-primary-outline --sm" data-action="cancel">목록</a>

                    <?php if ($order->isCanceled()) : ?>
                        <span class="no-text-error">이미 취소된 주문입니다.</span>

                    <?php elseif (!$order->isFinalized()) : ?>
                        <form action="<?= route('admin.orders.cancel', ['orderNo' => $order->order_no]) ?>" method="POST" id="cancel-frm">
                            <?= csrf_field() ?>
                            <?= put_field() ?>
                            <button type="submit" class="no-btn-error --sm">주문 취소</button>
                        </form>

                    <?php else : ?>
                        <a href="<?= route('admin.claims.create') ?>" class="no-btn-success --sm">클레임 접수</a>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 주문 제품 목록 -->
            <section>
                <div class="no-order-restore">
                    <div class="no-flex-between no-heading-margin">
                        <h2 class="no-order-restore__title">주문 제품 목록</h2>
                        <div>
                            <form method="post" action="<?= route('admin.orders.restore_all', ['orderId' => $order->id]) ?>" id="restore-form">
                                <?= csrf_field() ?>
                                <button type="submit" class="no-btn-success">전체 다시 장바구니에 담기</button>
                            </form>
                        </div>
                    </div>

                    <div class="no-page-index-table-outer">
                        <table class="no-page-index-table">
                            <thead>
                                <tr>
                                    <th>이미지</th>
                                    <th>제품명</th>
                                    <th>모델명</th>
                                    <th>가격</th>
                                    <th>옵션</th>
                                    <th>수량</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order->items as $item): 
                                    $product = $item->product;
                                    $type = $product->type;
                                    $sub = $type ? $product->{$type} : null;
                                ?>
                                <tr>
                                    <td style="width: 10rem">
                                        <?php if ($img = $product->template->fileattachment[0] ?? null): ?>
                                            <img src="<?= e($img->upload_path) ?>" alt="제품 이미지" style="width: 100%; min-width: 8rem; border-radius: .4rem;">
                                        <?php else: ?>
                                            <div class="no-order-restore__placeholder">No Image</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= e($product->name) ?></td>
                                    <td><?= e($product->model) ?></td>
                                    <td><?= '$' . e($product->price) ?></td>
                                    <td>
                                        <div class="no-order-option-tags">
                                            <?php if ($sub) : ?>
                                                <?php foreach ($sub->getAttributes() as $field => $value) :
                                                    if (in_array($field, ['id'])) continue;
                                                ?>
                                                    <div class="no-order-option-tag">
                                                        <span class="label"><?= lang('system.' . $type . '.' . $field) ?></span>
                                                        <span class="value"><?= e($value) ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?= $item->quantity ?></td>
                                </tr>

                                <?php if (!empty($item->sets)) : ?>
                                    <?php foreach ($item->sets as $setItem): 
                                        $setProduct = $setItem->product;
                                        $setType = $setProduct->type;
                                        $setSub = $setType ? $setProduct->{$setType} : null;
                                    ?>
                                    <tr class="no-order-subitem">
                                        <td style="padding-left: 2rem">
                                            <?php if ($img = $setProduct->template->fileattachment[0] ?? null): ?>
                                                <img src="<?= e($img->upload_path) ?>" alt="제품 이미지" style="width: 100%; min-width: 8rem; border-radius: .4rem;">
                                            <?php else: ?>
                                                <div class="no-order-restore__placeholder">No Image</div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= e($setProduct->name) ?></td>
                                        <td><?= e($setProduct->model) ?></td>
                                        <td><?= '$'.e($setProduct->price) ?></td>
                                        <td>
                                            <div class="no-order-option-tags">
                                                <?php if ($setSub) : ?>
                                                    <?php foreach ($setSub->getAttributes() as $field => $value) :
                                                        if (in_array($field, ['id'])) continue;
                                                    ?>
                                                        <div class="no-order-option-tag">
                                                            <span class="label"><?= __('loupe.' . $field) ?></span>
                                                            <span class="value"><?= e($value) ?></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><?= $setItem->quantity ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?php end_section() ?>
