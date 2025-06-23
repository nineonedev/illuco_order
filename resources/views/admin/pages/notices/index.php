<?php 

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'notice') ?>
<?php section('action', 'index') ?>
<?php section('title', '공지사항 목록') ?>

<?php section('content') ?>
<div class="no-page-container">
    <form method="get">
        <div class="no-page-row">
            <div class="no-page-head">
                <h1 class="no-heading-sm">공지사항</h1>
                <p class="no-text-secondary">
                    본사에서 게시한 공지사항을 확인하실 수 있습니다.
                </p> 
            </div>

            <div class="no-page-index-filter">
                <div class="no-page-index-filter__form">
                    <div class="no-form-search --sm">
                        <label for="title" class="no-form-search-inner">
                            <fieldset class="no-form-search-label --blind">
                                <legend class="no-form-search-text">검색</legend>
                            </fieldset>
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input type="search" name="title" id="title" class="no-form-search-input" placeholder="Search">
                        </label>
                    </div>
                </div>


                <?php if (can('notice.create')): ?>
                <div class="no-page-index-link">
                    <a href="<?= route('admin.notices.create') ?>" class="no-btn-primary --sm">
                        <span>Create</span>
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <div class="no-page-index-table-outer">
                <table class="no-page-index-table">
                    <thead>
                        <tr>
                            <?php if (can('notice.create')): ?>
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
                            <?php endif ?>
                            <th>공지</th>
                            <th>제목</th>
                            <th>작성자</th>
                            <th>공개여부</th>
                            <th>등록일</th>
                            <?php if (can('notice.create')): ?>
                            <th>작업</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notices->items() as $i => $notice): ?>
                        <tr class="no-table-hover">
                            <?php if (can('notice.create')): ?>
                            <td class="no-table-check">
                                <div class="no-form-checkbox --xs">
                                    <label for="notice<?= $notice->id ?>" class="no-form-checkbox-pointer">
                                        <input type="checkbox" name="checked_ids[]" id="notice<?= $notice->id ?>" value="<?= $notice->id ?>" class="no-form-checkbox-input">
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
                                <?= $notice->is_pinned ? '<i class="fa-solid fa-megaphone"></i>' : '-' ?>
                            </td>
                            <td><?= e($notice->title) ?></td>
                            <td><?= e($notice->author_name ?? '관리자') ?></td>
                            <td><?= $notice->status === 'public' ? '공개' : '비공개' ?></td>
                            <td><?= date('Y-m-d', strtotime($notice->created_at ?? 'now')) ?></td>

                            <?php if (can('notice.create')): ?>
                            <td class="no-table-action">
                                <div class="no-page-index-table__action">
                                    <a href="<?= route('admin.notices.show', ['id' => $notice->id]) ?>" class="no-btn-action" data-tooltip>
                                        <div class="no-btn-action-ripple">
                                            <i class="fa-light fa-eye"></i>
                                            <span data-tooltip-text><span>보기</span><span data-tooltip-arrow></span></span>
                                        </div>
                                    </a>
                                    <a href="" class="no-btn-action" data-tooltip>
                                        <div class="no-btn-action-ripple">
                                            <i class="fa-light fa-copy"></i>
                                            <span data-tooltip-text><span>복사</span><span data-tooltip-arrow></span></span>
                                        </div>
                                    </a>
                                    <a href="<?= route('admin.notices.edit', ['id' => $notice->id]) ?>" class="no-btn-action" data-tooltip>
                                        <div class="no-btn-action-ripple">
                                            <i class="fa-light fa-pen-to-square"></i>
                                            <span data-tooltip-text><span>수정</span><span data-tooltip-arrow></span></span>
                                        </div>
                                    </a>
                                    <a href="<?= route('admin.notices.destroy', ['id' => $notice->id]) ?>" class="no-btn-action" data-tooltip data-method="delete" data-confirm="정말 삭제하시겠습니까?">
                                        <div class="no-btn-action-ripple">
                                            <i class="fa-light fa-trash-can"></i>
                                            <span data-tooltip-text><span>삭제</span><span data-tooltip-arrow></span></span>
                                        </div>
                                    </a>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            
            <?= include_view('admin.components.pagination', ['paginator' => $notices]) ?>
        </div>
    </form>
</div>
<?php end_section() ?>
