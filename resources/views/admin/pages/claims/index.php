<?php extend('layouts.admin'); ?>

<?php section('title') ?>
클레임
<?php end_section() ?>

<?php section('content') ?>
<div class="no-page-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">클레임</h1>
            <p class="no-text-secondary">
                대리점에 등록된 클레임을 확인하실 수 있습니다.
            </p> 
        </div>
        <!-- Head -->

        <div class="no-page-index-filter">
            <form action="" class="no-page-index-filter__form">
                <div class="no-form-search --sm">
                    <label for="title" class="no-form-search-inner">
                        <fieldset class="no-form-search-label --blind">
                            <legend class="no-form-search-text">검색</legend>
                        </fieldset>
                        <div class="no-form-search__icon">
                            <i class="fa-light fa-magnifying-glass"></i>
                        </div>
                        <input type="search" name="title" id="title" class="no-form-search-input" placeholder="Search" >
                    </label>
                </div>

            </form>

            <div class="no-page-index-link">
                <a href="<?=route('admin.claims.create')?>" class="no-btn-primary --sm">
                    <span>Create</span>
                </a>
            </div>
        </div>
        <!-- Filter -->
        
        <form method="get" class="no-page-index-table-outer">
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
                        <th>대리점</th>
                        <th>제품</th>
                        <th>제목</th>
                        <th>주문자</th>
                        <th>작성자</th>
                        <th>등록일</th>
                        <th>작업</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($claims->items() as $claim): ?>
                    <tr class="no-table-hover">
                        <td class="no-table-check">
                            <div class="no-form-checkbox --xs">
                                <label for="claim<?= $claim->id ?>" class="no-form-checkbox-pointer">
                                    <input type="checkbox" name="claims[]" id="claim<?= $claim->id ?>" class="no-form-checkbox-input">
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
                        <td><?= e($claim->dealer->name ?? '대리점 없음') ?></td>
                        <td><?= e($claim->product->name ?? '제품 없음') ?></td>
                        <td><?= e($claim->title) ?></td>
                        <td><?= e($claim->orderer_name) ?></td>
                        <td><?= e($claim->author_name ?? '관리자') ?></td>
                        <td><?= date('Y-m-d', strtotime($claim->created_at ?? 'now')) ?></td>
                        <td class="no-table-action">
                            <div class="no-page-index-table__action">
                                <a href="<?= route('admin.claims.show', ['id' => $claim->id]) ?>" class="no-btn-action" data-tooltip>
                                    <div class="no-btn-action-ripple">
                                        <i class="fa-light fa-eye"></i>
                                        <span data-tooltip-text><span>보기</span><span data-tooltip-arrow></span></span>
                                    </div>
                                </a>
                                <a href="<?= route('admin.claims.edit', ['id' => $claim->id]) ?>" class="no-btn-action" data-tooltip>
                                    <div class="no-btn-action-ripple">
                                        <i class="fa-light fa-pen-to-square"></i>
                                        <span data-tooltip-text><span>수정</span><span data-tooltip-arrow></span></span>
                                    </div>
                                </a>
                                <a href="<?= route('admin.claims.destroy', ['id' => $claim->id]) ?>" class="no-btn-action" data-tooltip data-method="delete" data-confirm="정말 삭제하시겠습니까?">
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
        </form>
        <!-- Table -->
        
        <?= include_view('admin.components.pagination', ['paginator' => $claims]) ?>

    </div>
    <!-- Row -->
</div>

<?php end_section() ?>
