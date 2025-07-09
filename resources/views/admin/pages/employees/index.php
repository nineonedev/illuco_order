<?php extend('layouts.admin'); ?>
<?php section('controller', 'employee') ?>
<?php section('action', 'index') ?>
<?php section('title', '직원 목록') ?>

<?php section('content') ?>
<div class="no-page-container">
    <form method="get">
        <div class="no-page-row">
            <div class="no-page-head">
                <h1 class="no-heading-sm">직원</h1>
                <p class="no-text-secondary">
                    등록된 직원을 확인하실 수 있습니다.
                </p> 
            </div>

            <div class="no-page-index-filter">
                <div class="no-page-index-filter__form">
                    <div class="no-form-search --sm">
                        <label for="q" class="no-form-search-inner">
                            <fieldset class="no-form-search-label --blind">
                                <legend class="no-form-search-text">검색</legend>
                            </fieldset>
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input type="search" name="q" id="q" class="no-form-search-input" placeholder="Search" value="<?=request()->query('q', '')?>">
                        </label>
                    </div>
                </div>

                <div class="no-page-index-link">
                    <a href="<?= route('admin.employees.create') ?>" class="no-btn-primary --sm">
                        <span>등록</span>
                    </a>
                </div>
            </div>

            <div class="no-page-index-table-outer">
                <table class="no-page-index-table">
                    <thead>
                        <tr>
                            <th>이름</th>
                            <th>이메일</th>
                            <th>연락처</th>
                            <th>등록일</th>
                            <th>작업</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees->items() as $employee): ?>
                        <tr class="no-table-hover">
                            <td><?= e($employee->name ?? '-') ?></td>
                            <td><?= e($employee->email ?? '-') ?></td>
                            <td><?= e($employee->phone ?? '-') ?></td>
                            <td><?= date('Y-m-d', strtotime($employee->created_at ?? 'now')) ?></td>
                            <td class="no-table-action">
                                <div class="no-page-index-table__action">
                                    <a href="<?= route('admin.employees.edit', ['id' => $employee->id]) ?>" class="no-btn-action" data-tooltip>
                                        <div class="no-btn-action-ripple">
                                            <i class="fa-light fa-pen-to-square"></i>
                                            <span data-tooltip-text><span>수정</span><span data-tooltip-arrow></span></span>
                                        </div>
                                    </a>
                                    <a href="<?= route('admin.employees.destroy', ['id' => $employee->id]) ?>" class="no-btn-action" data-tooltip data-method="delete" data-confirm="정말 삭제하시겠습니까?">
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

            <?= include_view('admin.components.pagination', ['paginator' => $employees]) ?>
        </div>
    </form>
</div>
<?php end_section() ?>
