<?php extend('layouts.admin'); ?>
<?php section('controller', 'loupeSetting') ?>
<?php section('action', 'index') ?>
<?php section('title', '루페 세팅') ?>

<?php
/**
 * 기대 데이터
 * - $settings : LoupeSetting[] (->template.fileattachment 로드 권장)
 * - $colors   : LoupeFrameColor[] (id, name 등)
 */
?>

<?php section('content') ?>
<div class="loupe-setting">
    <div class="no-page-flex">
        <!-- 생성 폼 -->
        <form id="create-setting" method="post" action="<?= route('admin.loupe-settings.store') ?>" class="loupe-setting__form">
            <?= csrf_field() ?>
            <div class="no-page-row">
                <div class="no-form-divider --lg">
                    <h3 class="no-heading-xs">루페세팅 추가</h3>
                    <p class="no-text-secondary no-mt-4">루페를 선택하고 각 항목의 허용 범위를 입력하세요.</p>
                </div>
                <div class="no-form-group">
                    <!-- 템플릿 선택 -->
                    <div id="template-hook" class="loupe-setting__tpl"></div>
                    <input type="hidden" name="template_id" value="">
                    <span class="no-form-control-space"></span>

                    <!-- WD (cm) -->
                    <div class="loupe-setting__grid">
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="wd_min_new">
                            <input type="number" step="0.1" name="wd_min" id="wd_min_new" class="no-form-control-input" placeholder="">
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">WD 최소 (cm)</legend></fieldset>
                            </label>
                        </div>
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="wd_max_new">
                            <input type="number" step="0.1" name="wd_max" id="wd_max_new" class="no-form-control-input" placeholder="">
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">WD 최대 (cm)</legend></fieldset>
                            </label>
                        </div>
                    </div>

                    <!-- VD (mm) -->
                    <div class="loupe-setting__grid">
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="vd_min_new">
                            <input type="number" step="0.1" name="vd_min" id="vd_min_new" class="no-form-control-input" placeholder="">
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">VD 최소 (mm)</legend></fieldset>
                            </label>
                        </div>
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="vd_max_new">
                            <input type="number" step="0.1" name="vd_max" id="vd_max_new" class="no-form-control-input" placeholder="">
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">VD 최대 (mm)</legend></fieldset>
                            </label>
                        </div>
                    </div>

                    <!-- far PD RIGHT (mm) -->
                    <div class="loupe-setting__grid">
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="fd_right_min_new">
                            <input type="number" step="0.1" name="fd_right_min" id="fd_right_min_new" class="no-form-control-input" placeholder="">
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">far PD RIGHT 최소 (mm)</legend></fieldset>
                            </label>
                        </div>
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="fd_right_max_new">
                            <input type="number" step="0.1" name="fd_right_max" id="fd_right_max_new" class="no-form-control-input" placeholder="">
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">far PD RIGHT 최대 (mm)</legend></fieldset>
                            </label>
                        </div>
                    </div>

                    <!-- far PD LEFT (mm) -->
                    <div class="loupe-setting__grid">
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="fd_left_min_new">
                                <input type="number" step="0.1" name="fd_left_min" id="fd_left_min_new" class="no-form-control-input" placeholder="">
                                <fieldset class="no-form-control-label"><legend class="no-form-control-text">far PD LEFT 최소 (mm)</legend></fieldset>
                            </label>
                        </div>
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="fd_left_max_new">
                            <input type="number" step="0.1" name="fd_left_max" id="fd_left_max_new" class="no-form-control-input" placeholder="">
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">far PD LEFT 최대 (mm)</legend></fieldset>
                            </label>
                        </div>
                    </div>

                    <!-- TOTAL PD (mm) -->
                    <div class="loupe-setting__grid --one">
                        <div class="no-form-control --md">
                            <label class="no-form-control-inner" for="fd_total_new">
                            <input type="number" step="0.1" name="fd_total_distance" id="fd_total_new" class="no-form-control-input" placeholder="">
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">TOTAL PD (mm)</legend></fieldset>
                            </label>
                        </div>
                    </div>

                    <!-- 프레임 컬러(JSON id 배열) -->
                    <div class="no-form-divider --md">
                        <h4 class="no-heading-xxs">프레임 컬러</h4>
                        <p class="no-text-secondary no-mt-2">복수 선택 가능합니다.</p>
                    </div>
                    <!-- <div>
                        <?php if ($colors) :?>
                        <?php
                            $props = [
                                'label' => '프레임 컬러 선택',
                                'name'  => 'frame_colors[]',
                                'value' => '',
                                'options' => array_map(
                                    fn($c) => ['label' => $c['name'], 'value' => $c['id']],
                                    $colors
                                ),
                            ];
                        ?>
                        <div 
                            class="no-form-field"
                            data-view-type="multi-select"
                            data-view-props='<?= e(json_encode($props)) ?>'
                        ></div>
                        <?php endif; ?>
                    </div> -->
                    <div class="loupe-setting__chips">
                    <?php foreach ($colors as $c): ?>
                        <label class="loupe-chip">
                        <input type="checkbox" name="frame_color_ids[]" value="<?= $c['id'] ?>">
                        <span class="loupe-chip__btn">
                            <div class="loupe-chip__color-box" style="background-color: <?= $c['hex'] ?? '' ?>;"></div>
                            <span class="loupe-chip__text"><?= e($c['name'] ?? ('#'.$c['id'])) ?></span>
                        </span>
                        </label>
                    <?php endforeach; ?>
                    </div>
                </div>

                <div class="no-form-action">
                    <button type="submit" class="no-btn-primary --sm"><span>추가</span></button>
                </div>
            </div>
        </form>

        <!-- ===========================
        프레임 컬러 생성 폼
        =========================== -->
        <form id="create-frame-color" method="post" action="<?= route('admin.loupe-frame-colors.store') ?>" class="loupe-setting__form">
            <?= csrf_field() ?>
            <div class="no-page-row">
                <div class="no-form-divider --lg">
                    <h3 class="no-heading-xs">프레임 컬러 추가</h3>
                    <p class="no-text-secondary no-mt-4">루페 프레임 컬러(마스터 데이터)를 등록하세요.</p>
                </div>
                <div class="no-form-group">
                    <div class="no-form-flex --wrap" style="gap:12px;">
                    <!-- 컬러명 -->
                    <div class="no-form-control --md" style="min-width:240px;">
                        <label class="no-form-control-inner" for="color_name_new">
                            <input type="text" name="name" id="color_name_new" class="no-form-control-input" placeholder="예: Matte Black" required>
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">이름</legend></fieldset>
                        </label>
                    </div>

                    <!-- 코드/슬러그 -->
                    <div class="no-form-control --md" style="min-width:220px;">
                        <label class="no-form-control-inner" for="color_code_new">
                            <input type="text" name="code" id="color_code_new" class="no-form-control-input" placeholder="예: matte-black">
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">코드</legend></fieldset>
                        </label>
                    </div>

                    <!-- HEX 컬러 -->
                    <div class="no-form-control --sm" style="min-width:160px;">
                        <label class="no-form-control-inner" for="color_hex_new">
                            <input type="text" name="hex" readonly id="color_hex_new" value="#000000" class="no-form-control-input" placeholder="#000000">
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">HEX</legend></fieldset>
                        </label>
                    </div>
                    <div class="no-form-control --sm" style="width:72px;">
                        <label class="no-form-control-inner" for="color_hex_picker_new">
                            <input type="color" id="color_hex_picker_new" class="no-form-control-input" style="padding:0;height:40px;">
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">컬러 선택</legend></fieldset>
                        </label>
                    </div>
                    <div class="no-form-control --sm" style="min-width:220px;">
                        <label class="no-form-control-inner" for="frame_color_sort_order">
                            <input type="number" step="1" min="1" max="200" name="sort_order" value="1" id="frame_color_sort_order" class="no-form-control-input" placeholder>
                            <fieldset class="no-form-control-label"><legend class="no-form-control-text">순서</legend></fieldset>
                        </label>
                    </div>

                    <!-- 사용 여부 -->
                    <div class="no-form-checkbox --sm" style="align-self:center;margin-top:4px;">
                        <label class="no-form-checkbox-pointer">
                            <input type="checkbox" name="is_active" value="1" class="no-form-checkbox-input" checked>
                            <div class="no-form-checkbox-ripple">
                                <span class="no-form-checkbox-box"><div class="no-form-checkbox-icon"><i class="fa-solid fa-check"></i></div></span>
                            </div>
                            <span class="no-form-checkbox-text">사용</span>
                        </label>
                    </div>
                    </div>
                </div>

                <div class="no-form-action">
                    <button type="submit" class="no-btn-primary --sm"><span>추가</span></button>
                </div>
            </div>
        </form>
    </div>
