<?php

use App\Domains\Order\Entities\Order;
use App\Domains\Order\Enums\OrderStatus;

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
                <h2 class="no-order-restore__title">주문 제품 목록</h2>

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
                            <?php foreach ($order->items as $item): 
                                $product = $item->product;
                                $type = $item->product->type;
                                $sub = $type ? $item->product->{$type} : null;
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
                                <td><?= e($product->code) ?></td>
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
                                <td>
                                    <form 
                                        data-view="order-item-form"
                                        method="post" 
                                        action="<?= route('admin.orders.restore_item', ['orderItemId' => $item->id]) ?>"
                                    >
                                        <?= csrf_field() ?>
                                        <button type="submit" class="no-btn-success-outline --xs">복원</button>
                                    </form>
                                </td>
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
                                    <td><?= e($setProduct->code) ?></td>
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
                                    <td>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>


            <?php if (user()->isDealer()) : ?>
            <div class="no-form-action">
                <a href="<?= route_with_query('admin.orders.index') ?>" class="no-btn-primary-outline --sm">목록</a>
            </div>
            <?php else : ?>
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
                                { "value": "<?= OrderStatus::NEW ?>", "label": "<?= __('system.order.status.'.OrderStatus::NEW) ?>" },
                                { "value": "<?= OrderStatus::CONFIRMED ?>", "label": "<?= __('system.order.status.'.OrderStatus::CONFIRMED) ?>" },
                                { "value": "<?= OrderStatus::PREPARING ?>", "label": "<?= __('system.order.status.'.OrderStatus::PREPARING) ?>" },
                                { "value": "<?= OrderStatus::SHIPPED ?>", "label": "<?= __('system.order.status.'.OrderStatus::SHIPPED) ?>" }
                            ]
                        }'></div>
                    <div class="no-form-action">
                        <a href="<?= route_with_query('admin.orders.index') ?>" class="no-btn-primary-outline --sm">목록</a>
                        <button type="button" class="no-btn-error --sm" data-action="delete">주문 삭제</button>
                        <button type="submit" class="no-btn-primary --sm">상태 저장</button>
                    </div>
                </form>
                <div class="no-form-action" style="margin-top: 2rem;">
                    <form method="post" action="<?= route('admin.orders.restore_all', ['orderId' => $order->id]) ?>" id="restore-form">
                        <?= csrf_field() ?>
                        <button type="submit" class="no-btn-success">전체 다시 장바구니에 담기</button>
                    </form>
                </div>
            </section>
            <?php endif; ?>


        </div>

        <!-- END CONTENT -->
    </div>

</div>
<?php end_section() ?>
