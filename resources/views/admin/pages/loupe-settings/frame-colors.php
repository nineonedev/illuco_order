스타일을 최적화된 형태로 바꿔줄래? card형태로! input으로 입력하고 수정삭제는 있되! 왼쪽 리스트에서! 최종 html코드및 scss보여주!ㅓ 

<?php
/**
 * View: resources/views/admin/pages/loupe-settings/frame-colors.php
 *
 * 기대 데이터
 * - $colors : LoupeFrameColor[] (id, name, hex, sort_order, is_active)
 */
?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'loupeFrameColor') ?>
<?php section('action', 'index') ?>
<?php section('title', '루페 프레임 컬러') ?>

<?php section('content') ?>
<div class="no-form-outer no-cart">

    <div class="no-cart-grid">
        <!-- ===== 왼쪽: 목록/행 수정 ===== -->
        <div class="no-cart-area">
            <div class="no-page-row">
                <div class="no-page-head">
                    <h1 class="no-heading-sm">프레임 컬러</h1>
                    <p class="no-text-secondary">등록된 프레임 컬러를 확인/수정할 수 있습니다.</p>
                </div>

                <div>
                    <?php if (!empty($colors)) : ?>
                        <ol class="no-category-items" id="frame-color-list">
                            <?php foreach ($colors as $color): ?>
                            <li class="no-category-item">
                                <form action="<?= route('admin.loupe_frame_colors.update', ['id' => $color->id]) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <?= put_field() ?>
                                    <div class="no-category-item-block">
                                        <div class="no-category-item-head">
                                            <div class="no-category-item-head-text">

                                                <!-- 미리보기 칩 -->
                                                <div class="no-flex --middle" style="margin-bottom:0.8rem;">
                                                    <span class="loupe-chip no-chip --xs" title="미리보기">
                                                        <span class="no-chip__dot" style="background: <?= e($color->hex ?? '#999999') ?>;"></span>
                                                        <span class="no-chip__text"><?= e($color->name ?? ('#'.$color->id)) ?></span>
                                                    </span>
                                                </div>

                                                <!-- 이름 -->
                                                <div class="no-form-control">
                                                    <label for="color_name_<?= $color->id ?>" class="no-form-control-inner">
                                                        <input type="text"
                                                            name="name"
                                                            id="color_name_<?= $color->id ?>"
                                                            value="<?= e($color->name) ?>"
                                                            class="no-form-control-input"
                                                            placeholder=""
                                                            required>
                                                        <fieldset class="no-form-control-label">
                                                            <legend class="no-form-control-text">이름</legend>
                                                        </fieldset>
                                                    </label>
                                                </div>

                                                <!-- HEX / 피커 -->
                                                <div class="no-grid --cols-2 --gap-md">
                                                    <div class="no-form-control">
                                                        <label for="color_hex_<?= $color->id ?>" class="no-form-control-inner">
                                                            <input type="text"
                                                                name="hex"
                                                                id="color_hex_<?= $color->id ?>"
                                                                value="<?= e($color->hex) ?>"
                                                                class="no-form-control-input"
                                                                placeholder="#FFFFFF"
                                                                pattern="^#([0-9a-fA-F]{6})$"
                                                                title="#RRGGBB 형식으로 입력">
                                                            <fieldset class="no-form-control-label">
                                                                <legend class="no-form-control-text">HEX</legend>
                                                            </fieldset>
                                                        </label>
                                                    </div>
                                                    <div class="no-form-control">
                                                        <label for="color_picker_<?= $color->id ?>" class="no-form-control-inner">
                                                            <input type="color"
                                                                    id="color_picker_<?= $color->id ?>"
                                                                    value="<?= e($color->hex ?: '#999999') ?>"
                                                                    class="no-form-control-input">
                                                            <fieldset class="no-form-control-label">
                                                                <legend class="no-form-control-text">컬러 피커</legend>
                                                            </fieldset>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- 정렬 / 활성 -->
                                                <div class="no-grid --cols-2 --gap-md">
                                                    <div class="no-form-control">
                                                        <label for="color_order_<?= $color->id ?>" class="no-form-control-inner">
                                                            <input type="number"
                                                                    name="sort_order"
                                                                    id="color_order_<?= $color->id ?>"
                                                                    value="<?= $color->sort_order ?>"
                                                                    class="no-form-control-input"
                                                                    placeholder="0">
                                                            <fieldset class="no-form-control-label">
                                                                <legend class="no-form-control-text">정렬 순서</legend>
                                                            </fieldset>
                                                        </label>
                                                    </div>
                                                    <div class="no-form-control">
                                                        <label class="no-form-control-inner" for="color_active_<?= $color->id ?>">
                                                            <input type="checkbox"
                                                                    id="color_active_<?= $color->id ?>"
                                                                    name="is_active"
                                                                    value="1"
                                                                    <?= !empty($color->is_active) ? 'checked' : '' ?>
                                                                    class="no-form-control-input">
                                                            <fieldset class="no-form-control-label">
                                                                <legend class="no-form-control-text">활성화</legend>
                                                            </fieldset>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="no-form-action" style="margin-top:0;">
                                            <button type="submit" class="no-btn-primary-outline --xs">
                                                <span>저장</span>
                                            </button>
                                            <a href="<?= route('admin.loupe_frame_colors.destroy', ['id' => $color->id]) ?>"
                                                data-item-action="delete"
                                                class="no-btn-error-outline --xs">
                                                <span>삭제</span>
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else: ?>
                        <div class="no-form-empty-fallback">
                            <p>등록된 프레임 컬러가 없습니다.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ===== 오른쪽: 추가 폼 ===== -->
        <div class="no-cart-area">
            <div class="no-page-row">
                <div class="no-page-head">
                    <h1 class="no-heading-sm">프레임 컬러 추가</h1>
                    <p class="no-text-secondary">새 컬러를 등록합니다.</p>
                </div>

                <form action="<?= route('admin.loupe_frame_colors.store') ?>" method="post" id="create-frame-color">
                    <?= csrf_field() ?>

                    <div class="no-form-group --md">
                        <!-- 이름 -->
                        <div class="no-form-control --md">
                            <label for="color_name_new" class="no-form-control-inner">
                                <input type="text" name="name" id="color_name_new" class="no-form-control-input" placeholder="" required>
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">이름</legend>
                                </fieldset>
                            </label>
                        </div>

                        <!-- HEX / 피커 -->
                        <div class="no-grid --cols-2 --gap-md">
                            <div class="no-form-control --md">
                                <label for="color_hex_new" class="no-form-control-inner">
                                    <input type="text"
                                        name="hex"
                                        id="color_hex_new"
                                        class="no-form-control-input"
                                        placeholder="#000000"
                                        value="#999999"
                                        pattern="^#([0-9a-fA-F]{6})$"
                                        title="#RRGGBB 형식으로 입력">
                                    <fieldset class="no-form-control-label">
                                        <legend class="no-form-control-text">HEX</legend>
                                    </fieldset>
                                </label>
                            </div>
                            <div class="no-form-control --md">
                                <label for="color_hex_picker_new" class="no-form-control-inner">
                                    <input type="color" id="color_hex_picker_new" class="no-form-control-input" value="#999999">
                                    <fieldset class="no-form-control-label">
                                        <legend class="no-form-control-text">컬러 피커</legend>
                                    </fieldset>
                                </label>
                            </div>
                        </div>

                        <!-- 정렬 / 활성 -->
                        <div class="no-grid --cols-2 --gap-md">
                            <div class="no-form-control --md">
                                <label for="color_order_new" class="no-form-control-inner">
                                    <input type="number" name="sort_order" id="color_order_new" class="no-form-control-input" placeholder="0" value="0">
                                    <fieldset class="no-form-control-label">
                                        <legend class="no-form-control-text">정렬 순서</legend>
                                    </fieldset>
                                </label>
                            </div>
                            <div class="no-form-control --md">
                                <label class="no-form-control-inner" for="color_active_new">
                                    <input type="checkbox" id="color_active_new" name="is_active" value="1" class="no-form-control-input" checked>
                                    <fieldset class="no-form-control-label">
                                        <legend class="no-form-control-text">활성화</legend>
                                    </fieldset>
                                </label>
                            </div>
                        </div>

                        <!-- 미리보기 -->
                        <div class="no-form-divider --sm">
                            <div class="no-flex --middle" id="new-color-preview">
                                <span class="loupe-chip no-chip --xs">
                                    <span class="no-chip__dot" style="background:#999999;"></span>
                                    <span class="no-chip__text">미리보기</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="no-form-action">
                        <button type="submit" class="no-btn-primary --sm">
                            <span>추가</span>
                        </button>
                    </div>
                </form>

                <!-- 간단 동기화 스크립트(선택) -->
                <script>
                    (function(){
                        const hex = document.getElementById('color_hex_new');
                        const picker = document.getElementById('color_hex_picker_new');
                        const preview = document.querySelector('#new-color-preview .no-chip__dot');
                        if (hex && picker && preview) {
                            picker.addEventListener('input', () => {
                                hex.value = picker.value;
                                preview.style.background = picker.value;
                            });
                            hex.addEventListener('input', () => {
                                const v = (hex.value || '').trim();
                                if (/^#([0-9a-fA-F]{6})$/.test(v)) {
                                    picker.value = v;
                                    preview.style.background = v;
                                }
                            });
                        }
                        // 목록 쪽 각 행도 텍스트-피커 동기화
                        document.querySelectorAll('[id^="color_hex_"]').forEach(input => {
                            const id = input.id.replace('color_hex_', '');
                            const pickerEl = document.getElementById('color_picker_' + id);
                            if (!pickerEl) return;
                            pickerEl.addEventListener('input', () => input.value = pickerEl.value);
                            input.addEventListener('input', () => {
                                const v = (input.value || '').trim();
                                if (/^#([0-9a-fA-F]{6})$/.test(v)) pickerEl.value = v;
                            });
                        });
                    })();
                </script>
            </div>
        </div>
    </div>
</div>
<?php end_section() ?>