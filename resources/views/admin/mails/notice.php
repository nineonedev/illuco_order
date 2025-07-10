<?php extend('layouts.mail'); ?>

<?php section('title', '[일루코] 공지사항 안내') ?>

<?php section('style') ?>
<style>
    body {
        margin: 0;
        padding: 0;
        background: #f5f6fa;
    }

    .no-notice-mail {
        background: #ffffff;
        border: 1px solid #e1e5eb;
        border-radius: 8px;
        padding: 24px;
        max-width: 800px;
        margin: 40px auto;
        font-family: 'Noto Sans KR', sans-serif;
        color: #333333;
    }

    .no-notice-mail__title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 16px;
        color: #0052cc;
    }

    .no-notice-mail__box {
        background: #f9fafb;
        border: 1px solid #e1e5eb;
        border-radius: 6px;
        padding: 16px;
        margin-bottom: 20px;
    }

    .no-notice-mail__box p {
        margin: 8px 0;
        font-size: 14px;
    }

    .no-notice-mail__box .label {
        display: inline-block;
        width: 100px;
        font-weight: 600;
        color: #555;
    }

    .no-notice-mail__box .value {
        color: #222;
    }

    .no-notice-mail__content {
        font-size: 14px;
        line-height: 1.6;
        color: #333333;
    }

    .no-notice-mail__action {
        text-align: center;
        margin-top: 24px;
    }

    .no-notice-mail__btn {
        display: inline-block;
        background: #0052cc;
        color: #ffffff !important;
        text-decoration: none;
        padding: 12px 24px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        transition: background 0.3s;
    }

    .no-notice-mail__btn:hover {
        background: #003d99;
    }
</style>
<?php end_section() ?>

<?php section('content') ?>
<div class="no-notice-mail">
    <h1 class="no-notice-mail__title">[일루코] 공지사항 안내</h1>

    <div class="no-notice-mail__box">
        <p><span class="label">제목</span> <span class="value"><?= e($notice->title) ?></span></p>
        <p><span class="label">작성자</span> <span class="value"><?= e($notice->writer->name ?? '-') ?></span></p>
        <p><span class="label">등록일</span> <span class="value"><?= e($notice->created_at) ?></span></p>
    </div>

    <div class="no-notice-mail__content">
        <?= nl2br(e($notice->content)) ?>
    </div>

    <?php if (!empty($notice->fileattachment)): ?>
        <div class="no-notice-mail__box">
            <h2 style="font-size:16px; font-weight:600; margin-bottom:10px; color:#0052cc;">첨부파일</h2>
            <?php foreach ($notice->fileattachment as $file): ?>
                <p><a href="<?= e($file->upload_url) ?>" target="_blank"><?= e($file->origin_name) ?></a></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="no-notice-mail__action">
        <a href="<?= request()->http()->origin() . route('admin.notices.show', ['id' => $notice->id]) ?>" class="no-notice-mail__btn">
            공지 상세 보기
        </a>
    </div>
</div>
<?php end_section() ?>
