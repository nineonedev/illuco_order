<?php ?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'product_template') ?>
<?php section('action', 'index') ?>
<?php section('title', '제품 목록') ?>

<?php section('content') ?>
<div class="no-page-container">
    <form method="get">
        <div class="no-page-row">
            <div class="no-page-head">
                <h1 class="no-heading-sm">제품 관리</h1>
                <p class="no-text-secondary">
                    등록된 제품 목록을 확인하실 수 있습니다.
                </p> 
            </div>

            <div class="no-page-index-filter">
                <div class="no-page-index-filter__form">
                    <div class="no-form-search --sm">
                        <label for="name" class="no-form-search-inner">
                            <fieldset class="no-form-search-label --blind">
                                <legend class="no-form-search-text">검색</legend>
                            </fieldset>
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input type="search" name="name" id="name" class="no-form-search-input" placeholder="Search by name">
                        </label>
                    </div>
                </div>

                <div class="no-page-index-link">
                    <a href="<?= route('admin.product_templates.create') ?>" class="no-btn-primary --sm">
                        <span>Create</span>
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
                                        <input type="checkbox" name="all" id="all" class="no-form-checkbox-input">
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
                            <th>이미지</th>
                            <th>카테고리</th>
                            <th>이름</th>
                            <th>코드</th>
                            <th>모델</th>
                            <th>정렬순서</th>
                            <th>작업</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($templates->items() as $template): ?>
                        <tr class="no-table-hover">
                            <td class="no-table-check">
                                <div class="no-form-checkbox --xs">
                                    <label for="template<?= $template->id ?>" class="no-form-checkbox-pointer">
                                        <input type="checkbox" name="checked_ids[]" id="template<?= $template->id ?>" value="<?= $template->id ?>" class="no-form-checkbox-input">
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
                            <td>
                                <!-- 제품 이름 앞에 이미지 삽입 -->
                                 <?php if ($template->fileattachment && count($template->fileattachment) > 0): ?>
                                    <img src="<?= $template->fileattachment[0]->upload_path ?>" alt="<?= e($template->name) ?>" class="product-image" style="width: 60px; height: auto; margin-right: 10px;">
                                <?php endif; ?>
                                
                            </td>
                            <td><?= e($template->category->label ?? '-') ?></td>
                            <td><?= e($template->name) ?></td>
                            <td><?= e($template->code) ?></td>
                            <td><?= e($template->model) ?></td>
                            <td><?= e($template->sort_order) ?></td>
                            <td class="no-table-action">
                                <div class="no-page-index-table__action">
                                    <a href="<?= route('admin.product_templates.edit', ['id' => $template->id]) ?>" class="no-btn-action" data-tooltip>
                                        <div class="no-btn-action-ripple">
                                            <i class="fa-light fa-pen-to-square"></i>
                                            <span data-tooltip-text><span>수정</span><span data-tooltip-arrow></span></span>
                                        </div>
                                    </a>
                                    <a href="<?= route('admin.product_templates.destroy', ['id' => $template->id]) ?>" class="no-btn-action" data-tooltip data-method="delete" data-confirm="정말 삭제하시겠습니까?">
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

            <?= include_view('admin.components.pagination', ['paginator' => $templates]) ?>
        </div>
    </form>
</div>
<?php end_section() ?>
