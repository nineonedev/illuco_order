<?php extend('layouts.admin'); ?>

<?php section('controller', 'role') ?>
<?php section('action', 'index') ?>
<?php section('title', '권한 관리') ?>

<?php section('content') ?>
<div class="no-page-container">
    <form method="get">
        <div class="no-page-row">
            <div class="no-page-head">
                <h1 class="no-heading-sm">권한 관리</h1>
                <p class="no-text-secondary">등록된 권한을 확인할 수 있습니다.</p> 
            </div>

            <div class="no-page-index-filter">
                <div class="no-page-index-filter__form">

                    <!-- 권한명 검색 -->
                    <div class="no-form-search">
                        <label for="name" class="no-form-label">권한명</label>
                        <div class="no-form-search-inner">
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input
                                type="search"
                                name="name"
                                id="name"
                                class="no-form-search-input"
                                placeholder="권한명 검색"
                                value="<?= e(request()->query('name')) ?>"
                            >
                        </div>
                    </div>

                    <!-- 설명 검색 -->
                    <div class="no-form-search">
                        <label for="description" class="no-form-label">설명</label>
                        <div class="no-form-search-inner">
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input
                                type="search"
                                name="description"
                                id="description"
                                class="no-form-search-input"
                                placeholder="설명 검색"
                                value="<?= e(request()->query('description')) ?>"
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
                                ['label' => '권한명 ↑', 'value' => 'name_asc'],
                                ['label' => '권한명 ↓', 'value' => 'name_desc'],
                                ['label' => '등록일 ↑', 'value' => 'created_at_asc'],
                                ['label' => '등록일 ↓', 'value' => 'created_at_desc'],
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
                    <a href="<?= route('admin.roles.create') ?>" class="no-btn-primary --sm">
                        <span>등록</span>
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
                            <th>번호</th>
                            <th>권한명</th>
                            <th>설명</th>
                            <th>작업</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles->items() as $index => $role): ?>
                            <tr class="no-table-hover">
                                <td class="no-table-check">
                                    <div class="no-form-checkbox --xs">
                                        <label for="role_<?= $role->id ?>" class="no-form-checkbox-pointer">
                                            <input type="checkbox" id="role_<?= $role->id ?>" class="no-form-checkbox-input">
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
                                <td><?= e($role->rowNumber()) ?></td>
                                <td><?= e($role->name) ?></td>
                                <td><?= e($role->description) ?></td>
                                <td class="no-table-action">
                                    <div class="no-page-index-table__action">
                                        <a href="<?= route('admin.roles.edit', ['id' => $role->id]) ?>" class="no-btn-action" data-tooltip>
                                            <div class="no-btn-action-ripple">
                                                <i class="fa-light fa-pen-to-square"></i>
                                                <span data-tooltip-text>
                                                    <span>수정</span>
                                                    <span data-tooltip-arrow></span>
                                                </span>
                                            </div>
                                        </a>
                                        <a href="<?= route('admin.roles.destroy', ['id' => $role->id]) ?>" class="no-btn-action" data-tooltip data-method="delete" data-confirm="정말 삭제하시겠습니까?">
                                            <div class="no-btn-action-ripple">
                                                <i class="fa-light fa-trash-can"></i>
                                                <span data-tooltip-text>
                                                    <span>삭제</span>
                                                    <span data-tooltip-arrow></span>
                                                </span>
                                            </div>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?= include_view('admin.components.pagination', ['paginator' => $roles]) ?>
        </div>
    </form>
</div>
<?php end_section() ?>
