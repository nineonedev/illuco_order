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
                            <th rowspan="2" class="no-table-check">
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
                            <th colspan="10">오더 기본 정보</th>
                            <th colspan="3">헤드라이트 정보</th>
                            <th colspan="10">루페 정보</th>
                            <th colspan="2">기타</th>
                        </tr>
                        <tr>
                            <th>상태</th>
                            <th>총 금액</th>
                            <th>주문자</th>
                            <?php if (!user()->isDealer()) : ?>
                            <th>대리점</th>
                            <?php endif; ?>
                            <th>주문일</th>
                            <th>발주일</th>
                            <th>납기일</th>
                            <th>출하일</th>
                            <th>연락처</th>
                            <th>이메일</th>

                            <!-- 헤드라이트 정보 -->
                            <th>무선컬러</th>
                            <th>각인</th>
                            <th>비고</th>

                            <!-- 루페 정보 -->
                            <th>타입</th>
                            <th>WD</th>
                            <th>OD SPH</th>
                            <th>OS SPH</th>
                            <th>OD CYL</th>
                            <th>OS CYL</th>
                            <th>OD AXIS</th>
                            <th>OS AXIS</th>
                            <th>PD R</th>
                            <th>PD L</th>
                            <th>PD 합계</th>
                            <th>VD</th>

                            <!-- 기타 -->
                            <th>메모</th>
                            <th>작업</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders->items() as $order): ?>
                            <?php foreach ($order->items as $item): ?>
                                <?php
                                    $product = $item->product;
                                    $isHeadlight = ($product->type === 'headlight');
                                    $isLoupe = ($product->type === 'loupe');
                                    $headlight = $isHeadlight ? $product->headlight : null;
                                    $loupe = $isLoupe ? $product->loupe : null;
                                ?>
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
                                    <td><?= __('system.order.status.' . $order->order_status) ?></td>
                                    <td><?= Money::fromFloat($order->total_amount ?? 0) ?></td>
                                    <td><?= e($order->orderer_name) ?></td>
                                    <?php if (!user()->isDealer()) : ?>
                                    <td>
                                        <?php if($order->user ? $order->user->isDealer() : false) : ?>
                                        <a href="<?= route('admin.dealers.edit', ['id' => $order->user->id]) ?>">
                                            <span><?= $order->user->name ?></span>
                                        </a>
                                        <?php else: ?>
                                        <span> - </span>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                    <td><?= e($order->created_at) ?></td>
                                    <td><?= e($order->payment_date ?? '-') ?></td>
                                    <td><?= e($order->delivery_date ?? '-') ?></td>
                                    <td><?= e($order->shipping_date ?? '-') ?></td>
                                    <td><?= e($order->orderer_phone ?? '-') ?></td>
                                    <td><?= e($order->orderer_email ?? '-') ?></td>

                                    <!-- Headlight 정보 -->
                                    <td><?= $headlight ? e($headlight->wireless_color ?? '-') : '-' ?></td>
                                    <td><?= $headlight ? e($headlight->engraving_text ?? '-') : '-' ?></td>
                                    <td><?= $headlight ? e($product->description ?? '-') : '-' ?></td>

                                    <!-- Loupe 정보 -->
                                    <td><?= $loupe ? e($loupe->type ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->working_distance ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->od_sph ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->os_sph ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->od_cyl ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->os_cyl ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->od_axis ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->os_axis ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->pd_right ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->pd_left ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->pd_total ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->vertex_distance ?? '-') : '-' ?></td>

                                    <!-- 기타 -->
                                    <td><?= e($order->memo ?? '-') ?></td>
                                    <td class="no-table-action">
                                        <div class="no-page-index-table__action">
                                            <?php if (user()->isDealer()) : ?>
                                            <a href="<?= route('admin.orders.show', ['orderNo' => $order->order_no]) ?>" class="no-btn-action" data-tooltip>
                                                <div class="no-btn-action-ripple">
                                                    <i class="fa-light fa-eye"></i>
                                                    <span data-tooltip-text><span>보기</span><span data-tooltip-arrow></span></span>
                                                </div>
                                            </a>
                                            <?php else: ?>
                                            <a href="<?= route('admin.orders.edit', ['orderNo' => $order->order_no]) ?>" class="no-btn-action" data-tooltip>
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
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>

            <?= include_view('admin.components.pagination', ['paginator' => $orders]) ?>
        </div>
    </form>
</div>
<?php end_section() ?>
