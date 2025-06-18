<?php

use App\Domains\Product\Entities\ProductTemplate;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'product_template') ?>
<?php section('action', 'show') ?>
<?php section('title', $template->name) ?>

<?php section('content') ?>
<div class="no-form-container">
    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm"><?= $template->name ?></h1>
        </div>

        <div class="no-form-inner --readonly">
            <div class="no-form-group">

                <!-- 제품 이름 -->
                <div class="no-form-control --md">
                    <label class="no-form-control-inner">
                        <div class="no-form-control-label">제품 이름</div>
                        <div class="no-form-control-value"><?= e($template->name) ?></div>
                    </label>
                </div>

                <!-- 제품 코드 -->
                <div class="no-form-control --md">
                    <label class="no-form-control-inner">
                        <div class="no-form-control-label">제품 코드</div>
                        <div class="no-form-control-value"><?= e($template->code) ?></div>
                    </label>
                </div>

                <!-- 모델명 -->
                <div class="no-form-control --md">
                    <label class="no-form-control-inner">
                        <div class="no-form-control-label">모델명</div>
                        <div class="no-form-control-value"><?= e($template->model) ?></div>
                    </label>
                </div>

                <!-- 카테고리 -->
                <div class="no-form-control --md">
                    <label class="no-form-control-inner">
                        <div class="no-form-control-label">카테고리</div>
                        <div class="no-form-control-value"><?= e($template->category->name ?? '-') ?></div>
                    </label>
                </div>

                <!-- 대표 이미지 -->
                <div class="no-form-control --md">
                    <label class="no-form-control-inner">
                        <div class="no-form-control-label">대표 이미지</div>
                        <div class="no-form-control-value">
                            <?php if ($mainImage = $template->fileattachment->get('main_image')): ?>
                                <img src="<?= $mainImage->upload_path ?>" alt="대표 이미지" style="max-width: 200px;">
                            <?php else: ?>
                                <span>-</span>
                            <?php endif; ?>
                        </div>
                    </label>
                </div>

                <!-- 정렬 순서 -->
                <div class="no-form-control --md">
                    <label class="no-form-control-inner">
                        <div class="no-form-control-label">정렬 순서</div>
                        <div class="no-form-control-value"><?= (int) $template->sort_order ?></div>
                    </label>
                </div>

                <!-- 제품 설명 -->
                <div class="no-form-control --md">
                    <label class="no-form-control-inner">
                        <div class="no-form-control-label">제품 설명</div>
                        <div class="no-form-control-value"><?= nl2br(e($template->description)) ?></div>
                    </label>
                </div>

            </div>

            <div class="no-form-action">
                <a href="<?= route('admin.product_templates.index') ?>" class="no-btn-primary-outline --sm">
                    <span>목록으로</span>
                </a>
                <a href="<?= route('admin.product_templates.edit', ['id' => $template->id]) ?>" class="no-btn-primary --sm">
                    <span>수정하기</span>
                </a>
            </div>
        </div>
    </div>
</div>
<?php end_section() ?>
