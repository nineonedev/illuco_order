<?php

use App\Domains\Product\Entities\Loupe;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'order') ?>
<?php section('action', 'index') ?>
<?php section('title', 'Order List') ?>

<?php section('content') ?>
<div class="no-page-container">
    <form method="get">
        <div class="no-page-row">
            <div class="no-page-head">
                <h1 class="no-heading-sm">주문 목록</h1>
                <p class="no-text-secondary">
                    대리점 또는 고객의 주문을 확인할 수 있습니다.
                </p>
            </div>

            <div class="no-page-index-filter">
                <div class="no-page-index-filter__form">
                    <!-- 🔎 검색어 -->
                    <div class="no-form-search">
                        <label for="query" class="no-form-label">검색</label>
                        <div class="no-form-search-inner">
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input
                                type="search"
                                name="query"
                                id="query"
                                value="<?= e($query['query'] ?? '') ?>"
                                class="no-form-search-input"
                                placeholder="이름, 이메일, 전화번호, 고객명 등">
                        </div>
                    </div>
                    
                    <!-- ✅ 상태 -->
                    <?php
                        $props = [
                            'spacing' => false,
                            'label' => '상태',
                            'name' => 'status',
                            'value' => request()->query('status'),
                            'options' => array_merge([['label' => '전체', 'value' => '']], array_map(
                            fn($s) => [
                                'label' => __('system.order.status.' . $s),
                                'value' => $s,
                            ], $statuses)),
                        ];
                    ?>
                    <div 
                        class="no-form-field"
                        data-view-type="select"
                        data-view-props='<?= e(json_encode($props)) ?>'
                    ></div>

                    <!-- ✅ 카테고리 -->
                    <?php
                        $props = [
                            'spacing' => false,
                            'label' => '카테고리',
                            'name' => 'category_id',
                            'value' => request()->query('category_id'),
                            'options' => array_merge([['label' => '전체', 'value' => '']], array_map(
                                fn($c) => [
                                    'label' => $c->label, 
                                    'value' => $c->id,
                                ],
                                $categories
                            )),
                        ];
                    ?>
                    <div 
                        class="no-form-field"
                        data-view-type="select"
                        data-view-props='<?= e(json_encode($props)) ?>'
                    ></div>

                    <!-- ✅ 국가 -->
                    <?php
                        $options = [['label' => '전체', 'value' => '']];
                        foreach ($countries as $key => $value) {
                            $options[] = [
                                'label' => $value,
                                'value' => $key,
                            ];
                        }
                        
                        $props = [
                            'spacing' => false,
                            'label' => '국가',
                            'name' => 'country',
                            'value' => request()->query('country'),
                            'options' => $options,
                        ];
                    ?>
                    <div 
                        class="no-form-field"
                        data-view-type="select"
                        data-view-props='<?= e(json_encode($props)) ?>'
                    ></div>

                    <!-- ✅ 주문자 -->
                    <div class="no-form-search">
                        <label for="orderer_query" class="no-form-label">주문자</label>
                        <div class="no-form-search-inner">
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input
                                type="search"
                                name="orderer_query"
                                id="orderer_query"
                                value="<?= e($query['orderer_query'] ?? '') ?>"
                                class="no-form-search-input"
                                placeholder="주문자 이름, 나이, 연락처, 주소 검색">
                        </div>
                    </div>

                    <!-- ✅ 대리점 -->
                    <?php
                        $props = [
                            'spacing' => false,
                            'label' => '대리점',
                            'name' => 'dealer_id',
                            'value' => request()->query('dealer_id'),
                            'options' => array_merge([['label' => '전체', 'value' => '']], array_map(
                            fn($dealer) => [
                                'label' => $dealer->name,
                                'value' => $dealer->dealer->id,
                            ], $dealers)),
                        ];
                    ?>
                    <div 
                        class="no-form-field"
                        data-view-type="select"
                        data-view-props='<?= e(json_encode($props)) ?>'
                    ></div>

                    <!-- ✅ 정렬 -->
                    <?php
                        $props = [
                            'spacing' => false,
                            'label' => '정렬',
                            'name' => 'sort',
                            'value' => request()->query('sort'),
                            'options' => [
                                ['label' => '전체', 'value' => ''],
                                ['label' => '최신순', 'value' => 'created_at_desc'],
                                ['label' => '오래된순', 'value' => 'created_at_asc'],
                                ['label' => '이름 오름차순', 'value' => 'name_asc'],
                                ['label' => '이름 내림차순', 'value' => 'name_desc'],
                            ],
                        ];
                    ?>
                    <div
                        class="no-form-field"
                        data-view-type="select"
                        data-view-props='<?= e(json_encode($props)) ?>'
                    ></div>

                    <div class="no-page-index-link">
                        <button type="submit" class="no-btn-primary --sm">검색</button>
                    </div>
                </div>

                <div class="no-page-index-link">
                    <a href="<?= route('admin.orders.export') ?>" class="no-btn-success --sm">
                        <span>Export</span>
                    </a>
                </div>
            </div>

            <div class="no-page-index-table-outer">
                <table class="no-page-index-table">
                    <thead class="center">
                        <tr>
                            <th class="sticky --check" rowspan="3">
                                <div class="no-form-checkbox --xs">
                                    <label for="chk-all" class="no-form-checkbox-pointer">
                                        <input type="checkbox" name="chk-all" id="chk-all" value="1" class="no-form-checkbox-input">
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
                            <th colspan="14" class="order">오더 기본 정보</th>
                            <th colspan="1" class="headlight">헤드라이트 정보</th>
                            <th colspan="18" class="loupe">루페 정보</th>
                            <th rowspan="3" class="memo">메모</th>
                            <th rowspan="3">작업</th>
                        </tr>

                        <!-- 영문 헤더 -->
                        <tr>
                            <th class="sticky --order-no">Order Number (LOT)</th>
                            <th class="sticky --name">Name</th>
                            <th>Status</th>
                            <th>Age</th>

                            <th>Country</th>
                            <th>Payment Date</th>
                            <th>Delivery</th>
                            <th>Shipping Date</th>
                            <th>Distributor</th>
                            <th>Engraving</th>
                            <th>Category</th>
                            <th>Serial Number</th>
                            <th>Model Number</th>
                            <th>Type</th>

                            <th>Color for Wireless</th>

                            <th colspan="4">Right (OD)</th>
                            <th colspan="4">Left (OS)</th>
                            
                            <th>ADD Option</th>
                            <th>Quantity of<br>prescription lens</th>
                            <th>Spectacle</th>
                            <th>Flip-up Color</th>
                            <th>WD</th>
                            <th colspan="4">Far PD</th>
                            <th>VD</th>
                        </tr>

                        <!-- 한글 헤더 -->
                        <tr>
                            <th class="sticky --order-no">오더번호 (LOT)</th>
                            <th class="sticky --name">이름</th>
                            <th>상태</th>
                            <th>나이</th>

                            <th>국가</th>
                            <th>발주일</th>
                            <th>납기일</th>
                            <th>출하일</th>
                            <th>대리점</th>
                            <th>각인</th>
                            <th>구분</th>
                            <th>시리얼 번호</th>
                            <th>모델</th>
                            <th>형태</th>

                            <!-- headlight -->
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
                            <th>Right</th>
                            <th>Left</th>
                            <th>편차<br>(Deviation)</th>
                            <th>합계<br>(Total)</th>
                            <th>VD</th>
                        </tr>

                    </thead>

                    <tbody>
                        <?php foreach ($orders->items() as $idx => $order): ?>
                            <?php
                                $rowspan = count($order->set_group_items);
                                $first = true;
                                
                                $bgClass = $idx % 2 !== 0 ? 'bg' : '';

                                $country = $order->user->isDealer()
                                    ? $order->user->dealer->country
                                    : $order->customer->country;
                                $country = __('system.countries.'.$country);
                            ?>
                            <?php foreach ($order->set_group_items as $item): ?>
                                <?php
                                    $product = $item->product;
                                    $category = $product->template->category ?? null;
                                    $model = $product->template->model; 
                                    $isHeadlight = ($product->type === 'headlight');
                                    $isLoupe = ($product->type === 'loupe');
                                    $headlight = $isHeadlight ? $product->headlight : null;
                                    $loupe = $isLoupe ? $product->loupe : null;

                                    $type = '-'; 
                                    if ($headlight) {
                                        $type = $headlight->type; 
                                    } else if ($loupe) {
                                        $type = $loupe->type; 
                                    } else {
                                        $type = '-';
                                    }

                                    $pd_right = $loupe ? floatval($loupe->pd_right ?? 0) : 0;
                                    $pd_left = $loupe ? floatval($loupe->pd_left ?? 0) : 0;
                                    $deviation = ($loupe && ($loupe->pd_right !== null && $loupe->pd_left !== null))
                                        ? abs($pd_right - $pd_left)
                                        : '-';

                                ?>
                                <tr class="<?=$bgClass?>">
                                    <?php if ($first): ?>
                                        <!-- ✅ 체크박스 -->
                                        <td class="no-table-check sticky --check" rowspan="<?= $rowspan ?>">
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

                                        <!-- ✅ 공통 컬럼들만 rowspan -->
                                        <td class="sticky --order-no" rowspan="<?= $rowspan ?>"><?= e($order->order_no ?? '-') ?></td>
                                        <td class="sticky --name" rowspan="<?= $rowspan ?>"><?= e($order->orderer_name ?? '-') ?></td>
                                        <td class="sticky --name" rowspan="<?= $rowspan ?>">
                                            <span class="order-status --<?=$order->order_status?>">
                                                <?= e(__('system.order.status.'.$order->order_status) ?? '-') ?>
                                            </span>
                                        </td>
                                        <td rowspan="<?= $rowspan ?>"><?= e($order->customer->age) ?></td>
                                        <td rowspan="<?= $rowspan ?>"><?= e($country) ?></td>
                                        <td rowspan="<?= $rowspan ?>"><?= e($order->payment_date ?? '-') ?></td>
                                        <td rowspan="<?= $rowspan ?>"><?= e($order->delivery_date ?? '-') ?></td>
                                        <td rowspan="<?= $rowspan ?>"><?= e($order->shipping_date ?? '-') ?></td>
                                        
                                        <?php if (!user()->isDealer()) : ?>
                                            <td rowspan="<?= $rowspan ?>">
                                                <?php if ($order->user && $order->user->isDealer()) : ?>
                                                    <a href="<?= route('admin.dealers.edit', ['id' => $order->user->id]) ?>">
                                                        <?= e($order->user->name) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span>-</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>

                                    <?php endif; ?>

                                    <td><?= $headlight ? e($headlight->engraving_text ?? '-') : ($loupe ? e($loupe->engraving_text ?? '-') : '-') ?></td>
                                    <td><?= e($category ? $category->label : '-') ?></td>

                                    <!-- ✅ 아이템별 td는 매번 출력 -->
                                    <td><?= $product ? e($product->serial_number ?? '-') : '-' ?></td>
                                    <td><?= $model ?></td>
                                    <td><?= $type ?></td>
                                    <td><?= $headlight ? e($headlight->wireless_color ?? '-') : '-' ?></td>

                                    <td><?= $loupe ? e($loupe->od_sph ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->od_cyl ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->od_axis ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->od_add ?? '-') : '-' ?></td>

                                    <td><?= $loupe ? e($loupe->os_sph ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->os_cyl ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->os_axis ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->os_add ?? '-') : '-' ?></td>

                                    <td><?= $loupe ? e(Loupe::LABELS["add_option_".$loupe->add_option] ?? '-') : '-' ?></td>
                                    
                                    <td><?= $item->sets ? count($item->sets) : 0 ?></td>
                                    <td><?= $loupe ? e($loupe->frame_type ?? '-') : '-' ?></td>
                                    <td>-</td>
                                    <td><?= $loupe ? e($loupe->working_distance ?? '-') : '-' ?></td>
                                    <td><?= $pd_right ?></td>
                                    <td><?= $pd_left ?></td>
                                    <td><?= $deviation ?></td>
                                    <td><?= $loupe ? e($loupe->pd_total ?? '-') : '-' ?></td>
                                    <td><?= $loupe ? e($loupe->vertex_distance ?? '-') : '-' ?></td>

                                    <?php if ($first): ?>
                                        <td rowspan="<?= $rowspan ?>" class="memo"><?= e($order->memo ?? '-') ?></td>
                                        <td class="no-table-action" rowspan="<?= $rowspan ?>">
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
                                    <?php endif; ?>
                                </tr>
                                <?php $first = false; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

            <?php if (count($orders->items()) === 0): ?>
            <div class="no-form-empty-fallback">
                <p>조건에 해당하는 주문이 없습니다. 다른 조건으로 검색해보세요.</p>
            </div>
            <?php endif; ?>

            <?= include_view('admin.components.pagination', ['paginator' => $orders]) ?>
        </div>
    </form>
</div>
<?php end_section() ?>
