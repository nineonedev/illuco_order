<?php extend('layouts.admin'); ?>
<?php section('controller', 'claim') ?>
<?php section('action', 'index') ?>
<?php section('title', '클레임 목록') ?>

<?php section('content') ?>

<div class="no-page-container">
    <form method="get">
    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">클레임</h1>
            <p class="no-text-secondary">
                대리점에 등록된 클레임을 확인하실 수 있습니다.
            </p> 
        </div>
        <!-- Head -->

        <div class="no-page-index-filter">
            <div class="no-page-index-filter__form">

                <!-- 제목 검색 -->
                <div class="no-form-search">
                    <label for="title" class="no-form-label">제목</label>
                    <div class="no-form-search-inner">
                        <div class="no-form-search__icon">
                            <i class="fa-light fa-magnifying-glass"></i>
                        </div>
                        <input
                            type="search"
                            name="title"
                            id="title"
                            class="no-form-search-input"
                            placeholder="제목 검색"
                            value="<?= e(request()->query('title')) ?>"
                        >
                    </div>
                </div>

                <!-- 대리점 선택 -->
                <?php if ($dealers): ?>
                <?php
                $dealerProps = [
                    'spacing' => false,
                    'label' => '대리점 선택',
                    'name' => 'dealer_id',
                    'value' => request()->query('dealer_id'),
                    'options' => array_merge(
                        [['label' => '전체', 'value' => '']],
                        array_map(fn($dealer) => [
                            'label' => $dealer->user->name,
                            'value' => $dealer->id,
                        ], $dealers)
                    ),
                ];
                ?>
                <div
                    class="no-form-field"
                    data-view-type="select"
                    data-view-props='<?= e(json_encode($dealerProps)) ?>'
                ></div>
                <?php endif; ?>

                <!-- 제품 시리얼 검색 -->
                <div class="no-form-search">
                    <label for="product_serial" class="no-form-label">제품 시리얼번호</label>
                    <div class="no-form-search-inner">
                        <div class="no-form-search__icon">
                            <i class="fa-light fa-magnifying-glass"></i>
                        </div>
                        <input
                            type="search"
                            name="product_serial"
                            id="product_serial"
                            class="no-form-search-input"
                            placeholder="제품 시리얼번호 검색"
                            value="<?= e(request()->query('product_serial')) ?>"
                        >
                    </div>
                </div>

                <!-- 상태 선택 -->
                <?php
                use App\Domains\Communication\Enums\ClaimStatus;
                
                $props = [
                    'spacing' => false,
                    'label' => '상태 선택',
                    'name' => 'status',
                    'value' => request()->query('status'),
                    'options' => array_merge(
                        [['label' => '전체', 'value' => '']],
                        array_map(fn($status) => [
                            'label' => ClaimStatus::labels()[$status] ?? $status,
                            'value' => $status,
                        ], ClaimStatus::all())
                    ),
                ];
                ?>
                <div
                    class="no-form-field"
                    data-view-type="select"
                    data-view-props='<?= e(json_encode($props)) ?>'
                ></div>

                <!-- ✅ 기간 - 시작일 -->
                <?php
                    $props = [
                        'topLabel' => true,
                        'spacing' => false,
                        'label' => '시작일',
                        'name' => 'start',
                        'value' => request()->query('start') ?? '',
                        'placeholder' => '시작일',
                        'format' => 'YYYY-MM-DD',
                    ];
                ?>
                <div 
                    class="no-form-field"
                    data-view-type="date"
                    data-view-props='<?= e(json_encode($props)) ?>'
                ></div>

                <!-- ✅ 기간 - 종료일 -->
                <?php
                    $props = [
                        'topLabel' => true,
                        'spacing' => false,
                        'label' => '종료일',
                        'name' => 'end',
                        'value' => request()->query('end') ?? '',
                        'placeholder' => '종료일',
                        'format' => 'YYYY-MM-DD',
                    ];
                ?>
                <div 
                    class="no-form-field"
                    data-view-type="date"
                    data-view-props='<?= e(json_encode($props)) ?>'
                ></div>

                <!-- 주문자 검색 -->
                <div class="no-form-search">
                    <label for="orderer_name" class="no-form-label">주문자</label>
                    <div class="no-form-search-inner">
                        <div class="no-form-search__icon">
                            <i class="fa-light fa-magnifying-glass"></i>
                        </div>
                        <input
                            type="search"
                            name="orderer_name"
                            id="orderer_name"
                            class="no-form-search-input"
                            placeholder="주문자 검색"
                            value="<?= e(request()->query('orderer_name')) ?>"
                        >
                    </div>
                </div>

                <!-- 작성자 검색 -->
                <div class="no-form-search">
                    <label for="author_name" class="no-form-label">작성자</label>
                    <div class="no-form-search-inner">
                        <div class="no-form-search__icon">
                            <i class="fa-light fa-magnifying-glass"></i>
                        </div>
                        <input
                            type="search"
                            name="author_name"
                            id="author_name"
                            class="no-form-search-input"
                            placeholder="작성자 검색"
                            value="<?= e(request()->query('author_name')) ?>"
                        >
                    </div>
                </div>

                <!-- 정렬 선택 -->
                <?php
                $sortProps = [
                    'spacing' => false,
                    'label' => '정렬 선택',
                    'name' => 'sort',
                    'value' => request()->query('sort'),
                    'options' => [
                        ['label' => '전체', 'value' => ''],
                        ['label' => '등록일 ↑', 'value' => 'created_at_asc'],
                        ['label' => '등록일 ↓', 'value' => 'created_at_desc'],
                        ['label' => '제목 ↑', 'value' => 'title_asc'],
                        ['label' => '제목 ↓', 'value' => 'title_desc'],
                    ],
                ];
                ?>
                <div
                    class="no-form-field"
                    data-view-type="select"
                    data-view-props='<?= e(json_encode($sortProps)) ?>'
                ></div>

                <div class="no-page-index-link">
                    <button type="submit" class="no-btn-primary --sm">검색</button>
                </div>
            </div>

            <div class="no-page-index-link">
                <a href="<?= route('admin.claims.index') ?>" class="no-btn-success --sm">
                    <span>필터 초기화</span>
                </a>

                <?php if (can('claim.delete')): ?>
                <button id="select-delete-btn" class="no-btn-error --sm" type="button" disabled>
                    <span>선택삭제</span>
                </button>
                <?php endif; ?>

                <?php if (can('claim.create')): ?>
                <a href="<?= route('admin.claims.create') ?>" class="no-btn-primary --sm">
                    <span>Create</span>
                </a>
                <?php endif;?>
            </div>
        </div>
        <!-- Filter -->

        <form method="get" class="no-page-index-table-outer">
            <table class="no-page-index-table">
                <thead>
                    <tr>
                        <?php if (can('claim.delete')): ?>
                        <th class="no-table-check">
                            <div class="no-form-checkbox --xs">
                                <label for="chk-all" class="no-form-checkbox-pointer">
                                    <input type="checkbox" name="chk-all" id="chk-all" class="no-form-checkbox-input">
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
                        <?php endif; ?>
                        <th>대리점</th>
                        <th>제품</th>
                        <th>제목</th>
                        <th>주문자</th>
                        <th>작성자</th>
                        <th>상태</th>
                        <th>등록일</th>
                        <th>작업</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($claims->items() as $claim): ?>
                        <tr class="no-table-hover">
                            <?php if (can('claim.delete')): ?>
                            <td class="no-table-check">
                                <div class="no-form-checkbox --xs">
                                    <label for="claim<?= $claim->id ?>" class="no-form-checkbox-pointer">
                                        <input type="checkbox" name="checked_ids[]" id="claim<?= $claim->id ?>" class="no-form-checkbox-input" value="<?=$claim->id?>">
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
                            <?php endif; ?>
                            <td><?= $claim->dealer ? $claim->dealer->user->name : '-' ?></td>
                            <td>
                                <div><?= e($claim->product_name) ?></div>
                                <small class="no-text-secondary">
                                    모델: <?= e($claim->product_model) ?><br>
                                    코드: <?= e($claim->product_code) ?><br>
                                    시리얼: <?= e($claim->product_serial_number) ?>
                                </small>
                            </td>
                            <td>
                                <a href="<?= route('admin.claims.show', ['id' => $claim->id]) ?>" class="--underline"><?= e($claim->title) ?></a>
                            </td>
                            <td><?= e($claim->customer_name) ?></td>
                            <td><?= e($claim->user->name ?? '관리자') ?></td>
                            <td>
                                <span class="claim-status --<?=$claim->status?>">
                                    <?= ClaimStatus::labels()[$claim->status] ?? $claim->status ?>
                                </span>
                            </td>
                            <td><?= date('Y-m-d', strtotime($claim->created_at ?? 'now')) ?></td>
                            <td class="no-table-action">
                                <div class="no-page-index-table__action">
                                    <a href="<?= route('admin.claims.show', ['id' => $claim->id]) ?>" class="no-btn-action" data-tooltip>
                                        <div class="no-btn-action-ripple">
                                            <i class="fa-light fa-eye"></i>
                                            <span data-tooltip-text><span>보기</span><span data-tooltip-arrow></span></span>
                                        </div>
                                    </a>

                                    <?php if (can('claim.delete')): ?>
                                    <a href="<?= route('admin.claims.destroy', ['id' => $claim->id]) ?>" data-item-action="delete" class="no-btn-action" data-tooltip data-method="delete" data-confirm="정말 삭제하시겠습니까?">
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
                    </tbody>

            </table>
        </form>
        <!-- Table -->

        <?= include_view('admin.components.pagination', ['paginator' => $claims]) ?>
    </div>
    <!-- Row -->
    </form>
</div>

<?php end_section() ?>
