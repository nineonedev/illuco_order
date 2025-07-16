<?php extend('layouts.admin'); ?>

<?php section('controller', 'admin') ?>
<?php section('action', 'dashboard') ?>
<?php section('title', 'Dashboard') ?>

<?php section('content') ?>

<div class="dashboard-container">
    <h1 class="no-heading-sm">대시보드</h1>

    <div class="dashboard-section">
        <form id="filter-form" method="get" class="no-form-inline">
            <div class="no-page-row">
                <div class="no-page-index-filter">
                    <div class="no-page-index-filter__form dashboard-section-form">
                        <?php
                            $options = [['label' => '전체', 'value' => '']];

                            // 여기가 포인트 — 연도 범위 구하기
                            foreach ($years as $y) {
                                $options[] = [
                                    'label' => $y . '년',
                                    'value' => $y,
                                ];
                            }

                            $props = [
                                'spacing' => false,
                                'label' => '연도 선택',
                                'name' => 'year',
                                'value' => $year,
                                'options' => $options,
                            ];
                        ?>
                        <div 
                            class="no-form-field"
                            data-view-type="select"
                            data-view-props='<?= e(json_encode($props)) ?>'
                        ></div>


                        <div class="no-page-index-link">
                            <button type="submit" class="no-btn-primary --sm">조회</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- 총 매출 요약 -->
    <div class="dashboard-section">
        <h2 class="dashboard-section-title">요약</h2>
        <div class="dashboard-summary">
            <?php if (!user()->isDealer()): ?>
            <div class="dashboard-summary-card">
                <p class="dashboard-summary-label">총 매출</p>
                <p class="dashboard-summary-value" id="total-sales-amount">-</p>
            </div>
            <div class="dashboard-summary-card">
                <p class="dashboard-summary-label">본사(일루코) 매출</p>
                <p class="dashboard-summary-value" id="illuco-sales-amount">-</p>
            </div>
            <?php endif ?>
            <div class="dashboard-summary-card">
                <p class="dashboard-summary-label">대리점 매출</p>
                <p class="dashboard-summary-value" id="dealer-sales-amount">-</p>
            </div>
        </div>
    </div>

    <?php if (!user()->isDealer()): ?>
    <!-- 전체 매출 차트 -->
    <div class="dashboard-section">
        <h2 class="dashboard-section-title">전체 매출 (월별)</h2>
        <div class="dashboard-chart">
            <canvas id="sales-chart"></canvas>
        </div>
    </div>


    <!-- 본사(일루코) 매출 차트 -->
    <div class="dashboard-section">
        <h2 class="dashboard-section-title">본사(일루코) 매출 (월별)</h2>

        <div class="dashboard-chart">
            <canvas id="illuco-chart"></canvas>
        </div>
    </div>
    <?php endif; ?>

    <!-- 대리점별 매출 차트 -->
    <div class="dashboard-section">
        <h2 class="dashboard-section-title"><?= user()->isDealer() ? '매출' : '대리점별 매출' ?></h2>

        <div class="dashboard-chart">
            <canvas id="dealer-chart"></canvas>
        </div>
    </div>


    <!-- 최근 오더 목록 -->
    <div class="dashboard-section">
        <h2 class="dashboard-section-title">최근 오더 목록</h2>

        <div class="no-page-index-table-outer">
            <table class="no-page-index-table">
                <thead>
                    <tr>
                        <th>주문번호</th>
                        <th>주문자</th>
                        <th>대리점</th>
                        <th>주문일</th>
                        <th>주문금액</th>
                    </tr>
                </thead>
                <tbody id="recent-orders-body">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): 
                            $link = user()->isDealer() 
                                ? route('admin.orders.show', ['orderNo' => $order->order_no])
                                : route('admin.orders.edit', ['orderNo' => $order->order_no]);
                        ?>
                            <tr class="no-table-hover">
                                <td>
                                    <a href="<?= $link ?>" class="--underline"><?= e($order->order_no ?? '-') ?></a>
                                </td>
                                <td><?= e($order->customer->name ?? '-') ?></td>
                                <td><?= e($order->user->isDealer() ? $order->user->name : '-') ?></td>
                                <td><?= e(date('Y-m-d', strtotime($order->created_at ?? ''))) ?></td>
                                <td><?= number_format($order->total_amount ?? 0) ?> USD</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">주문 내역이 없습니다.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>



    <!-- 공지사항 -->
    <div class="dashboard-section">
        <h2 class="dashboard-section-title">공지사항</h2>

        <div class="no-page-index-table-outer">
            <table class="no-page-index-table">
                <thead>
                    <tr>
                        <th>제목</th>
                        <th>작성자</th>
                        <th>등록일</th>
                    </tr>
                </thead>
                <tbody id="notices-body">
                    <?php if (!empty($notices)): ?>
                        <?php foreach ($notices as $notice): 
                            $link = user()->isDealer() 
                                ? route('admin.notices.show', ['id' => $notice->id])
                                : route('admin.notices.edit', ['id' => $notice->id]);    
                        ?>
                            <tr class="no-table-hover">
                                <td>
                                    <a href="<?= $link ?>" class="--underline">
                                        <?= e($notice->title ?? '-') ?>
                                    </a>
                                </td>
                                <td><?= e($notice->user->name ?? '-') ?></td>
                                <td><?= e(date('Y-m-d', strtotime($notice->created_at ?? ''))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">등록된 공지사항이 없습니다.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php end_section() ?>
