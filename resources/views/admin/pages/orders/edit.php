<?php

use App\Domains\Order\Entities\Order;
use App\Domains\Order\Enums\OrderStatus;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'order') ?>
<?php section('action', 'edit') ?>
<?php section('title', '주문 정보') ?>

<?php section('content') ?>
<div class="no-page-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">주문 정보</h1>
        </div>

        <!-- <div class="no-base-tab-container">
            <ul class="no-base-tabs">
                <li class="no-base-tab">
                    <button class="no-base-tab-btn">
                        <span>수정</span>
                        <span class="no-base-tab-badge">0</span>
                    </button>
                </li>
                <li class="no-base-tab">
                    <button class="no-base-tab-btn">
                        <span>문서</span>
                    </button>
                </li>
                <li class="no-base-tab">
                    <button class="no-base-tab-btn">
                        <span>상세정보</span>
                    </button>
                </li>
                <li class="no-base-tab">
                    <button class="no-base-tab-btn">
                        <span>히스토리</span>
                    </button>
                </li>
                <li class="no-base-tab">
                    <button class="no-base-tab-btn">
                        <span>미수금</span>
                    </button>
                </li>
            </ul>
        </div> -->

        <div class="no-section-container">
            <!-- 수정 -->
            <section class="no-page-flex">
                <div>
                    <h2 class="no-order-update__title">주문 수정</h2>

                    <form method="post" id="frm" action="<?= route('admin.orders.update', ['id' => $order->id]) ?>">
                        <?= csrf_field() ?>
                        <?= put_field() ?>
                        <input type="hidden" name="id" value="<?=$order->id?>">

                        <div class="no-form-group">
                            <div 
                                class="no-form-control --md"
                                data-view-type="select"
                                data-view-props='{
                                    "label": "주문 상태",
                                    "name": "order_status",
                                    "value": "<?= $order->order_status ?>",
                                    "options": [
                                        { 
                                            "value": "<?= OrderStatus::NEW ?>", 
                                            "label": "<?= __('system.order.status.'.OrderStatus::NEW) ?>",
                                            "disabled": true
                                        },
                                        { 
                                            "value": "<?= OrderStatus::CONFIRMED ?>", 
                                            "label": "<?= __('system.order.status.'.OrderStatus::CONFIRMED) ?>" 
                                        },
                                        { 
                                            "value": "<?= OrderStatus::PREPARING ?>", 
                                            "label": "<?= __('system.order.status.'.OrderStatus::PREPARING) ?>" 
                                        },
                                        { 
                                            "value": "<?= OrderStatus::SHIPPED ?>", 
                                            "label": "<?= __('system.order.status.'.OrderStatus::SHIPPED) ?>" 
                                        },
                                        { 
                                            "value": "<?= OrderStatus::COMPLETED ?>", 
                                            "label": "<?= __('system.order.status.'.OrderStatus::COMPLETED) ?>" 
                                        },
                                        { 
                                            "value": "<?= OrderStatus::CANCELED ?>", 
                                            "label": "<?= __('system.order.status.'.OrderStatus::CANCELED) ?>",
                                            "disabled": true
                                        },
                                        { 
                                            "value": "<?= OrderStatus::REJECTED ?>", 
                                            "label": "<?= __('system.order.status.'.OrderStatus::REJECTED) ?>"
                                        }
                                    ]
                                }'
                            ></div>

                            <div 
                                data-view-type="date"
                                data-view-props='{
                                    "label": "발주일",
                                    "name": "payment_date",
                                    "value": "<?= $order->payment_date ?? ''?>"
                                }'
                            ></div>
                            <div 
                                data-view-type="date"
                                data-view-props='{
                                    "label": "납기일",
                                    "name": "delivery_date",
                                    "value": "<?= $order->delivery_date ?? ''?>"
                                }'
                            ></div>
                            <div 
                                data-view-type="date"
                                data-view-props='{
                                    "label": "출하일",
                                    "name": "shipping_date",
                                    "value": "<?= $order->shipping_date ?? ''?>"
                                }'
                            ></div>
                        </div>

                        <div class="no-form-action">
                            <a href="<?= route_with_query('admin.orders.index') ?>" class="no-btn-primary-outline --sm">목록</a>
                            <button type="button" class="no-btn-error --sm" data-action="delete">주문 삭제</button>
                            <button type="submit" class="no-btn-primary --sm">상태 저장</button>
                        </div>
                    </form>
                </div>

                <div class="no-order-history">
                    <h2 class="no-order-update__title">오더 히스토리</h2>

                    <!-- 등록 Form -->
                    <form method="post" action="<?= route('admin.order_histories.store') ?>" id="history-form">
                        <?= csrf_field() ?>

                        <input type="hidden" name="order_id" value="<?= $order->id ?>">
                        <input type="hidden" name="dealer_id" value="<?= $order->dealer_id ?? '' ?>">
                        <input type="hidden" name="customer_id" value="<?= $order->customer_id ?? ''?>">

                        <div class="no-form-group">

                            <div class="no-form-control">
                                <label for="balance" class="no-form-control-inner">
                                    <input type="number" name="balance" id="balance" class="no-form-control-input" placeholder="미수금 변동액 (예: -500 또는 1000)" step="0.01">
                                    <fieldset class="no-form-control-label">
                                        <legend class="no-form-control-text">미수금 변동액</legend>
                                    </fieldset>
                                </label>
                                <span class="no-form-control-space"></span>
                            </div>

                            <div class="no-form-checkbox --md">
                                <label for="settled" class="no-form-checkbox-pointer">
                                    <input type="checkbox" name="settled" id="settled" class="no-form-checkbox-input" value="1">
                                    <div class="no-form-checkbox-ripple">
                                        <span class="no-form-checkbox-box">
                                            <div class="no-form-checkbox-icon">
                                                <i class="fa-solid fa-check"></i>
                                            </div>
                                        </span>
                                    </div>
                                    <span class="no-form-checkbox-text">처리 완료</span>
                                </label>
                                 <span class="no-form-control-space"></span>
                            </div>

                            <div class="no-form-control --textarea">
                                <label for="memo" class="no-form-control-inner">
                                    <textarea name="memo" id="memo" class="no-form-control-input" rows="4" placeholder="메모를 입력하세요" required></textarea>
                                    <fieldset class="no-form-control-label">
                                        <legend class="no-form-control-text">메모</legend>
                                    </fieldset>
                                </label>
                            </div>

                        </div>

                        <div class="no-form-action">
                            <button type="submit" class="no-btn-primary --sm">히스토리 추가</button>
                        </div>
                    </form>
                </div>

            </section>

            <section>
                <div class="no-section-container">
                    <div class="no-page-flex --xl">
                        <div>
                            <div class="no-order-history-list">
                                <h3 class="no-order-update__title">상태 변경 내역</h3>
                                <table class="no-page-index-table">
                                    <thead>
                                        <tr>
                                            <th>변경자</th>
                                            <th>이전 상태</th>
                                            <th>변경 후 상태</th>
                                            <!-- <th>사유/메모</th> -->
                                            <th>변경일</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orderLogs as $log): ?>
                                            <tr>
                                                <td><?= e($log->user->name ?? '-') ?></td>
                                                <td><?= $log->previous_status ? __('system.order.status.' . $log->previous_status) : '-' ?></td>
                                                <td><?= __('system.order.status.' . $log->status) ?></td>
                                                <!-- <td><?= nl2br(e($log->message ?? '-')) ?></td> -->
                                                <td><?= date('Y-m-d h:i A', strtotime($log->created_at)) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div>
                            <div class="no-order-history-list">
                                <h3 class="no-order-update__title">히스토리 내역</h3>
                                <table class="no-page-index-table">
                                    <thead>
                                        <tr>
                                            <th>대상</th>
                                            <th>주문번호</th>
                                            <th>작성자</th>
                                            <th>메모</th>
                                            <th>미수금 변동</th>
                                            <th>처리 여부</th>
                                            <th>작성일</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orderHistories as $history) : ?>
                                            <?php
                                            dump($history); 
                                                // 타깃(대상) 결정
                                                if ($history->dealer) {
                                                    $target = $history->dealer->user->name ?? '대리점';
                                                } elseif ($history->customer) {
                                                    $target = $history->customer->name ?? '고객';
                                                } else {
                                                    $target = '-';
                                                }

                                                $orderNo = $history->order->order_no;
                                            ?>
                                            <tr>
                                                <td><?= e($target) ?></td>
                                                <td>
                                                    <a class="--underline" href="<?= route('admin.orders.edit', ['orderNo' => $orderNo]) ?>"><?= e($orderNo) ?></a>
                                                </td>
                                                <td><?= e($history->user->name ?? '-') ?></td>
                                                <td><?= nl2br(e($history->memo)) ?></td>
                                                <td><?= number_format($history->balance, 2) ?> USD</td>
                                                <td><?= $history->settled ? '완료' : '미처리' ?></td>
                                                <td><?= date('Y-m-d h:i A', strtotime($history->created_at)) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <!-- 문서 -->
            <section>
                <h2 class="no-order-update__title">주문 문서</h2>
                <div>
                    <ul class="no-order-docs-list">
                        <li class="no-order-docs-item">
                            <div class="no-order-docs-item__title">
                                <span>PI</span>
                                <h3>Proforma Inovice</h3>
                            </div>
                            <div class="no-order-docs-item__action">
                                <a href="<?= route('admin.order_documents.print', ['documentNo' => $order->proforma_invoice->document->document_no]) ?>" class="no-btn-success-outline --xs">다운로드</a>
                                <a href="<?= route('admin.order_documents.edit', ['documentNo' => $order->proforma_invoice->document->document_no]) ?>" class="no-btn-premium-outline --xs">편집</a>
                            </div>
                        </li>
                        <li class="no-order-docs-item">
                            <div class="no-order-docs-item__title">
                                <span>PR</span>
                                <h3>생산의뢰서</h3>
                            </div>
                            <div class="no-order-docs-item__action">
                                <a href="<?= route('admin.order_documents.print', ['documentNo' => $order->product_request->document->document_no]) ?>" class="no-btn-success-outline --xs">다운로드</a>
                                <a href="<?= route('admin.order_documents.edit', ['documentNo' => $order->product_request->document->document_no]) ?>" class="no-btn-premium-outline --xs">편집</a>
                            </div>
                        </li>
                        <li class="no-order-docs-item">
                            <div class="no-order-docs-item__title">
                                <span>PL</span>
                                <h3>Packing List</h3>
                            </div>
                            <div class="no-order-docs-item__action">
                                <a href="<?= route('admin.order_documents.print', ['documentNo' => $order->packing_list->document->document_no]) ?>" class="no-btn-success-outline --xs">다운로드</a>
                                <a href="<?= route('admin.order_documents.edit', ['documentNo' => $order->packing_list->document->document_no]) ?>" class="no-btn-premium-outline --xs">편집</a>
                            </div>
                        </li>
                        <li class="no-order-docs-item">
                            <div class="no-order-docs-item__title">
                                <span>CI</span>
                                <h3>Commercial Inovice</h3>
                            </div>
                            <div>
                                <a href="<?= route('admin.order_documents.print', ['documentNo' => $order->commercial_invoice->document->document_no]) ?>" class="no-btn-success-outline --xs">다운로드</a>
                                <a href="<?= route('admin.order_documents.edit', ['documentNo' => $order->commercial_invoice->document->document_no]) ?>" class="no-btn-premium-outline --xs">편집</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- 상세정보 -->
            <section>
                <!-- 1. 주문 정보 -->
                <div class="no-order-summary">
                    <h2 class="no-order-summary__title">주문 정보</h2>
                    <div class="no-order-summary__grid">
                        <div class="no-order-summary__item"><span class="label">주문자 이름</span><span class="value"><?= e($order->orderer_name) ?></span></div>
                        <div class="no-order-summary__item"><span class="label">이메일</span><span class="value"><?= e($order->orderer_email) ?></span></div>
                        <div class="no-order-summary__item"><span class="label">전화번호</span><span class="value"><?= e($order->orderer_phone ?: '-') ?></span></div>
                        <div class="no-order-summary__item"><span class="label">고객</span><span class="value"><?= e($order->customer->name ?? '-') ?></span></div>
                    </div>
                </div>

                <!-- 2. 메모 및 집계 -->
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
                
                <!-- 3. 상품 복원 카드 -->
                <div class="no-order-restore">
                    <div class="no-flex-between">
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
                </div>
            </section>
        </div>
    </div>

</div>
<?php end_section() ?>
