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

                    <!-- 제품명 검색 -->
                    <div class="no-form-search">
                        <label for="name" class="no-form-label">제품명</label>
                        <div class="no-form-search-inner">
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input
                                type="search"
                                name="name"
                                id="name"
                                class="no-form-search-input"
                                placeholder="제품명 검색"
                                value="<?= e(request()->query('name')) ?>"
                            >
                        </div>
                    </div>

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

                    <!-- 모델 검색 -->
                    <div class="no-form-search">
                        <label for="model" class="no-form-label">모델</label>
                        <div class="no-form-search-inner">
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input
                                type="search"
                                name="model"
                                id="model"
                                class="no-form-search-input"
                                placeholder="모델 검색"
                                value="<?= e(request()->query('model')) ?>"
                            >
                        </div>
                    </div>

                    <!-- 카테고리 선택 -->
                    <?php
                        $categoryProps = [
                            'spacing' => false,
                            'label' => '카테고리',
                            'name' => 'category_id',
                            'value' => request()->query('category_id'),
                            'options' => array_merge(
                                [['label' => '전체', 'value' => '']],
                                array_map(fn($cat) => [
                                    'label' => $cat->label,
                                    'value' => $cat->id,
                                ], $categories)
                            ),
                        ];
                    ?>
                    <div
                        class="no-form-field"
                        data-view-type="select"
                        data-view-props='<?= e(json_encode($categoryProps)) ?>'
                    ></div>

                    <!-- 정렬 선택 -->
                    <?php
                        $sortProps = [
                            'spacing' => false,
                            'label' => '정렬 선택',
                            'name' => 'sort',
                            'value' => request()->query('sort'),
                            'options' => [
                                ['label' => '전체', 'value' => ''],
                                ['label' => '이름 ↑', 'value' => 'name_asc'],
                                ['label' => '이름 ↓', 'value' => 'name_desc'],
                                ['label' => '등록일 ↑', 'value' => 'created_at_asc'],
                                ['label' => '등록일 ↓', 'value' => 'created_at_desc'],
                                ['label' => '정렬순서 ↑', 'value' => 'sort_order_asc'],
                                ['label' => '정렬순서 ↓', 'value' => 'sort_order_desc'],
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
                    <a href="<?= route('admin.product_templates.index') ?>" class="no-btn-success --sm">
                        <span>필터 초기화</span>
                    </a>
                    <?php if (can('product.delete')): ?>
                    <button id="select-delete-btn" class="no-btn-error --sm" type="button" disabled>
                        <span>선택삭제</span>
                    </button>
                    <?php endif; ?>

                    <a href="<?= route('admin.product_templates.create') ?>" class="no-btn-primary --sm">
                        <span>Create</span>
                    </a>
                </div>
            </div>

            <div class="no-page-index-table-outer">
                <table class="no-page-index-table">
                    <thead>
                        <tr>
                            <?php if (can('order.delete')): ?>
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
                            <?php if (can('product.delete')): ?>
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
                            <?php endif; ?>
                            <td>
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
                                    <?php if (can('product.delete')): ?>
                                    <a href="<?= route('admin.product_templates.destroy', ['id' => $template->id]) ?>" data-item-action="delete" class="no-btn-action" data-tooltip data-method="delete" data-confirm="정말 삭제하시겠습니까?">
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
            </div>

            <?= include_view('admin.components.pagination', ['paginator' => $templates]) ?>
        </div>
    </form>
</div>
<?php end_section() ?>
