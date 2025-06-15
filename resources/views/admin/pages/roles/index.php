<?php extend('layouts.admin'); ?>

<?php section('title') ?>
권한 관리
<?php end_section() ?>

<?php section('content') ?>
<div class="no-page-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">권한 관리</h1>
            <p class="no-text-secondary">등록된 권한을 확인할 수 있습니다.</p> 
        </div>
        
        <div class="no-page-index-filter">
            <form action="" class="no-page-index-filter__form">
                <div class="no-form-search --sm">
                    <label for="query" class="no-form-search-inner">
                        <fieldset class="no-form-search-label --blind">
                            <legend class="no-form-search-text">검색</legend>
                        </fieldset>
                        <div class="no-form-search__icon">
                            <i class="fa-light fa-magnifying-glass"></i>
                        </div>
                        <input type="search" name="query" id="query" class="no-form-search-input" placeholder="Search">
                    </label>
                </div>
            </form>

            <div class="no-page-index-link">
                <a href="<?= route('admin.roles.create') ?>" class="no-btn-primary --sm">
                    <span>Create</span>
                </a>
            </div>
        </div>

        <form method="get" class="no-page-index-table-outer">
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
                                    <a href="<?= route('admin.roles.edit', ['id' => $role->id])?>" class="no-btn-action" data-tooltip>
                                        <div class="no-btn-action-ripple">
                                            <i class="fa-light fa-pen-to-square"></i>
                                            <span data-tooltip-text>
                                                <span>수정</span>
                                                <span data-tooltip-arrow></span>
                                            </span>
                                        </div>
                                    </a>
                                    <button type="button" class="no-btn-action" data-tooltip>
                                        <div class="no-btn-action-ripple">
                                            <i class="fa-light fa-trash-can"></i>
                                            <span data-tooltip-text>
                                                <span>삭제</span>
                                                <span data-tooltip-arrow></span>
                                            </span>
                                        </div>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>

        <!-- Pagination -->
        <div class="no-pagination">
            <p class="no-pagination__text">Rows per page:</p>
            <div class="no-pagination__input">
                <select name="perPage" onchange="this.form.submit()">
                    <option value="15" <?= $roles->perPage() == 15 ? 'selected' : '' ?>>15</option>
                    <option value="25" <?= $roles->perPage() == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= $roles->perPage() == 50 ? 'selected' : '' ?>>50</option>
                </select>
            </div>
            <div class="no-pagination__text">
                <?= $roles->from() ?>-<?= $roles->to() ?> of <?= $roles->total() ?>
            </div>
            <div class="no-pagination__btn">
                <?php if ($roles->currentPage() > 1): ?>
                    <a href="<?= $roles->previousPageUrl() ?>" class="no-btn-move">
                        <i class="fa-duotone fa-light fa-chevron-left"></i>
                    </a>
                <?php else: ?>
                    <a class="no-btn-move --disabled">
                        <i class="fa-duotone fa-light fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php if ($roles->hasMorePages()): ?>
                    <a href="<?= $roles->nextPageUrl() ?>" class="no-btn-move">
                        <i class="fa-duotone fa-light fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <a class="no-btn-move --disabled">
                        <i class="fa-duotone fa-light fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <!-- Pagination -->

    </div>
</div>
<?php end_section() ?>
