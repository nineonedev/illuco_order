<?php

use Framework\Support\ValueObjects\Money;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'orders') ?>
<?php section('action', 'index') ?>
<?php section('title', '주문 목록') ?>

<?php section('content') ?>
<div class="no-page-container">
    <form method="get">
        <div class="no-page-row">
            <div class="no-page-head">
                <h1 class="no-heading-sm">주문 목록</h1>
                <p class="no-text-secondary">
                    대리점 또는 고객의 주문 내역을 확인하실 수 있습니다.
                </p>
            </div>

            <div class="no-page-index-filter">
                <div class="no-page-index-filter__form">
                    <div class="no-form-search --sm">
                        <label for="query" class="no-form-search-inner">
                            <fieldset class="no-form-search-label --blind">
                                <legend class="no-form-search-text">검색</legend>
                            </fieldset>
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input type="search" name="query" id="query" class="no-form-search-input" placeholder="이름, 이메일, 연락처 검색">
                        </label>
                    </div>
                </div>

                <div class="no-page-index-link">
                    <a href="<?= '#' //route('admin.orders.export') ?>" class="no-btn-success --sm">
                        <span>Export</span>
                    </a>
                </div>
            </div>

            <div class="no-page-index-table-outer">
                <table class="no-page-index-table">
                    <thead>
                        <tr>
                            <th class="no-table-check">
                                <div class="no-form-checkbox --xs">
                                    <label for="all" class="no-form-checkbox-pointer">
                                        <input type="checkbox" id="all" class="no-form-checkbox-input">
                                        <div class="no-form-checkbox-ripple">
                                            <span class="no-form-checkbox-box">
                                                <div class="no-form-checkbox-icon">
                                                    <i class="fa-solid fa-check"></i>
                                                </div>
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            </th>
                            <th>주문자</th>
                            <?php if (!user()->isDealer()) : ?>
                            <th>대리점</th>
                            <?php endif; ?>
                            <th>연락처</th>
                            <th>이메일</th>
                            <th>총 금액</th>
                            <th>상태</th>
                            <th>주문일</th>
                            <th>작업</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders->items() as $order): ?>
                        <tr class="no-table-hover">
                            <td class="no-table-check">
                                <div class="no-form-checkbox --xs">
                                    <label for="order<?= $order->id ?>" class="no-form-checkbox-pointer">
                                        <input type="checkbox" name="checked_ids[]" id="order<?= $order->id ?>" value="<?= $order->id ?>" class="no-form-checkbox-input">
                                        <div class="no-form-checkbox-ripple">
                                            <span class="no-form-checkbox-box">
                                                <div class="no-form-checkbox-icon">
                                                    <i class="fa-solid fa-check"></i>
                                                </div>
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            </td>
                            <td><?= e($order->orderer_name) ?></td>
                            <?php if (!user()->isDealer()) : ?>
                            <td>
                                <?php if($order->user->isDealer()) : ?>
                                <a href="<?= route('admin.dealers.edit', ['id' => $order->user->userable->id]) ?>">
                                    <span><?= $order->user->name ?></span>
                                </a>
                                <?php else: ?>
                                <span> - </span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <td><?= e($order->orderer_phone ?? '-') ?></td>
                            <td><?= e($order->orderer_email ?? '-') ?></td>
                            <td><?= Money::fromFloat($order->total_amount ?? 0) ?></td>
                            <td><?= __('system.order.status.' . $order->order_status) ?></td>
                            <td><?= date('Y-m-d', strtotime($order->created_at)) ?></td>
                            <td class="no-table-action">
                                <div class="no-page-index-table__action">
                                    <a href="<?= route('admin.orders.show', ['id' => $order->id]) ?>" class="no-btn-action" data-tooltip>
                                        <div class="no-btn-action-ripple">
                                            <i class="fa-light fa-eye"></i>
                                            <span data-tooltip-text><span>보기</span><span data-tooltip-arrow></span></span>
                                        </div>
                                    </a>
                                    <a href="<?= route('admin.orders.edit', ['id' => $order->id]) ?>" class="no-btn-action" data-tooltip>
                                        <div class="no-btn-action-ripple">
                                            <i class="fa-light fa-pen-to-square"></i>
                                            <span data-tooltip-text><span>수정</span><span data-tooltip-arrow></span></span>
                                        </div>
                                    </a>
                                    <a href="<?= route('admin.orders.destroy', ['id' => $order->id]) ?>" class="no-btn-action" data-tooltip data-method="delete" data-confirm="정말 삭제하시겠습니까?">
                                        <div class="no-btn-action-ripple">
                                            <i class="fa-light fa-trash-can"></i>
                                            <span data-tooltip-text><span>삭제</span><span data-tooltip-arrow></span></span>
                                        </div>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?= include_view('admin.components.pagination', ['paginator' => $orders]) ?>
        </div>
    </form>
</div>
<?php end_section() ?>
