<?php extend('layouts.admin'); ?>

<?php section('controller', 'admin') ?>
<?php section('action', 'dashboard') ?>
<?php section('title', 'Dashboard') ?>

<?php section('content') ?>

<div class="dashboard-container">
    <h1 class="no-heading-sm">일루코 대시보드</h1>

    <!-- 매출 현황 -->
    <div class="dashboard-chart">
        <canvas id="sales-chart"></canvas>
    </div>

    <!-- 최근 오더 목록 -->
    <div class="dashboard-section">
        <h2 class="dashboard-section-title">최근 오더 목록</h2>
        <table class="no-table">
            <thead>
                <tr>
                    <th>주문번호</th>
                    <th>주문자</th>
                    <th>대리점</th>
                    <th>주문일</th>
                    <th>주문금액</th>
                </tr>
            </thead>
            <tbody id="recent-orders-body"></tbody>
        </table>
    </div>

    <!-- 공지사항 -->
    <div class="dashboard-section">
        <h2 class="dashboard-section-title">공지사항</h2>
        <table class="no-table">
            <thead>
                <tr>
                    <th>제목</th>
                    <th>작성자</th>
                    <th>등록일</th>
                </tr>
            </thead>
            <tbody id="notices-body"></tbody>
        </table>
    </div>
</div>

<?php end_section() ?>
