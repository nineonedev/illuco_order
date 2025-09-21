<?php

use App\Domains\Product\Entities\ProductSerial;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'order') ?>
<?php section('action', 'serial') ?>
<?php section('title', 'Serial Lookup') ?>

<?php section('content') ?>
<div class="no-page-container">
    <form method="get">
        <div class="no-page-row">
            <div class="no-page-head">
                <h1 class="no-heading-sm">시리얼 번호 조회</h1>
                <p class="no-text-secondary">시리얼 번호를 입력하면 관련 정보를 바로 확인할 수 있습니다.</p>
            </div>

            <div class="no-page-index-filter">
                <div class="no-page-index-filter__form">
                    <!-- 🔎 시리얼 단일 입력 -->
                    <div class="no-form-search --lg" style="max-width:560px">
                        <label for="serial" class="no-form-label">시리얼 번호</label>
                        <div class="no-form-search-inner">
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-barcode-read"></i>
                            </div>
                            <input
                                type="search"
                                name="serial"
                                id="serial"
                                value="<?= e($query['serial'] ?? '') ?>"
                                class="no-form-search-input no-text-mono"
                                placeholder="예: DSNNN25000003A"
                                autofocus
                                required
                            >
                        </div>
                    </div>

                    <div class="no-page-index-link">
                        <button type="submit" class="no-btn-primary --sm">조회</button>
                        <a href="<?= route('admin.orders.serial') ?>" class="no-btn-success --sm">초기화</a>
                    </div>
                </div>
            </div>

            <?php if (!empty($serial)) : ?>
                <?php
                    $product   = $serial->product ?? null;
                    $template  = $product->template ?? null;
                    $orderItem = $serial->orderItem ?? null;
                    $order     = $orderItem->order ?? null;

                    $dealerName = '-';
                    if ($order && $order->user && $order->user->isDealer()) {
                        $dealerName = $order->user->name;
                    } elseif ($order && $order->customer && $order->customer->dealer) {
                        $dealerName = $order->customer->dealer->user->name ?? '-';
                    }
                ?>

                <!-- ✅ 결과 카드 -->
                <div class="no-section-container">
                    <section class="no-order-summary">
                        <h2 class="no-order-summary__title">조회 결과</h2>
                        <div class="no-order-summary__grid">
                            <div class="no-order-summary__item">
                                <span class="label">시리얼 번호</span>
                                <span class="value no-text-mono"><?= e($serial->serial_number) ?></span>
                            </div>
                            <!-- <div class="no-order-summary__item">
                                <span class="label">상태</span>
                                <span class="value">
                                    <span class="order-status --<?= e($serial->status) ?>"><?= e(ucfirst($serial->status)) ?></span>
                                </span>
                            </div> -->
                            <div class="no-order-summary__item">
                                <span class="label">생성일</span>
                                <span class="value"><?= date('Y-m-d h:i A', strtotime($serial->created_at ?? 'now')) ?></span>
                            </div>
                        </div>
                    </section>

                    <section class="no-order-summary">
                        <h3 class="no-order-summary__title">제품 정보</h3>
                        <div class="no-order-summary__grid">
                            <div class="no-order-summary__item"><span class="label">제품명</span><span class="value"><?= e($template->name ?? $product->name ?? '-') ?></span></div>
                            <div class="no-order-summary__item"><span class="label">모델</span><span class="value"><?= e($template->model ?? '-') ?></span></div>
                            <div class="no-order-summary__item"><span class="label">코드</span><span class="value"><?= e($product->code ?? '-') ?></span></div>
                            <div class="no-order-summary__item"><span class="label">단가</span><span class="value"><?= isset($template->price) ? number_format($template->price, 2) . ' USD' : '-' ?></span></div>
                        </div>
                    </section>

                    <section class="no-order-summary">
                        <h3 class="no-order-summary__title">주문 정보</h3>
                        <div class="no-order-summary__grid">
                            <div class="no-order-summary__item">
                                <span class="label">주문번호</span>
                                <span class="value">
                                    <?php if ($order): ?>
                                        <?php $detailLink = user()->isDealer()
                                            ? route('admin.orders.show', ['orderNo' => $order->order_no])
                                            : route('admin.orders.edit', ['orderNo' => $order->order_no]); ?>
                                        <a class="--underline" href="<?= $detailLink ?>"><?= e($order->order_no) ?></a>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="no-order-summary__item"><span class="label">고객</span><span class="value"><?= e($order->customer->name ?? '-') ?></span></div>
                            <div class="no-order-summary__item"><span class="label">대리점</span><span class="value"><?= e($dealerName) ?></span></div>
                            <div class="no-order-summary__item">
                                <span class="label">주문 상태</span>
                                <span class="value">
                                    <?php if ($order): ?>
                                        <span class="order-status --<?= e($order->order_status) ?>">
                                            <?= e(__('system.order.status.' . $order->order_status)) ?>
                                        </span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </section>

                    <?php if (!empty($siblings ?? [])) : ?>
                    <!-- <section>
                        <h3 class="no-order-update__title">동일 Prefix 시리얼(최근)</h3>
                        <div class="no-page-index-table-outer">
                            <table class="no-page-index-table">
                                <thead>
                                    <tr>
                                        <th>Serial</th>
                                        <th>Status</th>
                                        <th>생성일</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($siblings as $s) : ?>
                                        <tr>
                                            <td class="no-text-mono"><?= e($s->serial_number) ?></td>
                                            <td><span class="order-status --<?= e($s->status) ?>"><?= e(ucfirst($s->status)) ?></span></td>
                                            <td><?= date('Y-m-d h:i A', strtotime($s->created_at ?? 'now')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section> -->
                    <?php endif; ?>
                </div>

            <?php elseif (isset($query['serial'])): ?>
                <div class="no-form-empty-fallback">
                    <p>입력하신 시리얼 <b class="no-text-mono"><?= e($query['serial']) ?></b> 에 대한 결과가 없습니다.</p>
                </div>
            <?php endif; ?>

        </div>
    </form>
</div>
<?php end_section() ?>