</div>

<hr class="no-hr --xl">

<h3 class="no-heading-xs no-heading-self">프레임 컬러 목록</h3>
<div class="no-page-index-table-outer">
  <table class="no-page-index-table" id="frame-color-hook">
     <colgroup>
        <col style="width:34%;">
        <col style="width:18%;">
        <col style="width:12%;">
        <col style="width:10%;">
        <col style="width:8%;">
        <col style="width:18%;">
    </colgroup>
    <thead class="center">
        <tr>
        <th>이름</th>
        <th>코드</th>
        <th>HEX</th>
        <th>순서</th>
        <th>사용</th>
        <th>작업</th>
        </tr>
    </thead>
  </table>
</div>

<hr class="no-hr --xl">

<!-- 목록/행별 수정 -->
<div class="no-page-container">
  <h3 class="no-heading-xs no-heading-self">루페세팅 목록</h3>

  <div class="no-page-index-table-outer">
    <table class="no-page-index-table loupe-setting__table" id="loupe-setting-hook">
      <thead class="center">
        <tr>
          <th style="width:22%;">제품</th>
          <th style="width:12%;">WD (cm)</th>
          <th style="width:12%;">VD (mm)</th>
          <th style="width:14%;">far PD RIGHT (mm)</th>
          <th style="width:14%;">far PD LEFT (mm)</th>
          <th style="width:10%;">TOTAL PD</th>
          <th style="width:16%;">프레임 컬러</th>
          <th style="width:8%;">작업</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<?php end_section() ?>

<?php section('script') ?>
<script>
  // HEX 텍스트 <-> 컬러 피커 간단 동기화
  (function(){
    const hex = document.getElementById('color_hex_new');
    const pick = document.getElementById('color_hex_picker_new');
    if (!hex || !pick) return;
    pick.addEventListener('input', () => { hex.value = pick.value; });
    hex.addEventListener('input', () => {
      const v = hex.value?.trim();
      if (/^#([0-9a-fA-F]{6})$/.test(v)) pick.value = v;
    });
  })();
</script>

<?php end_section() ?>