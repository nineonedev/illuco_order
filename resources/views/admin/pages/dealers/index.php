<?php extend('layouts.admin'); ?>
<?php section('controller', 'dealer') ?>
<?php section('action', 'index') ?>
<?php section('title', '대리점 목록') ?>

<?php section('content') ?>
<div class="no-page-container">
    <form method="get">
        <div class="no-page-row">
            <div class="no-page-head">
                <h1 class="no-heading-sm">대리점</h1>
                <p class="no-text-secondary">
                    등록된 대리점을 확인하실 수 있습니다.
                </p> 
            </div>

            <div class="no-page-index-filter">
                <div class="no-page-index-filter__form">

                    <!-- 대리점명 검색 -->
                    <div class="no-form-search">
                        <label for="name" class="no-form-label">대리점명</label>
                        <div class="no-form-search-inner">
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input
                                type="search"
                                name="name"
                                id="name"
                                class="no-form-search-input"
                                placeholder="대리점명 검색"
                                value="<?= e(request()->query('name')) ?>"
                            >
                        </div>
                    </div>

                    <!-- 국가 선택 -->
                    <?php
                        $countryProps = [
                            'spacing' => false,
                            'label' => '국가',
                            'name' => 'country',
                            'value' => request()->query('country'),
                            'options' => array_map(
                                fn($code, $label) => ['value' => $code, 'label' => $label],
                                array_keys($countries),
                                array_values($countries)
                            )
                        ];
                    ?>
                    <div
                        class="no-form-field"
                        data-view-type="select"
                        data-view-props='<?= e(json_encode($countryProps)) ?>'
                    ></div>

                    <!-- 코드 검색 -->
                    <div class="no-form-search">
                        <label for="code" class="no-form-label">코드</label>
                        <div class="no-form-search-inner">
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input
                                type="search"
                                name="code"
                                id="code"
                                class="no-form-search-input"
                                placeholder="코드 검색"
                                value="<?= e(request()->query('code')) ?>"
                            >
                        </div>
                    </div>

                    <!-- 연락처 검색 -->
                    <div class="no-form-search">
                        <label for="phone" class="no-form-label">연락처</label>
                        <div class="no-form-search-inner">
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input
                                type="search"
                                name="phone"
                                id="phone"
                                class="no-form-search-input"
                                placeholder="연락처 검색"
                                value="<?= e(request()->query('phone')) ?>"
                            >
                        </div>
                    </div>

                    <!-- 이메일 검색 -->
                    <div class="no-form-search">
                        <label for="email" class="no-form-label">이메일</label>
                        <div class="no-form-search-inner">
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input
                                type="search"
                                name="email"
                                id="email"
                                class="no-form-search-input"
                                placeholder="이메일 검색"
                                value="<?= e(request()->query('email')) ?>"
                            >
                        </div>
                    </div>

                    <!-- 정렬 선택 -->
                    <?php
                        $sortProps = [
                            'spacing' => false,
                            'label' => '정렬',
                            'name' => 'sort',
                            'value' => request()->query('sort'),
                            'options' => [
                                ['label' => '전체', 'value' => ''],
                                ['label' => '등록일 ↑', 'value' => 'created_at_asc'],
                                ['label' => '등록일 ↓', 'value' => 'created_at_desc'],
                                ['label' => '대리점명 ↑', 'value' => 'name_asc'],
                                ['label' => '대리점명 ↓', 'value' => 'name_desc'],
                                ['label' => '코드 ↑', 'value' => 'code_asc'],
                                ['label' => '코드 ↓', 'value' => 'code_desc'],
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
                    <a href="<?= route('admin.dealers.create') ?>" class="no-btn-primary --sm">
                        <span>등록</span>
                    </a>
                </div>
            </div>

            <div class="no-page-index-table-outer">
                <table class="no-page-index-table">
                    <thead>
                        <tr>
                            <th>대리점명</th>
                            <th>국가</th>
                            <th>코드</th>
                            <th>연락처</th>
                            <th>이메일</th>
                            <th>등록일</th>
                            <th>작업</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dealers->items() as $dealer): ?>
                        <tr class="no-table-hover">
                            <td><?= e($dealer->name ?? '-') ?></td>
                            <td><?= lang('system.countries.' . e($dealer->dealer->country)) ?></td>
                            <td><?= e($dealer->dealer->code ?? '-') ?></td>
                            <td><?= e($dealer->phone ?? '-') ?></td>
                            <td><?= e($dealer->email ?? '-') ?></td>
                            <td><?= date('Y-m-d', strtotime($dealer->created_at ?? 'now')) ?></td>
                            <td class="no-table-action">
                                <div class="no-page-index-table__action">
                                    <a href="<?= route('admin.dealers.edit', ['id' => $dealer->id]) ?>" class="no-btn-action" data-tooltip>
                                        <div class="no-btn-action-ripple">
                                            <i class="fa-light fa-pen-to-square"></i>
                                            <span data-tooltip-text><span>수정</span><span data-tooltip-arrow></span></span>
                                        </div>
                                    </a>
                                    <a href="<?= route('admin.dealers.destroy', ['id' => $dealer->id]) ?>" class="no-btn-action" data-tooltip data-method="delete" data-confirm="정말 삭제하시겠습니까?">
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

            <?= include_view('admin.components.pagination', ['paginator' => $dealers]) ?>
        </div>
    </form>
</div>
<?php end_section() ?>
