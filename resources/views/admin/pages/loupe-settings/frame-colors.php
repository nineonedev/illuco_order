<?php
/**
 * View: resources/views/admin/pages/loupe-settings/frame-colors.php
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
    <!-- ===== 왼쪽: 카드 리스트 (신규 네임스페이스 lc-*) ===== -->
    <div class="no-cart-area">
      <div class="no-page-row">
        <div class="no-page-head">
          <h1 class="no-heading-sm">프레임 컬러</h1>
          <p class="lc-desc">카드에서 바로 수정/삭제할 수 있습니다.</p>
        </div>

        <?php if (!empty($colors)) : ?>
          <ol class="lc-cards" id="frame-color-list">
            <?php foreach ($colors as $color): ?>
              <li class="lc-card" data-id="<?= (int)$color->id ?>" style="--chip: <?= e($color->hex ?: '#999999') ?>;">
                <form action="<?= route('admin.loupe_frame_colors.update', ['id' => $color->id]) ?>" method="post" class="lc-card__form">
                  <?= csrf_field() ?>
                  <?= put_field() ?>

                  <div class="lc-card__inner">
                    <!-- 헤더 -->
                    <div class="lc-card__header">
                      <div class="lc-chip" title="미리보기"></div>

                      <div class="lc-title">
                        <strong class="lc-name"><?= e($color->name ?: ('#'.$color->id)) ?></strong>
                        <span class="lc-meta">ID #<?= (int)$color->id ?></span>
                        <input type="hidden" name="id" value="<?=$color->id?>">
                      </div>

                      <div class="lc-status">
                        <label class="lc-switch">
                          <input type="checkbox"
                                 id="color_active_<?= $color->id ?>"
                                 name="is_active" value="1"
                                 <?= !empty($color->is_active) ? 'checked' : '' ?>>
                          <span class="lc-switch__slider" aria-hidden="true"></span>
                          <span class="lc-switch__label">활성</span>
                        </label>
                      </div>
                    </div>

                    <!-- 바디 -->
                    <div class="lc-card__body">
                      <div class="lc-grid">
                        <!-- 이름 -->
                        <div class="lc-field">
                          <label for="color_name_<?= $color->id ?>" class="lc-field__wrap">
                            <input type="text"
                                   name="name"
                                   id="color_name_<?= $color->id ?>"
                                   value="<?= e($color->name) ?>"
                                   class="lc-input"
                                   placeholder="예) Classic Coffee"
                                   required>
                            <span class="lc-label">이름</span>
                          </label>
                        </div>

                        <!-- [ADD] CODE -->
                        <div class="lc-field">
                            <label for="color_code_<?= $color->id ?>" class="lc-field__wrap">
                                <input type="text"
                                    name="code"
                                    id="color_code_<?= $color->id ?>"
                                    value="<?= e($color->code ?? '') ?>"
                                    class="lc-input"
                                    disabled
                                    placeholder="예) CLASSIC-BLK">
                                <span class="lc-label">코드</span>
                            </label>
                        </div>

                        <!-- HEX -->
                        <div class="lc-field">
                          <label for="color_hex_<?= $color->id ?>" class="lc-field__wrap">
                            <input type="text"
                                   name="hex"
                                   id="color_hex_<?= $color->id ?>"
                                   value="<?= e($color->hex) ?>"
                                   class="lc-input"
                                   placeholder="#FFFFFF"
                                   pattern="^#([0-9a-fA-F]{6})$"
                                   title="#RRGGBB 형식으로 입력">
                            <span class="lc-label">HEX</span>
                          </label>
                        </div>

                        <!-- 피커 -->
                        <div class="lc-field">
                          <label for="color_picker_<?= $color->id ?>" class="lc-field__wrap">
                            <input type="color"
                                   id="color_picker_<?= $color->id ?>"
                                   value="<?= e($color->hex ?: '#999999') ?>"
                                   class="lc-input lc-input--color">
                            <span class="lc-label">컬러 피커</span>
                          </label>
                        </div>

                        <!-- 정렬 -->
                        <div class="lc-field">
                          <label for="color_order_<?= $color->id ?>" class="lc-field__wrap">
                            <input type="number"
                                   name="sort_order"
                                   id="color_order_<?= $color->id ?>"
                                   value="<?= (int)$color->sort_order ?>"
                                   class="lc-input"
                                   placeholder="0" inputmode="numeric">
                            <span class="lc-label">정렬 순서</span>
                          </label>
                        </div>
                      </div>
                    </div>

                    <!-- 액션 -->
                    <div class="lc-actions">
                      <button type="submit" class="lc-btn lc-btn--primary lc-btn--xs"><span>저장</span></button>
                      <a href="<?= route('admin.loupe_frame_colors.destroy', ['id' => $color->id]) ?>"
                         data-item-action="delete"
                         class="lc-btn lc-btn--danger-ol lc-btn--xs"><span>삭제</span></a>
                    </div>
                  </div>
                </form>
              </li>
            <?php endforeach; ?>
          </ol>
        <?php else: ?>
          <div class="lc-empty">
            <p>등록된 프레임 컬러가 없습니다.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- ===== 오른쪽: 추가 폼 (기존 구조 유지 가능) ===== -->
    <div class="no-cart-area">
      <div class="no-page-row">
        <div class="no-page-head">
          <h1 class="no-heading-sm">프레임 컬러 추가</h1>
          <p class="no-text-secondary">새 컬러를 등록합니다.</p>
        </div>

        <form action="<?= route('admin.loupe_frame_colors.store') ?>" method="post" id="create-frame-color">
          <?= csrf_field() ?>

          <div class="no-form-group --md">
            <div class="no-form-control --md">
              <label for="color_name_new" class="no-form-control-inner">
                <input type="text" name="name" id="color_name_new" class="no-form-control-input" placeholder="예) Classic Black" required>
                <fieldset class="no-form-control-label">
                  <legend class="no-form-control-text">이름</legend>
                </fieldset>
              </label>
            </div>

            <!-- [ADD] CODE -->
            <div class="no-form-control --md">
                <label for="color_code_new" class="no-form-control-inner">
                    <input type="text"
                        name="code"
                        id="color_code_new"
                        class="no-form-control-input"
                        placeholder="예) CLASSIC-BLK"
                        required>
                    <fieldset class="no-form-control-label">
                    <legend class="no-form-control-text">코드</legend>
                    </fieldset>
                </label>
            </div>


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
                <div class="lc-status">
                    <label class="lc-switch" for="color_active_new">
                        <input type="checkbox"
                                id="color_active_new"
                                name="is_active" value="1" checked>
                        <span class="lc-switch__slider" aria-hidden="true"></span>
                        <span class="lc-switch__label">활성</span>
                    </label>
                </div>
              </div>
            </div>

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
            <a href="<?= route('admin.loupe_settings.index') ?>" class="no-btn-primary-outline --sm"><span>취소</span></a>
            <button type="submit" class="no-btn-primary --sm"><span>추가</span></button>
          </div>
        </form>

        <script>
            (function () {
            const isHex = (v) => /^#([0-9a-fA-F]{6})$/.test((v || '').trim());

            // ====== 우측 "추가" 폼 ======
            const nameNew    = document.getElementById('color_name_new');
            const codeNew    = document.getElementById('color_code_new'); // [ADD]
            const hexNew     = document.getElementById('color_hex_new');
            const pickerNew  = document.getElementById('color_hex_picker_new');
            const previewDot = document.querySelector('#new-color-preview .no-chip__dot');
            const previewTxt = document.querySelector('#new-color-preview .no-chip__text');

            // [ADD] code normalize: 모든 공백→'_' , 연속 '_' 축약, 앞/뒤 '_' 제거
            const normalizeCode = (v) => {
                let s = String(v ?? '').replace(/\s+/g, '_');
                s = s.replace(/_+/g, '_');
                s = s.replace(/^_+|_+$/g, '');
                return s;
            };

            // [ADD] 입력/붙여넣기/스페이스 방지
            if (codeNew) {
                // 실시간 정규화
                const applyNormalize = () => {
                const norm = normalizeCode(codeNew.value);
                if (codeNew.value !== norm) codeNew.value = norm;
                };
                codeNew.addEventListener('input', applyNormalize);
                codeNew.addEventListener('blur', applyNormalize);

                // 스페이스 키 → '_' 로 치환
                codeNew.addEventListener('keydown', (e) => {
                if (e.key === ' ') {
                    e.preventDefault();
                    const { selectionStart, selectionEnd, value } = codeNew;
                    const before = value.slice(0, selectionStart);
                    const after  = value.slice(selectionEnd);
                    codeNew.value = before + '_' + after;
                    const pos = before.length + 1;
                    codeNew.setSelectionRange(pos, pos);
                }
                });

                // 붙여넣기 시 정규화
                codeNew.addEventListener('paste', (e) => {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text');
                const norm = normalizeCode(text);
                document.execCommand
                    ? document.execCommand('insertText', false, norm)
                    : (codeNew.value += norm);
                });
            }

            // ====== (이하 기존 동기화 로직: 색상, 미리보기 텍스트 등) ======
            const setPreviewColor = (v) => { if (previewDot) previewDot.style.background = v; };
            const setPreviewText  = () => {
                const t = (nameNew?.value || '').trim();
                if (previewTxt) previewTxt.textContent = t ? t : '미리보기';
            };

            if (pickerNew) setPreviewColor(pickerNew.value || '#999999');
            setPreviewText();

            if (pickerNew && hexNew) {
                pickerNew.addEventListener('input', () => {
                hexNew.value = pickerNew.value;
                setPreviewColor(pickerNew.value);
                });
            }

            if (hexNew && pickerNew) {
                hexNew.addEventListener('input', () => {
                const v = (hexNew.value || '').trim();
                if (isHex(v)) {
                    pickerNew.value = v;
                    setPreviewColor(v);
                }
                });
            }

            if (nameNew) {
                nameNew.addEventListener('input', setPreviewText);
            }

            // ====== 좌측 카드(HEX<->피커, 이름->헤더) ======
            document.querySelectorAll('.lc-card').forEach((card) => {
                const hex = card.querySelector('[id^="color_hex_"]');
                const picker = card.querySelector('[id^="color_picker_"]');
                const setChip = (v) => card.style.setProperty('--chip', v);

                if (picker && picker.value) setChip(picker.value);

                if (picker && hex) {
                picker.addEventListener('input', () => {
                    hex.value = picker.value;
                    setChip(picker.value);
                });
                }
                if (hex && picker) {
                hex.addEventListener('input', () => {
                    const v = (hex.value || '').trim();
                    if (isHex(v)) {
                    picker.value = v;
                    setChip(v);
                    }
                });
                }

                const nameInput = card.querySelector('input[name="name"]');
                const nameLabel = card.querySelector('.lc-name');
                if (nameInput && nameLabel) {
                nameInput.addEventListener('input', () => {
                    const t = (nameInput.value || '').trim();
                    nameLabel.textContent = t || nameLabel.textContent;
                });
                }
            });
            })();
            </script>


      </div>
    </div>
  </div>
</div>
<?php end_section() ?>
