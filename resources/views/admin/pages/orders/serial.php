<?php

use App\Domains\Product\Entities\ProductSerial;
use App\Domains\Product\Entities\Loupe; // ⬅️ 루페 렌더용 추가

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

                    <?php
                    // =====================[ 루페 전용 출력 ]=====================
                    // 제품 타입이 루페고 서브엔티티가 있으면 시력정보/모렌즈/자렌즈 + 옵션 태그 출력
                    $type = $product->type ?? null;
                    if ($type) {
                        $product->load([$type]);
                    }
                    $sub  = $type ? ($product->{$type} ?? null) : null;

                    $fmtSigned = function ($v, int $dec = 2) {
                        if ($v === null || $v === '' || $v === '-') return '-';
                        return sprintf('%+.' . $dec . 'f', (float)$v);
                    };
                    $fmtAxis = function ($v) {
                        if ($v === null || $v === '' || $v === '-') return '-';
                        return (string)intval($v);
                    };

                    if ($type === \App\Domains\Product\Entities\Loupe::alias() && $sub):
                    $a = $sub->getAttributes();
                    $odS = $a['od_sph'] ?? null; $odC = $a['od_cyl'] ?? null; $odA = $a['od_axis'] ?? null; $odAdd = $a['od_add'] ?? null;
                    $osS = $a['os_sph'] ?? null; $osC = $a['os_cyl'] ?? null; $osA = $a['os_axis'] ?? null; $osAdd = $a['os_add'] ?? null;

                    // 모렌즈/자렌즈 계산 (요약)
                    $opt = $a['add_option'] ?? null; // include|ignore|zero_diopter
                    $molOdS = ($opt === 'zero_diopter') ? 0 : $odS;  $molOsS = ($opt === 'zero_diopter') ? 0 : $osS;
                    $jarOdS = ($odS !== null && $odAdd !== null) ? (float)$odS + (float)$odAdd : null;
                    $jarOsS = ($osS !== null && $osAdd !== null) ? (float)$osS + (float)$osAdd : null;
                    ?>
                    <!-- <section class="spec-card" aria-label="루페 시력정보 및 렌즈 계산 요약">
                        <header class="spec-card__header">
                            <h3 class="spec-card__title">루페 시력정보 & 렌즈 계산 요약</h3>
                        </header>

                        <div class="spec-card__body">

                            <div class="spec-card__tables">
                            <div class="spec-table">
                                <div class="spec-table__title">처방전 (Prescription)</div>
                                <table aria-label="처방전 수치">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>SPH</th>
                                    <th>CYL</th>
                                    <th>Axis</th>
                                    <th>ADD</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row">OD</th>
                                    <td class="u-mono u-sign"><?= e($fmtSigned($odS)) ?></td>
                                    <td class="u-mono u-sign"><?= e($fmtSigned($odC)) ?></td>
                                    <td class="u-mono"><?= e($fmtAxis($odA)) ?><span class="u-deg"></span></td>
                                    <td class="u-mono u-sign"><?= e($fmtSigned($odAdd)) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">OS</th>
                                    <td class="u-mono u-sign"><?= e($fmtSigned($osS)) ?></td>
                                    <td class="u-mono u-sign"><?= e($fmtSigned($osC)) ?></td>
                                    <td class="u-mono"><?= e($fmtAxis($osA)) ?><span class="u-deg"></span></td>
                                    <td class="u-mono u-sign"><?= e($fmtSigned($osAdd)) ?></td>
                                </tr>
                                </tbody>
                                </table>
                            </div>

                            <div class="spec-table">
                                <div class="spec-table__title">렌즈 계산 요약</div>
                                <table aria-label="렌즈 계산 요약">
                                <thead>
                                <tr>
                                    <th>렌즈</th>
                                    <th>OD · S</th><th>OD · C</th><th>OD · A</th><th>OD · ADD</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row">처방전</th>
                                    <td class="u-mono u-sign"><?= e($fmtSigned($odS)) ?></td>
                                    <td class="u-mono u-sign"><?= e($fmtSigned($odC)) ?></td>
                                    <td class="u-mono"><?= e($fmtAxis($odA)) ?><span class="u-deg"></span></td>
                                    <td class="u-mono u-sign"><?= e($fmtSigned($odAdd)) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">모렌즈</th>
                                    <td class="u-mono u-sign"><?= e($fmtSigned($molOdS)) ?></td>
                                    <td class="u-mono u-sign"><?= e($fmtSigned($odC)) ?></td>
                                    <td class="u-mono"><?= e($fmtAxis($odA)) ?><span class="u-deg"></span></td>
                                    <td>–</td>
                                </tr>
                                <tr>
                                    <th scope="row">자렌즈</th>
                                    <td class="u-mono u-sign"><?= e($fmtSigned($jarOdS)) ?></td>
                                    <td class="u-mono u-sign"><?= e($fmtSigned($odC)) ?></td>
                                    <td class="u-mono"><?= e($fmtAxis($odA)) ?><span class="u-deg"></span></td>
                                    <td>–</td>
                                </tr>
                                </tbody>
                                </table>
                            </div>
                            </div>

                            <div class="spec-card__row">
                            <div class="spec-tags">
                                <?php
                                $labels = \App\Domains\Product\Entities\Loupe::LABELS;
                                $map = [
                                'type' => '루페 종류',
                                'frame_type' => '프레임 종류',
                                'working_distance' => '작업 거리 (mm)',
                                'pd_right' => '우안 동공거리 (PD Right)',
                                'pd_left'  => '좌안 동공거리 (PD Left)',
                                'pd_total' => '총 동공거리 (PD Total)',
                                'vertex_distance' => '정점거리 (mm)',
                                'add_option' => '추가 옵션',
                                'engraving_text' => '각인',
                                ];

                                $exclude = ['od_sph','os_sph','od_cyl','os_cyl','od_axis','os_axis','od_add','os_add','id'];
                                foreach ($map as $key => $label):
                                if (in_array($key, $exclude, true)) continue;
                                $val = $a[$key] ?? null; if ($val === null || $val === '') continue;

                                // 라벨 매핑(가능하면)
                                if ($key === 'add_option')      { $val = $labels['add_option_'.$val] ?? $val; }
                                if ($key === 'frame_type')      { /* 필요 시 코드→이름 변환 로직 연결 */ }
                                ?>
                                <span class="spec-tags__item">
                                    <span class="spec-tags__label"><?= e($map[$key]) ?></span>
                                    <span class="spec-tags__value"><?= e($val) ?></span>
                                </span>
                                <?php endforeach; ?>
                            </div>
                            </div>

                        </div>
                        </section> -->
                    <?php
                    // ====================[ / 루페 전용 출력 끝 ]==================
                    endif;
                    ?>
                    
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
                    <!--
                    <section>
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
                    </section>
                    -->
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
