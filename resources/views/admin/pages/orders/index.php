<?php

use Framework\Support\ValueObjects\Money;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'orders') ?>
<?php section('action', 'index') ?>
<?php section('title', 'Order List') ?>

<?php section('content') ?>
<div class="no-page-container">
    <form method="get">
        <div class="no-page-row">
            <div class="no-page-head">
                <h1 class="no-heading-sm">Order List</h1>
                <p class="no-text-secondary">
                    You can check orders from dealers or customers.
                </p>
            </div>

            <div class="no-page-index-filter">
                <div class="no-page-index-filter__form">
                    <div class="no-form-search --sm">
                        <label for="query" class="no-form-search-inner">
                            <fieldset class="no-form-search-label --blind">
                                <legend class="no-form-search-text">Search</legend>
                            </fieldset>
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input type="search" name="query" id="query" class="no-form-search-input" placeholder="Search by name, email, or contact">
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
                            <th rowspan="3">체크</th>
                            <th colspan="12">오더 기본 정보</th>
                            <th colspan="2">헤드라이트 정보</th>
                            <th colspan="18">루페 정보</th>
                            <th rowspan="3">메모</th>
                            <th rowspan="3">작업</th>
                        </tr>

                        <!-- 영문 헤더 -->
                        <tr>
                            <th>Country</th>
                            <th>Payment Date</th>
                            <th>Delivery</th>
                            <th>Shipping Date</th>
                            <th>Distributor</th>
                            <th>CI Number (LOT)</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Engraving</th>
                            <th>Category</th>
                            <th>Serial Number</th>
                            <th>Model Number</th>

                            <th>Type</th>
                            <th>Color for Wireless</th>

                            <th colspan="4">Right (OD)</th>
                            <th colspan="4">Left (OS)</th>
                            
                            <th>ADD Option</th>
                            <th>Quantity of prescription lens</th>
                            <th>Spectacle</th>
                            <th>Flip-up Color</th>
                            <th>WD</th>
                            <th colspan="4">Far PD</th>
                            <th>VD</th>
                        </tr>

                        <!-- 한글 헤더 -->
                        <tr>
                            <th>국가</th>
                            <th>발주일</th>
                            <th>납기일</th>
                            <th>출하일</th>
                            <th>대리점</th>
                            <th>오더번호 (LOT)</th>
                            <th>이름</th>
                            <th>나이</th>
                            <th>각인</th>
                            <th>구분</th>
                            <th>시리얼 번호</th>
                            <th>모델</th>

                            <!-- headlight -->
                            <th>무선/유선</th>
                            <th>무선 컬러</th>

                            <!-- loupe -->
                            <!-- 우안 -->
                            <th>S</th>
                            <th>C</th>
                            <th>A</th>
                            <th>ADD</th>

                            <th>ADD 옵션</th>

                            <!-- 좌안 -->
                            <th>S</th>
                            <th>C</th>
                            <th>A</th>
                            <th>ADD</th>

                            <th>처방렌즈</th>
                            <th>안경테</th>
                            <th>Flip-up 색상</th>
                            <th>WD</th>

                            <!-- Far PD -->
                            <th>원거리 PD R</th>
                            <th>원거리 PD L</th>
                            <th>원거리 PD 편차</th>
                            <th>원거리 PD 합계</th>
                            <th>VD</th>
                        </tr>

                    </thead>

                    <tbody>
                        <?php foreach ($orders->items() as $order): ?>
                            <?php foreach ($order->items as $item): ?>
                                <?php
                                    $product = $item->product;
                                    $category = $product->template->category ?? null;
                                    $model = $product->template->model; 
                                    $isHeadlight = ($product->type === 'headlight');
                                    $isLoupe = ($product->type === 'loupe');
                                    $headlight = $isHeadlight ? $product->headlight : null;
                                    $loupe = $isLoupe ? $product->loupe : null;

                                    $lensCount = 0; 

                                    if ($loupe) {
                                        // sets
                                    }

                                    $country = $order->user->isDealer()
                                        ? $order->user->dealer->country
                                        : $order->customer->country;
                                    $country = __('system.countries.'.$country);
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

                                    <td><?= e($country) ?></td>
                                    <td><?= e($order->payment_date ?? '-') ?></td>
                                    <td><?= e($order->delivery_date ?? '-') ?></td>
                                    <td><?= e($order->shipping_date ?? '-') ?></td>
                                    <?php if (!user()->isDealer()) : ?>
                                    <td>
                                        <?php if ($order->user && $order->user->isDealer()) : ?>
                                        <a href="<?= route('admin.dealers.edit', ['id' => $order->user->id]) ?>">
                                            <?= e($order->user->name) ?>
                                        </a>
                                        <?php else: ?>
                                        <span>-</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                    <td><?= e($order->order_no ?? '-') ?></td>
                                    <td><?= e($order->orderer_name ?? '-') ?></td>
                                    <td><?= $order->customer->age ?></td> <!-- Age -->
                                    <td><?= $headlight ? e($headlight->engraving_text ?? '-') : ($loupe ? e($loupe->engraving_text ?? '-') : '-') ?></td>
                                    <td><?= e($category ? $category->label : '-') ?></td>
                                    <td><?= $product ? e($product->serial_number ?? '-') : '-' ?></td>
                                    <td><?= $model ?></td>

                                    <!-- <td><?= Money::fromFloat($order->total_amount ?? 0) ?></td> -->
                                    <td><?= $headlight ? e($headlight->wireless_color ? '무선' : '유선') : '유선' ?></td>
                                    <td><?= $headlight ? e($headlight->wireless_color ?? '-') : '-' ?></td>

                                    <!-- Right (OD) -->
                                    <td><?= $loupe ? e($loupe->od_sph ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->od_cyl ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->od_axis ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->od_add ?? '-') : '-' ?></td>

                                    <!-- Left (OS) -->
                                    <td><?= $loupe ? e($loupe->os_sph ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->os_cyl ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->os_axis ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->os_add ?? '-') : '-' ?></td>

                                    <td><?= $loupe ? e($loupe->add_option ?? '-') : '-' ?></td>

                                    <td>-</td> <!-- Qty of prescription lens -->
                                    <td><?= $loupe ? e($loupe->frame_type ?? '-') : '-' ?></td>
                                    <td>-</td> <!-- Flip-up Color -->
                                    <td><?= $loupe ? e($loupe->working_distance ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->pd_right ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->pd_left ?? '-') : '-' ?></td>
                                    <td>-</td> <!-- Deviation -->
                                    <td><?= $loupe ? e($loupe->pd_total ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->vertex_distance ?? '-') : '-' ?></td>

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
