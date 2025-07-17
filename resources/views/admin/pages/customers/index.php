<?php extend('layouts.admin'); ?>
<?php section('controller', 'customer') ?>
<?php section('action', 'index') ?>
<?php section('title', '고객 목록') ?>

<?php section('content') ?>
<div class="no-page-container">
    <form method="get">
        <div class="no-page-row">
            <div class="no-page-head">
                <h1 class="no-heading-sm">고객</h1>
                <p class="no-text-secondary">
                    등록된 고객을 확인하실 수 있습니다.
                </p> 
            </div>

            <div class="no-page-index-filter">
                <div class="no-page-index-filter__form">
                    
                    <!-- 이름 검색 -->
                    <div class="no-form-search">
                        <label for="name" class="no-form-label">이름</label>
                        <div class="no-form-search-inner">
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input
                                type="search"
                                name="name"
                                id="name"
                                class="no-form-search-input"
                                placeholder="이름 검색"
                                value="<?= e(request()->query('name')) ?>"
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
                            ),
                        ];
                    ?>
                    <div
                        class="no-form-field"
                        data-view-type="country-select"
                        data-view-props='<?= e(json_encode($countryProps)) ?>'
                    ></div>

                    <!-- 전화번호 검색 -->
                    <div class="no-form-search">
                        <label for="phone" class="no-form-label">전화번호</label>
                        <div class="no-form-search-inner">
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input
                                type="search"
                                name="phone"
                                id="phone"
                                class="no-form-search-input"
                                placeholder="전화번호 검색"
                                value="<?= e(request()->query('phone')) ?>"
                            >
                        </div>
                    </div>

                    <!-- 대리점 선택 -->
                    <?php if (!user()->isDealer()) : ?>
                        <?php
                            $dealerProps = [
                                'spacing' => false,
                                'label' => '대리점',
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
                                ['label' => '이름 ↑', 'value' => 'name_asc'],
                                ['label' => '이름 ↓', 'value' => 'name_desc'],
                                ['label' => '이메일 ↑', 'value' => 'email_asc'],
                                ['label' => '이메일 ↓', 'value' => 'email_desc'],
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
                    <?php if (can('customer.delete')): ?>
                    <button id="select-delete-btn" class="no-btn-error --sm" disabled>
                        <span>선택삭제</span>
                    </button>
                    <?php endif; ?>
                    <a href="<?= route('admin.customers.create') ?>" class="no-btn-primary --sm">
                        <span>등록</span>
                    </a>
                </div>
            </div>

            <div class="no-page-index-table-outer">
                <table class="no-page-index-table">
                    <thead>
                        <tr>
                            <?php if (can('customer.delete')): ?>
                            <th class="sticky --check">
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
                            <?php endif; ?>
                            <th>이름</th>
                            <?php if (!user()->isDealer()) :?>
                            <th>대리점</th>
                            <?php endif; ?>
                            <th>국가</th>
                            <th>이메일</th>
                            <th>전화번호</th>
                            <th>등록일</th>
                            <th>작업</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers->items() as $customer): ?>
                        <tr class="no-table-hover">
                            <?php if (can('customer.delete')): ?>
                            <td class="no-table-check">
                                <div class="no-form-checkbox --xs">
                                    <label for="customer<?= $customer->id ?>" class="no-form-checkbox-pointer">
                                        <input type="checkbox" name="checked_ids[]" id="customer<?= $customer->id ?>" value="<?= $customer->id ?>" class="no-form-checkbox-input">
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
                            <td><?= e($customer->name) ?></td>
                            <?php if (!user()->isDealer()) :?>
                            <td>
                                <?php if ($customer->dealer) : ?>
                                <a href="<?= route('admin.dealers.edit', ['id' => $customer->dealer->id]) ?>" class="no-direct-link">
                                    <?= $customer->dealer->user->name ?>
                                </a>
                                <?php else : ?>
                                <span> - </span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <td><?= lang('system.countries.'.e($customer->country)) ?></td>
                            <td><?= e($customer->email) ?></td>
                            <td><?= e($customer->phone) ?></td>
                            <td><?= date('Y-m-d', strtotime($customer->created_at ?? 'now')) ?></td>
                            <td class="no-table-action">
                                <div class="no-page-index-table__action">
                                    <a href="<?= route('admin.customers.edit', ['id' => $customer->id]) ?>" class="no-btn-action" data-tooltip>
                                        <div class="no-btn-action-ripple">
                                            <i class="fa-light fa-pen-to-square"></i>
                                            <span data-tooltip-text><span>수정</span><span data-tooltip-arrow></span></span>
                                        </div>
                                    </a>
                                    <a href="<?= route('admin.customers.destroy', ['id' => $customer->id]) ?>" class="no-btn-action" data-item-action="delete" data-tooltip data-method="delete" data-confirm="정말 삭제하시겠습니까?">
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

            <?= include_view('admin.components.pagination', ['paginator' => $customers]) ?>
        </div>
    </form>
</div>
<?php end_section() ?>
