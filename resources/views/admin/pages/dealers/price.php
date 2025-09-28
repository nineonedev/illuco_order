<?php extend('layouts.admin'); ?>
<?php section('controller', 'dealerPrice') ?>
<?php section('action', 'edit') ?>
<?php section('title', '대리점 단가') ?>

<?php
/**
 * 기대 데이터
 * - $dealer  : \App\Domains\User\Entities\User (->dealer 사용)
 * - $prices  : DealerPrice[] (template 관계 로드됨)
 * - $templates : ProductTemplate[] (추가용 셀렉트)
 */
?>

<?php section('content') ?>
<div class="no-form-container">
    <div class="no-page-row">
        <div class="dealer-price-header" data-component="dealer-price-header">
            <div class="dealer-price-header__title">
                <h1 class="dealer-price-header__h1">대리점 단가</h1>
            </div>

            <dl class="dealer-price-header__meta">
                <div class="dealer-price-header__item">
                <dt class="dealer-price-header__label">대리점</dt>
                <dd class="dealer-price-header__value"><?= e($dealer->name ?? '') ?></dd>
                </div>

                <div class="dealer-price-header__item">
                <dt class="dealer-price-header__label">코드</dt>
                <dd class="dealer-price-header__value"><?= e($dealer->dealer->code ?? '') ?></dd>
                </div>
            </dl>
        </div>


        <!-- 신규 단가 추가 -->
        <form id="create-price" method="post" action="<?= route('admin.dealer-price.store', ['id' => $dealer->id]) ?>">
            <?= csrf_field() ?>
            <div class="no-form-group">
                <div class="no-form-divider --lg">
                    <h3 class="no-heading-xs">단가 추가</h3>
                    <p class="no-text-secondary no-mt-4">제품 템플릿을 선택하고 대리점 전용 단가를 입력하세요.</p>
                </div>
                <div class="no-form-control-space"></div>
                
                <div id="template-hook"></div>
                <input type="hidden" name="product_template_id" value="">
                <span class="no-form-control-space"></span>

                <div class="no-form-control --md">
                    <label for="price_new" class="no-form-control-inner">
                        <input type="number" step="0.01" min="0" name="price" id="price_new" class="no-form-control-input" placeholder="예: 199.99" value="00.00">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">대리점 단가 (USD)</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-checkbox --sm" style="margin-top:2px;">
                    <label class="no-form-checkbox-pointer">
                        <input type="checkbox" name="is_active" value="1" class="no-form-checkbox-input" checked>
                        <div class="no-form-checkbox-ripple">
                            <span class="no-form-checkbox-box"><div class="no-form-checkbox-icon"><i class="fa-solid fa-check"></i></div></span>
                        </div>
                        <span class="no-form-checkbox-text">사용</span>
                    </label>
                </div>
            </div>

            <div class="no-form-action">
                <a href="<?= route('admin.dealers.index') ?>" class="no-btn-primary-outline --sm" data-action="cancel">
                    <span>취소</span>
                </a>
                <button type="submit" class="no-btn-primary --sm">
                    <span>추가</span>
                </button>
            </div>
        </form>
    </div>
</div>

<hr class="no-hr --xl">

<div class="no-page-container">
    <!-- 단가 목록 -->
    <h3 class="no-heading-xs no-heading-self">단가 목록</h3>

    <div class="no-page-index-table-outer">
        <table class="no-page-index-table">
            <thead class="center">
                <tr>
                    <th style="width:26%;">제품</th>
                    <!-- <th style="width:10%;">코드</th>
                    <th style="width:14%;">모델</th> -->
                    <th style="width:10%;">기본 단가(USD)</th>
                    <th style="width:14%;">대리점 단가(USD)</th>
                    <th style="width:8%;">사용</th>
                    <!-- <th style="width:10%;">수정일</th> -->
                    <th style="width:8%;">작업</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($prices)) : ?>
                    <tr>
                        <td colspan="8" class="center no-text-secondary">등록된 단가가 없습니다.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($prices as $price): ?>
                        <?php
                            $tpl = $price->template ?? null;
                            $basePrice = $tpl ? $tpl->price : null;
                        ?>
                        <tr data-row-id="<?= $price->id ?>"> <!-- (JS용 표식도 추가 권장) -->
                            <td>
                                <!-- <?php if ($tpl): $data = json_encode(['label' => $tpl->name]); ?>
                                <button type="button"
                                        data-view-type="search-button"
                                        data-view-props='<?= $data ?>'
                                        data-template-id="<?= $tpl->id ?>"
                                        data-price-form="<?= $price->id ?>"></button>
                                <?php else: ?><span class="no-text-secondary">-</span><?php endif; ?> -->
                                <div class="no-img-label">
                                    <div class="no-img-label__image">
                                        <img src="<?= $tpl->fileattachment[0]->upload_path ?>" alt="">
                                    </div>
                                    <span class="no-img-label__label"><?= $tpl->name ?></span>
                                </div>
                            </td>

                            <td class="right">
                                <?= $basePrice !== null ? number_format((float)$basePrice, 2) : '-' ?>
                            </td>

                            <td>
                                <div class="no-form-control --sm" style="margin:0;">
                                    <label class="no-form-control-inner">
                                        <input type="number" step="0.01" min="0" name="price"
                                            class="no-form-control-input"
                                            value="<?= e((string)($price->price ?? '')) ?>"
                                            form="f-<?= $price->id ?>">
                                        <fieldset class="no-form-control-label">
                                            <legend class="no-form-control-text">대리점 단가</legend>
                                        </fieldset>
                                    </label>
                                </div>
                            </td>

                            <td class="center">
                                <div class="no-form-checkbox --sm" style="justify-content:center;">
                                    <label class="no-form-checkbox-pointer">
                                        <input 
                                            type="checkbox" 
                                            name="is_active" 
                                            value="1"
                                            class="no-form-checkbox-input" 
                                            <?= $price->is_active == 1 ? 'checked' : '' ?> 
                                        >
                                        <div class="no-form-checkbox-ripple">
                                            <span class="no-form-checkbox-box">
                                                <div class="no-form-checkbox-icon"><i class="fa-solid fa-check"></i></div>
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            </td>

                            <td class="no-table-action">
                                <div class="no-page-index-table__action no-prod-attr-list__action">
                                    <button 
                                        type="button" 
                                        class="no-btn-primary-outline" 
                                        data-row-for="<?= $price->id ?>"
                                        data-action="update"
                                    >
                                        <span>저장</span>
                                    </button>
                                    <button 
                                        type="button"
                                        class="no-btn-error-outline"
                                        data-row-for="<?= $price->id ?>"
                                        data-action="destroy"
                                        data-confirm="정말 삭제하시겠습니까?">
                                        <span>삭제</span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php end_section() ?>
