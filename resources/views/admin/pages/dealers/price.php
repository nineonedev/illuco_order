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
        <div class="no-page-head">
            <h1 class="no-heading-sm">대리점 단가</h1>
            <div class="no-text-xs no-text-muted" style="margin-top:6px;">
                <p class="no-body-lg">대리점: <strong><?= e($dealer->name ?? '') ?></strong></p>
                <p>코드: <?= e($dealer->dealer->code ?? '') ?></p>
            </div>
        </div>

        <!-- 신규 단가 추가 -->
        <form id="create-price" method="post" action="<?= route('admin.dealer-price.save', ['id' => $dealer->id]) ?>">
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
<div class="no-page-container">
    <!-- 단가 목록 -->
    <div class="no-page-index-table-outer" style="margin-top:24px;">
        <table class="no-page-index-table">
            <thead class="center">
                <tr>
                    <th style="width:26%;">제품</th>
                    <th style="width:10%;">코드</th>
                    <th style="width:14%;">모델</th>
                    <th style="width:10%;">기본 단가</th>
                    <th style="width:14%;">대리점 단가(USD)</th>
                    <th style="width:8%;">사용</th>
                    <th style="width:10%;">수정일</th>
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
                        <tr>
                            <form method="post" action="<?= route('admin.dealer-price.save', ['id' => $dealer->id]) ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="dealer_price_id" value="<?= e($price->id) ?>">
                                <input type="hidden" name="product_template_id" value="<?= e($tpl->id ?? '') ?>">

                                <td>
                                    <?php if ($tpl): 
                                        $data = json_encode(['label' => $tpl->name]);    
                                    ?>
                                    <button type="button" data-view-type="search-button" data-view-props='<?= $data ?>'></button>
                                    <?php else: ?>
                                        <span class="no-text-secondary">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="no-text-mono"><?= e($tpl->code ?? '-') ?></td>
                                <td class="no-text-mono"><?= e($tpl->model ?? '-') ?></td>

                                <td class="right">
                                    <?= $basePrice !== null ? number_format((float)$basePrice, 2) : '-' ?>
                                </td>

                                <td>
                                    <div class="no-form-control --sm" style="margin:0;">
                                        <label class="no-form-control-inner">
                                            <input type="number" step="0.01" min="0" name="price" class="no-form-control-input"
                                                    value="<?= e((string)($price->price ?? '')) ?>">
                                            <fieldset class="no-form-control-label">
                                                <legend class="no-form-control-text">대리점 단가</legend>
                                            </fieldset>
                                        </label>
                                    </div>
                                </td>

                                <td class="center">
                                    <div class="no-form-checkbox --sm" style="justify-content:center;">
                                        <label class="no-form-checkbox-pointer">
                                            <input type="checkbox" name="is_active" value="1" class="no-form-checkbox-input"
                                                <?= !empty($price->is_active) ? 'checked' : '' ?>>
                                            <div class="no-form-checkbox-ripple">
                                                <span class="no-form-checkbox-box">
                                                    <div class="no-form-checkbox-icon"><i class="fa-solid fa-check"></i></div>
                                                </span>
                                            </div>
                                        </label>
                                    </div>
                                </td>

                                <td class="no-text-xs center">
                                    <?= $price->updated_at ? date('Y-m-d H:i', strtotime($price->updated_at)) : '-' ?>
                                </td>

                                <td class="no-table-action">
                                    <div class="no-page-index-table__action no-prod-attr-list__action">
                                        <button type="submit" class="no-btn-primary-outline">
                                            <span>저장</span>
                                        </button>

                                        <!-- 삭제는 같은 tr 폼에서 별도 액션으로 보냄 -->
                                        <button
                                            type="submit"
                                            class="no-btn-error-outline"
                                            formaction="<?= route('admin.dealer-price.destroy', ['id' => $price->id]) ?>"
                                            formmethod="post"
                                            data-tooltip
                                            data-item-action="destroy"
                                            data-confirm="정말 삭제하시겠습니까?"
                                        >
                                            <span>삭제</span>
                                        </button>
                                    </div>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php end_section() ?>
