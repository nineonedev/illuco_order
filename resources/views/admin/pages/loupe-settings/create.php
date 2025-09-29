<?php
/**
 * View: resources/views/admin/loupe-settings/create.php
 * PHP 7.4 기준
 *
 * 기대 데이터
 * - $colors : LoupeFrameColor[] (id, name, hex, sort_order, is_active)
 */
?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'loupeSetting') ?>
<?php section('action', 'create') ?>
<?php section('title', '루페 세팅 생성') ?>

<?php section('content') ?>
<div class="no-form-container">
    <div class="no-page-row">
        <div class="no-page-head">
        <h1 class="no-heading-sm">루페 세팅 생성</h1>
        <p class="no-text-secondary">제품 템플릿을 선택하고 허용 범위를 입력하세요.</p>
        </div>

        <form
        method="post"
        id="frm"
        action="<?= route('admin.loupe_settings.store') ?>"
        >
        <?= csrf_field() ?>

        <div class="no-form-inner">
                <div class="no-form-group --md">

                    <!-- 제품 템플릿 선택 -->
                    <div class="">
                        <h3 class="no-heading-xxs">제품 선택</h3>
                        <p class="no-text-secondary no-mt-4">세팅을 적용할 루페 제품 템플릿을 선택하세요.</p>
                    </div>

                    <div class="no-form-control --md">
                        <div id="template-hook"></div>
                        <input type="hidden" name="template_id" value="">
                        <span class="no-form-control-space"></span>
                    </div>

                    <!-- WD (cm) -->
                    <div class="no-form-divider --lg">
                        <h3 class="no-heading-xxs">WD (Working Distance, cm)</h3>
                    </div>
                    <div class="no-grid --cols-2 --gap-md">
                        <div class="no-form-control --md">
                        <label class="no-form-control-inner" for="wd_min">
                            <input type="number" step="0.1" name="wd_min" id="wd_min" class="no-form-control-input" placeholder>
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">WD 최소 (cm)</legend></fieldset>
                        </label>
                        </div>
                        <div class="no-form-control --md">
                        <label class="no-form-control-inner" for="wd_max">
                            <input type="number" step="0.1" name="wd_max" id="wd_max" class="no-form-control-input" placeholder>
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">WD 최대 (cm)</legend></fieldset>
                        </label>
                        </div>
                    </div>

                    <!-- VD (mm) -->
                    <div class="no-form-divider --lg">
                        <h3 class="no-heading-xxs">VD (Vertex Distance, mm)</h3>
                        <p class="no-text-secondary no-mt-4">권장 범위: 10 ~ 25mm</p>
                    </div>
                    <div class="no-grid --cols-2 --gap-md">
                        <div class="no-form-control --md">
                        <label class="no-form-control-inner" for="vd_min">
                            <input type="number" step="0.1" name="vd_min" id="vd_min" class="no-form-control-input" placeholder>
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">VD 최소 (mm)</legend></fieldset>
                        </label>
                        </div>
                        <div class="no-form-control --md">
                        <label class="no-form-control-inner" for="vd_max">
                            <input type="number" step="0.1" name="vd_max" id="vd_max" class="no-form-control-input" placeholder>
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">VD 최대 (mm)</legend></fieldset>
                        </label>
                        </div>
                    </div>

                    <!-- far PD (mm) -->
                    <div class="no-form-divider --lg">
                        <h3 class="no-heading-xxs">far PD (mm)</h3>
                    </div>
                    <div class="no-grid --cols-2 --gap-md">
                        <div class="no-form-control --md">
                        <label class="no-form-control-inner" for="pd_right_min">
                            <input type="number" step="0.1" name="pd_right_min" id="pd_right_min" class="no-form-control-input" placeholder>
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">RIGHT 최소 (mm)</legend></fieldset>
                        </label>
                        </div>
                        <div class="no-form-control --md">
                        <label class="no-form-control-inner" for="pd_right_max">
                            <input type="number" step="0.1" name="pd_right_max" id="pd_right_max" class="no-form-control-input" placeholder>
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">RIGHT 최대 (mm)</legend></fieldset>
                        </label>
                        </div>
                    </div>

                    <div class="no-grid --cols-2 --gap-md">
                        <div class="no-form-control --md">
                        <label class="no-form-control-inner" for="pd_left_min">
                            <input type="number" step="0.1" name="pd_left_min" id="pd_left_min" class="no-form-control-input" placeholder>
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">LEFT 최소 (mm)</legend></fieldset>
                        </label>
                        </div>
                        <div class="no-form-control --md">
                        <label class="no-form-control-inner" for="pd_left_max">
                            <input type="number" step="0.1" name="pd_left_max" id="pd_left_max" class="no-form-control-input" placeholder>
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">LEFT 최대 (mm)</legend></fieldset>
                        </label>
                        </div>
                    </div>

                    <!-- TOTAL PD -->
                    <div class="no-grid --cols-1 --gap-md">
                        <div class="no-form-control --md">
                        <label class="no-form-control-inner" for="pd_total_distance">
                            <input type="number" step="0.1" name="pd_total_distance" id="pd_total_distance" class="no-form-control-input" placeholder>
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">TOTAL PD 편차(mm)</legend></fieldset>
                        </label>
                        <div id="pd-helper" class="no-text-xs" style="margin-top:4px;"></div>
                        </div>
                    </div>

                    <!-- 프레임 컬러 -->
                    <div class="no-form-divider --lg">
                        <h3 class="no-heading-xxs">프레임 컬러</h3>
                        <p class="no-text-secondary no-mt-4">복수 선택 가능합니다.</p>
                    </div>

                    <div class="loupe-setting__chips no-flex --wrap">
                        <?php foreach ($colors as $c): ?>
                        <label class="loupe-chip no-chip --clickable">
                            <input type="checkbox" name="frame_color_ids[]" value="<?= $c->id ?>" style="display:none;">
                            <span class="loupe-chip__btn no-flex --middle">
                            <span class="no-chip__dot" style="background: <?= $c->hex ?? '#999' ?>;"></span>
                            <span class="no-chip__text"><?= e($c->name ?? ('#'.$c->id)) ?></span>
                            </span>
                        </label>
                        <?php endforeach; ?>
                    </div>

                </div>

                <hr class="no-hr --xl">

                <div class="no-form-action">
                <a href="<?= route('admin.loupe_settings.index') ?>" data-action="cancel" class="no-btn-primary-outline --sm">
                    <span>취소</span>
                </a>
                <button type="submit" class="no-btn-primary --sm">
                    <span>저장</span>
                </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php end_section() ?>
