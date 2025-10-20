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
                            <?php
                                $ext = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));

                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'])) {
                                    $fileType = 'image';
                                } elseif (in_array($ext, ['mp4', 'mov', 'avi', 'webm'])) {
                                    $fileType = 'video';
                                } elseif (in_array($ext, ['pdf'])) {
                                    $fileType = 'document';
                                } else {
                                    $fileType = 'file';
                                }
                            ?>
                            <li class="file-type-<?= $fileType ?>">
                                <div class="file-preview">
                                    <?php if ($fileType === 'image') : ?>
                                        <img src="<?= e($file->upload_path) ?>" alt="<?= e($file->original_name) ?>" style="max-width: 100%; height: auto; border: 1px solid #ccc;">
                                        <div><?= e($file->original_name) ?></div>

                                    <?php elseif ($fileType === 'video') : ?>
                                        <video controls style="max-width: 100%;">
                                            <source src="<?= e($file->upload_path) ?>" type="video/<?= $ext ?>">
                                            해당 브라우저는 video 태그를 지원하지 않습니다.
                                        </video>
                                        <div><?= e($file->original_name) ?></div>

                                    <?php elseif ($fileType === 'document') : ?>
                                        <iframe src="<?= e($file->upload_path) ?>" style="width: 100%; height: 500px; border: 1px solid #ccc;"></iframe>
                                        <div><?= e($file->original_name) ?></div>

                                    <?php else : ?>
                                        <a href="<?= e($file->upload_path) ?>" target="_blank" download>
                                            📁 <?= e($file->original_name) ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
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
            </div>
        </div>
    </div>
</div>

<?php end_section() ?>

<?php section('script') ?>
<?php end_section() ?>
