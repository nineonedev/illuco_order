<?php extend('layouts.mail'); ?>

<?php section('title', '[일루코] 클레임 접수 안내') ?>

<?php section('style') ?>
<style>
    body {
        margin: 0;
        padding: 0;
        background: #f5f6fa;
    }

    .no-claim-mail {
        background: #ffffff;
        border: 1px solid #e1e5eb;
        border-radius: 8px;
        padding: 24px;
        max-width: 800px;
        margin: 40px auto;
        font-family: 'Noto Sans KR', sans-serif;
        color: #333333;
    }

    .no-claim-mail__title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 16px;
        color: #d93f0b;
    }

    .no-claim-mail__box {
        background: #f9fafb;
        border: 1px solid #e1e5eb;
        border-radius: 6px;
        padding: 16px;
        margin-bottom: 20px;
    }

    .no-claim-mail__box p {
        margin: 8px 0;
        font-size: 14px;
    }

    .no-claim-mail__box .label {
        display: inline-block;
        width: 100px;
        font-weight: 600;
        color: #555;
    }

    .no-claim-mail__box .value {
        color: #222;
    }

    .no-claim-mail__content {
        font-size: 14px;
        line-height: 1.6;
        color: #333333;
    }

    .no-claim-mail__action {
        text-align: center;
        margin-top: 24px;
    }

    .no-claim-mail__btn {
        display: inline-block;
        background: #d93f0b;
        color: #ffffff !important;
        text-decoration: none;
        padding: 12px 24px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        transition: background 0.3s;
    }

    .no-claim-mail__btn:hover {
        background: #a83007;
    }
</style>
<?php end_section() ?>

<?php section('content') ?>
<div class="no-claim-mail">
    <h1 class="no-claim-mail__title">[일루코] 클레임 접수 안내</h1>

    <div class="no-claim-mail__box">
        <p><span class="label">제목</span> <span class="value"><?= e($claim->title) ?></span></p>
        <p><span class="label">작성자</span> <span class="value"><?= e($claim->user->name ?? $claim->orderer_name ?? '-') ?></span></p>
        <p><span class="label">접수일</span> <span class="value"><?= e($claim->created_at) ?></span></p>
        <p><span class="label">제품명</span> <span class="value"><?= e($claim->product_name ?? '-') ?></span></p>
        <p><span class="label">모델명</span> <span class="value"><?= e($claim->product_model ?? '-') ?></span></p>
        <p><span class="label">시리얼</span> <span class="value"><?= e($claim->product_serial_number ?? '-') ?></span></p>
    </div>

    <div class="no-claim-mail__content">
        <?= nl2br($claim->content) ?>
    </div>

    <?php if (!empty($claim->fileattachment)): ?>
        <div class="no-claim-mail__box">
            <h2 style="font-size:16px; font-weight:600; margin-bottom:10px; color:#d93f0b;">첨부파일</h2>
            <?php foreach ($claim->fileattachment as $file): ?>
                <p><a href="<?= e($file->upload_url) ?>" target="_blank"><?= e($file->origin_name) ?></a></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="no-claim-mail__action">
        <a href="<?= request()->http()->origin() . route('admin.claims.show', ['id' => $claim->id]) ?>" class="no-claim-mail__btn">
            클레임 상세 보기
        </a>
    </div>
</div>
<?php end_section() ?>
