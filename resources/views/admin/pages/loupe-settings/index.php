<?php extend('layouts.admin'); ?>
<?php section('controller', 'loupeSetting') ?>
<?php section('action', 'index') ?>
<?php section('title', '루페 세팅') ?>

<?php
/**
 * 기대 데이터
 * - $settings : \App\Domains\Loupe\Entities\LoupeSetting[] (->template.fileattachment 로드 권장)
 * - $colors   : \App\Domains\Loupe\Entities\LoupeFrameColor[] (id, name, hex, is_active, sort_order)
 * - $paginator: LengthAwarePaginator (선택)
 *
 * 요청 파라미터 (GET)
 * - name: 제품명 검색 (template.name)
 * - code: 제품 코드 검색 (template.code)
 * - model: 제품 모델 검색 (template.model)
 * - color_id: 프레임 컬러 필터
 * - sort: 정렬키 (name_asc|name_desc|created_at_asc|created_at_desc|sort_order_asc|sort_order_desc)
 */
?>

<?php section('content') ?>
<div class="no-page-container loupe-setting-index">
<form method="get" action="<?= route('admin.loupe_settings.index') ?>">
    <div class="no-page-row">
    <div class="no-page-head">
        <h1 class="no-heading-sm">루페 세팅</h1>
        <p class="no-text-secondary">루페 제품의 세팅 범위 및 프레임 컬러를 관리합니다.</p>
    </div>

    <!-- ===== 필터 ===== -->
    <div class="no-page-index-filter">
        <div class="no-page-index-filter__form">
        <!-- 제품명 -->
        <div class="no-form-search">
            <label for="name" class="no-form-label">제품명</label>
            <div class="no-form-search-inner">
            <div class="no-form-search__icon"><i class="fa-light fa-magnifying-glass"></i></div>
            <input
                type="search"
                name="name"
                id="name"
                class="no-form-search-input"
                placeholder="제품명 검색"
                value="<?= e(request()->query('name', '')) ?>"
            >
            </div>
        </div>

        <!-- 코드 -->
        <div class="no-form-search">
            <label for="code" class="no-form-label">코드</label>
            <div class="no-form-search-inner">
            <div class="no-form-search__icon"><i class="fa-light fa-magnifying-glass"></i></div>
            <input
                type="search"
                name="code"
                id="code"
                class="no-form-search-input"
                placeholder="코드 검색"
                value="<?= e(request()->query('code', '')) ?>"
            >
            </div>
        </div>

        <!-- 모델 -->
        <div class="no-form-search">
            <label for="model" class="no-form-label">모델</label>
            <div class="no-form-search-inner">
            <div class="no-form-search__icon"><i class="fa-light fa-magnifying-glass"></i></div>
            <input
                type="search"
                name="model"
                id="model"
                class="no-form-search-input"
                placeholder="모델 검색"
                value="<?= e(request()->query('model', '')) ?>"
            >
            </div>
        </div>

        <!-- 프레임 컬러 -->
        <?php
            $colorOptions = [['label' => '전체', 'value' => '']];
            foreach ($colors as $c) {
            $colorOptions[] = ['label' => $c->name, 'value' => (string)$c->id];
            }
            $colorProps = [
            'spacing' => false,
            'label'   => '프레임 컬러',
            'name'    => 'color_id',
            'value'   => request()->query('color_id', ''),
            'options' => $colorOptions,
            ];
        ?>
        <div
            class="no-form-field"
            data-view-type="select"
            data-view-props='<?= e(json_encode($colorProps)) ?>'
        ></div>

        <!-- 정렬 -->
        <?php
            $sortProps = [
            'spacing' => false,
            'label' => '정렬',
            'name'  => 'sort',
            'value' => request()->query('sort', ''),
            'options' => [
                ['label' => '전체', 'value' => ''],
                ['label' => '이름 ↑', 'value' => 'name_asc'],
                ['label' => '이름 ↓', 'value' => 'name_desc'],
                ['label' => '등록일 ↑', 'value' => 'created_at_asc'],
                ['label' => '등록일 ↓', 'value' => 'created_at_desc'],
                ['label' => '정렬순서 ↑', 'value' => 'sort_order_asc'],
                ['label' => '정렬순서 ↓', 'value' => 'sort_order_desc'],
            ],
            ];
        ?>
            <div
                class="no-form-field"
                data-view-type="select"
                data-view-props='<?= e(json_encode($sortProps)) ?>'
            ></div>

            <!-- 검색 버튼 -->
            <div class="no-page-index-link">
                <button type="submit" class="no-btn-primary --sm">검색</button>
            </div>
        </div>

        <!-- 우측 링크 -->
        <div class="no-page-split">
            <div class="no-page-split__block">
                <a href="<?= route('admin.loupe_settings.create') ?>" class="no-btn-primary --sm">
                    <i class="fa-light fa-circle-plus"></i><span>루페 세팅 추가</span>
                </a>
                <a href="<?= route('admin.loupe_frame_colors.index') ?>" class="no-btn-premium-outline --sm" style="margin-left:8px;">
                    <i class="fa-light fa-palette"></i><span>루페 프레임 컬러 관리</span>
                </a>
            </div>

            <div class="no-page-split__block">
                <a href="<?= route('admin.loupe_settings.index') ?>" class="no-btn-success --sm"><span>필터 초기화</span></a>
                <?php if (can('product.delete')): ?>
                <button id="select-delete-btn" class="no-btn-error --sm" type="button" disabled>
                    <span>선택삭제</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ===== 목록 테이블 ===== -->
    <div class="no-page-index-table-outer">
        <table class="no-page-index-table">
        <thead class="center">
            <tr>
                <?php if (can('product.delete')): ?>
                <th class="no-table-check">
                    <div class="no-form-checkbox --xs">
                        <label for="chk-all" class="no-form-checkbox-pointer">
                            <input type="checkbox" id="chk-all" class="no-form-checkbox-input">
                            <div class="no-form-checkbox-ripple">
                                <span class="no-form-checkbox-box">
                                    <div class="no-form-checkbox-icon"><i class="fa-solid fa-check"></i></div>
                                </span>
                            </div>
                        </label>
                    </div>
                </th>
                <?php endif; ?>
                <th style="width:20%;">제품</th>
                <th style="width:11%;">WD (cm)</th>
                <th style="width:11%;">VD (mm)</th>
                <th style="width:12%;">far PD RIGHT (mm)</th>
                <th style="width:12%;">far PD LEFT (mm)</th>
                <th style="width:10%;">TOTAL PD</th>
                <th style="width:16%;">프레임 컬러</th>
                <th style="width:8%;">작업</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($settings)): ?>
            <?php foreach ($settings->items() as $s): ?>
                <tr class="no-table-hover">
                    <?php if (can('product.delete')): ?>
                    <td class="no-table-check">
                        <div class="no-form-checkbox --xs">
                            <label for="setting<?= $s->id ?>" class="no-form-checkbox-pointer">
                                <input type="checkbox" name="checked_ids[]" id="setting<?= $s->id ?>" value="<?= $s->id ?>" class="no-form-checkbox-input">
                                <div class="no-form-checkbox-ripple">
                                    <span class="no-form-checkbox-box">
                                        <div class="no-form-checkbox-icon"><i class="fa-solid fa-check"></i></div>
                                    </span>
                                </div>
                            </label>
                        </div>
                    </td>
                    <?php endif; ?>

                    <!-- 제품 -->
                    <td>
                        <div class="no-flex --middle --col-lg" style="gap:10px;">
                        <?php if (!empty($s->template->fileattachment[0]->upload_path)): ?>
                            <img
                            src="<?= e($s->template->fileattachment[0]->upload_path) ?>"
                            alt="<?= e($s->template->name ?? '') ?>"
                            style="width:60px;height:auto;border-radius:6px;"
                            >
                        <?php endif; ?>
                        <div>
                            <div class="no-text-sm"><strong><?= e($s->template->name ?? '-') ?></strong></div>
                            <div class="no-text-xs no-text-muted">
                            <?= e($s->template->model ?? '-') ?> · <?= e($s->template->code ?? '-') ?>
                            </div>
                        </div>
                        </div>
                    </td>

                    <!-- WD -->
                    <td class="center">
                        <?php
                        $wdMin = is_numeric($s->wd_min) ? (float)$s->wd_min : null;
                        $wdMax = is_numeric($s->wd_max) ? (float)$s->wd_max : null;
                        ?>
                        <?= $wdMin !== null && $wdMax !== null ? e(number_format($wdMin, 1)).' ~ '.e(number_format($wdMax, 1)) : '-' ?>
                    </td>

                    <!-- VD -->
                    <td class="center">
                        <?php
                        $vdMin = is_numeric($s->vd_min) ? (float)$s->vd_min : null;
                        $vdMax = is_numeric($s->vd_max) ? (float)$s->vd_max : null;
                        ?>
                        <?= $vdMin !== null && $vdMax !== null ? e(number_format($vdMin, 1)).' ~ '.e(number_format($vdMax, 1)) : '-' ?>
                    </td>

                    <!-- far PD RIGHT -->
                    <td class="center">
                        <?php
                        $frMin = is_numeric($s->pd_right_min) ? (float)$s->pd_right_min : null;
                        $frMax = is_numeric($s->pd_right_max) ? (float)$s->pd_right_max : null;
                        ?>
                        <?= $frMin !== null && $frMax !== null ? e(number_format($frMin, 1)).' ~ '.e(number_format($frMax, 1)) : '-' ?>
                    </td>

                    <!-- far PD LEFT -->
                    <td class="center">
                        <?php
                        $flMin = is_numeric($s->pd_left_min) ? (float)$s->pd_left_min : null;
                        $flMax = is_numeric($s->pd_left_max) ? (float)$s->pd_left_max : null;
                        ?>
                        <?= $flMin !== null && $flMax !== null ? e(number_format($flMin, 1)).' ~ '.e(number_format($flMax, 1)) : '-' ?>
                    </td>

                    <!-- TOTAL PD -->
                    <td class="center">
                        <?= is_numeric($s->pd_total_distance) ? e(number_format((float)$s->pd_total_distance, 1)) : '-' ?>
                    </td>

                    <!-- 프레임 컬러 -->
                    <td>
                        <div class="no-flex --wrap" style="gap:6px;">
                        <?php
                            $frameColors = $s->frameColors ?? [];
                            // frame_color가 JSON(배열)이라고 가정: [{id, name, hex}] or [id...]
                            foreach ($frameColors as $fc) {
                            $name = $fc->name ?? '';
                            $hex  = $fc->hex ?? '#999999';
                        ?>
                            <span class="no-chip --xs">
                                <span class="no-chip__dot" style="background:<?= e($hex) ?>;"></span>
                                <span class="no-chip__text"><?= e($name) ?></span>
                            </span>
                        <?php } ?>
                        <?php if (empty($frameColors)): ?>
                            <span class="no-text-muted">-</span>
                        <?php endif; ?>
                        </div>
                    </td>
                    <td class="no-table-action">
                        <div class="no-page-index-table__action">
                            <a href="<?= route('admin.loupe_settings.edit', ['id' => $s->id]) ?>" class="no-btn-action" data-tooltip>
                                <div class="no-btn-action-ripple">
                                    <i class="fa-light fa-pen-to-square"></i>
                                    <span data-tooltip-text><span>수정</span><span data-tooltip-arrow></span></span>
                                </div>
                            </a>
                            <?php if (can('product.delete')): ?>
                            <button 
                                data-action="<?= route('admin.loupe_settings.destroy', ['id' => $s->id]) ?>" 
                                type="button" 
                                data-method="delete" 
                                class="no-btn-action" 
                                data-confirm="정말 삭제하시겠습니까?"
                            >
                                <div class="no-btn-action-ripple">
                                    <i class="fa-light fa-trash-can"></i>
                                    <span data-tooltip-text><span>삭제</span><span data-tooltip-arrow></span></span>
                                </div>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="7" class="center no-text-secondary">등록된 루페 세팅이 없습니다.</td>
            </tr>
            <?php endif; ?>
        </tbody>
        </table>
    </div>

    <?php if (isset($settings)): ?>
        <?= include_view('admin.components.pagination', ['paginator' => $settings]) ?>
    <?php endif; ?>
    </div>
</form>
</div>
<?php end_section() ?>

<?php section('script') ?>
<script>
// (선택) 컬러 프리뷰가 필요한 경우 향후 확장용 훅만 유지
(function(){
    // 여기에 index 전용 상호작용 스크립트를 추가할 수 있습니다.
})();
</script>
<?php end_section() ?>
