<?php
/**
 * View: resources/views/admin/loupe-settings/edit.php
 * PHP 7.4 기준
 *
 * 기대 데이터
 * - $setting : \App\Domains\Product\Entities\LoupeSetting (template, frameColors eager loaded 권장)
 * - $colors  : LoupeFrameColor[] (id, name, hex, sort_order, is_active)
 */
?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'loupeSetting') ?>
<?php section('action', 'edit') ?>
<?php section('title', '루페 세팅 수정') ?>

<?php
    // 선택된 프레임 컬러 id 목록
    $selectedColorIds = [];
    if (!empty($setting->frameColors)) {
        foreach ((array) $setting->frameColors as $c) {
            $selectedColorIds[] = (int) ($c->id ?? 0);
        }
    }
    $tplName = $setting->template->name ?? null;
?>

<?php section('content') ?>
<div class="no-form-container">
    <div class="no-page-row">
        <div class="no-page-head">
            <div class="no-page-head__between">
                <div>
                    <h1 class="no-heading-sm">루페 세팅 수정</h1>
                    <p class="no-text-secondary no-mt-4">필요한 범위를 수정하고 저장하세요.</p>
                </div>
                <div class="no-text-xs no-text-muted">
                    ID: <?= (int) $setting->id ?>
                </div>
            </div>
        </div>

        <form
            method="post"
            id="frm"
            action="<?= route('admin.loupe_settings.update', ['id' => $setting->id]) ?>"
        >
            <?= csrf_field() ?>
            <?= method_field('put') ?>

            <div class="no-form-inner">
                <div class="no-form-group --md">

                    <!-- 제품 템플릿 선택 -->
                    <div class="">
                        <h3 class="no-heading-xxs">제품 선택</h3>
                        <p class="no-text-secondary no-mt-4">세팅을 적용할 루페 제품 템플릿을 선택하세요.</p>
                    </div>

                    <div class="no-form-control --md">
                        <div id="template-hook" <?= $tplName ? 'data-current-label="'.e($tplName).'"' : '' ?>></div>
                        <input type="hidden" name="template_id" value="<?= (int) $setting->template_id ?>">
                        <span class="no-form-control-space"></span>
                        <?php if ($tplName): ?>
                            <div class="no-text-xs no-text-muted">현재 선택: <strong><?= e($tplName) ?></strong></div>
                        <?php endif; ?>
                    </div>

                    <!-- WD (cm) -->
                    <div class="no-form-divider --lg">
                        <h3 class="no-heading-xxs">WD (Working Distance, cm)</h3>
                    </div>
                    <div class="no-grid --cols-2 --gap-md">
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="wd_min">
                                <input type="number" step="0.1" name="wd_min" id="wd_min" class="no-form-control-input" placeholder
                                        value="<?= e($setting->wd_min) ?>">
                                <fieldset class="no-form-control-label"><legend class="no-form-control-text">WD 최소 (cm)</legend></fieldset>
                            </label>
                        </div>
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="wd_max">
                                <input type="number" step="0.1" name="wd_max" id="wd_max" class="no-form-control-input" placeholder
                                        value="<?= e($setting->wd_max) ?>">
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
                                <input type="number" step="0.1" name="vd_min" id="vd_min" class="no-form-control-input" placeholder
                                        value="<?= e($setting->vd_min) ?>">
                                <fieldset class="no-form-control-label"><legend class="no-form-control-text">VD 최소 (mm)</legend></fieldset>
                            </label>
                        </div>
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="vd_max">
                                <input type="number" step="0.1" name="vd_max" id="vd_max" class="no-form-control-input" placeholder
                                        value="<?= e($setting->vd_max) ?>">
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
                                <input type="number" step="0.1" name="pd_right_min" id="pd_right_min" class="no-form-control-input" placeholder
                                        value="<?= e($setting->pd_right_min) ?>">
                                <fieldset class="no-form-control-label"><legend class="no-form-control-text">RIGHT 최소 (mm)</legend></fieldset>
                            </label>
                        </div>
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="pd_right_max">
                                <input type="number" step="0.1" name="pd_right_max" id="pd_right_max" class="no-form-control-input" placeholder
                                        value="<?= e($setting->pd_right_max) ?>">
                                <fieldset class="no-form-control-label"><legend class="no-form-control-text">RIGHT 최대 (mm)</legend></fieldset>
                            </label>
                        </div>
                    </div>

                    <div class="no-grid --cols-2 --gap-md">
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="pd_left_min">
                                <input type="number" step="0.1" name="pd_left_min" id="pd_left_min" class="no-form-control-input" placeholder
                                        value="<?= e($setting->pd_left_min) ?>">
                                <fieldset class="no-form-control-label"><legend class="no-form-control-text">LEFT 최소 (mm)</legend></fieldset>
                            </label>
                        </div>
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="pd_left_max">
                                <input type="number" step="0.1" name="pd_left_max" id="pd_left_max" class="no-form-control-input" placeholder
                                        value="<?= e($setting->pd_left_max) ?>">
                                <fieldset class="no-form-control-label"><legend class="no-form-control-text">LEFT 최대 (mm)</legend></fieldset>
                            </label>
                        </div>
                    </div>

                    <!-- TOTAL PD -->
                    <div class="no-grid --cols-1 --gap-md">
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="pd_total_distance">
                                <input type="number" step="0.1" name="pd_total_distance" id="pd_total_distance" class="no-form-control-input" placeholder
                                        value="<?= e($setting->pd_total_distance) ?>">
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
                            <?php $checked = in_array((int)$c->id, $selectedColorIds, true) ? 'checked' : ''; ?>
                            <label class="loupe-chip no-chip --clickable <?= $checked ? 'is-checked' : '' ?>">
                                <input type="checkbox" name="frame_color_ids[]" value="<?= (int)$c->id ?>" style="display:none;" <?= $checked ?>>
                                <span class="loupe-chip__btn no-flex --middle">
                                    <span class="no-chip__dot" style="background: <?= e($c->hex ?? '#999') ?>;"></span>
                                    <span class="no-chip__text"><?= e($c->name ?? ('#'.$c->id)) ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                </div>

                <hr class="no-hr --xl">

                <div class="no-form-action no-flex --between --middle">
                    <div class="no-text-muted no-text-xs">수정 후 저장을 눌러주세요.</div>
                    <div class="no-flex --middle" style="gap:.8rem;">
                        <a href="<?= route('admin.loupe_settings.index') ?>" data-action="cancel" class="no-btn-primary-outline --sm">
                            <span>목록</span>
                        </a>
                        <button type="button" data-action="delete" class="no-btn-error --sm">
                            <span>삭제</span>
                        </button>
                        <button type="submit" class="no-btn-primary --sm">
                            <span>저장</span>
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
<?php end_section() ?>
