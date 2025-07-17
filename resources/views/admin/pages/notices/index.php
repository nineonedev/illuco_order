<?php

use App\Domains\Communication\Enums\NoticeStatus;

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
                    
                    <!-- 🔎 제목 검색 -->
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
                                value="<?= e(request()->query('title')) ?>"
                                class="no-form-search-input"
                                placeholder="제목 검색">
                        </div>
                    </div>

                    <!-- ✅ 공개 여부 -->
                    <?php
                        $props = [
                            'spacing' => false,
                            'label' => '공개여부',
                            'name' => 'status',
                            'value' => request()->query('status'),
                            'options' => array_merge(
                                [['label' => '전체', 'value' => '']],
                                array_map(fn($status) => [
                                    'label' => NoticeStatus::labels()[$status] ?? $status,
                                    'value' => $status,
                                ], NoticeStatus::all())
                            ),
                        ];
                    ?>
                    <div
                        class="no-form-field"
                        data-view-type="select"
                        data-view-props='<?= e(json_encode($props)) ?>'
                    ></div>

                    <!-- ✅ 고정 여부 -->
                    <?php
                        $props = [
                            'spacing' => false,
                            'label' => '고정여부',
                            'name' => 'is_pinned',
                            'value' => request()->query('is_pinned'),
                            'options' => [
                                ['label' => '전체', 'value' => ''],
                                ['label' => '고정', 'value' => '1'],
                                ['label' => '비고정', 'value' => '0'],
                            ],
                        ];
                    ?>
                    <div
                        class="no-form-field"
                        data-view-type="select"
                        data-view-props='<?= e(json_encode($props)) ?>'
                    ></div>

                    <!-- ✅ 작성자 -->
                    <div class="no-form-search">
                        <label for="author" class="no-form-label">작성자</label>
                        <div class="no-form-search-inner">
                            <div class="no-form-search__icon">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </div>
                            <input
                                type="search"
                                name="author"
                                id="author"
                                value="<?= e(request()->query('author')) ?>"
                                class="no-form-search-input"
                                placeholder="작성자 검색">
                        </div>
                    </div>

                    <!-- ✅ 정렬 -->
                    <?php
                        $props = [
                            'spacing' => false,
                            'label' => '정렬',
                            'name' => 'sort',
                            'value' => request()->query('sort'),
                            'options' => [
                                ['label' => '전체', 'value' => ''],
                                ['label' => '최신순', 'value' => 'created_at_desc'],
                                ['label' => '오래된순', 'value' => 'created_at_asc'],
                                ['label' => '제목 오름차순', 'value' => 'title_asc'],
                                ['label' => '제목 내림차순', 'value' => 'title_desc'],
                            ],
                        ];
                    ?>
                    <div
                        class="no-form-field"
                        data-view-type="select"
                        data-view-props='<?= e(json_encode($props)) ?>'
                    ></div>

                    <div class="no-page-index-link">
                        <button type="submit" class="no-btn-primary --sm">검색</button>
                    </div>
                </div>

                <div class="no-page-index-link">
                    <?php if (can('notice.delete')): ?>
                    <button id="select-delete-btn" class="no-btn-error --sm" type="button" disabled>
                        <span>선택삭제</span>
                    </button>
                    <?php endif; ?>
                    <?php if (can('notice.create')): ?>
                        <a href="<?= route('admin.notices.create') ?>" class="no-btn-primary --sm">
                            <span>Create</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="no-page-index-table-outer">
                <table class="no-page-index-table">
                    <thead>
                        <tr>
                            <?php if (can('notice.delete')): ?>
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
                        <?php foreach ($notices->items() as $notice): ?>
                            <tr class="no-table-hover">
                                <?php if (can('notice.delete')): ?>
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
                                        <a href="<?= route('admin.notices.edit', ['id' => $notice->id]) ?>" class="no-btn-action" data-tooltip>
                                            <div class="no-btn-action-ripple">
                                                <i class="fa-light fa-pen-to-square"></i>
                                                <span data-tooltip-text><span>수정</span><span data-tooltip-arrow></span></span>
                                            </div>
                                        </a>
                                        <a href="<?= route('admin.notices.destroy', ['id' => $notice->id]) ?>" data-item-action="delete" class="no-btn-action" data-tooltip data-method="delete" data-confirm="정말 삭제하시겠습니까?">
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
