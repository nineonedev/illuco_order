<?php 

use App\Domains\Communication\Enums\NoticeStatus;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'notice') ?>
<?php section('action', 'show') ?>
<?php section('title', '공지사항 상세보기') ?>

<?php section('content') ?>

<div class="no-notice-show">
    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">공지사항 상세보기</h1>
        </div>

        <div class="no-notice-show__block">
            <h2 class="no-notice-show__title"><?= e($notice->title) ?></h2>

            <dl class="no-notice-show__info">
                <div class="no-notice-show__row">
                    <dt>상태</dt>
                    <dd><?= NoticeStatus::getLabel($notice->status) ?></dd>
                </div>

                <div class="no-notice-show__row">
                    <dt>노출 시작일</dt>
                    <dd><?= $notice->visible_from ?? '-' ?></dd>
                </div>

                <div class="no-notice-show__row">
                    <dt>노출 종료일</dt>
                    <dd><?= $notice->visible_to ?? '-' ?></dd>
                </div>

                <div class="no-notice-show__row">
                    <dt>상단 고정</dt>
                    <dd><?= $notice->is_pinned ? '예' : '아니오' ?></dd>
                </div>
            </dl>

            <div class="no-notice-show__content">
                <?= $notice->content ?>
            </div>

            <div class="no-notice-show__files">
                <h3 class="no-notice-show__files-title">첨부파일</h3>
                <?php
                    $files = $notice->fileattachment->all();
                ?>
                <?php if (!empty($files)) : ?>
                    <ul class="no-notice-show__files-list">
                        <?php foreach ($files as $file) : ?>
                            <li>
                                <a href="<?= $file->upload_path ?>" target="_blank">
                                    <?= e($file->original_name) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>첨부된 파일이 없습니다.</p>
                <?php endif; ?>
            </div>

            <div class="no-notice-show__actions">
                <a href="<?= route('admin.notices.index') ?>" class="no-btn-primary-outline --sm">
                    목록으로
                </a>
                <?php if (user()->can('notice.edit')) : ?>
                    <a href="<?= route('admin.notices.edit', ['id' => $notice->id]) ?>" class="no-btn-primary --sm">
                        수정
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php end_section() ?>

<?php section('script') ?>
<?php end_section() ?>
