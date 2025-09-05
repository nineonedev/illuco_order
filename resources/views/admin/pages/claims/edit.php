<?php 

use App\Domains\Claim\Entities\Claim; 
use App\Domains\Communication\Enums\ClaimStatus;// 실제 네임스페이스에 맞게 조정
// use App\Domains\Product\Entities\Product;
// use App\Domains\Customer\Entities\Customer;

?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'claim') ?>
<?php section('action', 'edit') ?>
<?php section('title', '클레임 수정') ?>

<?php section('content') ?>

<div class="no-form-container">
    <div class="no-page-row">
        <div class="no-page-head__between">
            <h1 class="no-heading-sm">클레임 수정</h1>
        </div>

        <form  
            method="post" 
            id="frm" 
            enctype="multipart/form-data" 
            action="<?= route('admin.claims.update', ['id' => $claim->id]) ?>"
        >
            <?= csrf_field() ?>
            <?= put_field() ?>

            <div class="no-form-group">
                <h2 class="no-form-group-title no-body-lg">제품 정보</h2>

                <?php
                    // search 위젯 기본 프롭스
                    $productSearchProps = [
                        'label' => '제품검색',
                        // 선택된 제품 초기값(프론트에서 하이드레이션용)
                        // 프론트 컴포넌트 규격에 맞춰 key명/구조 조정
                        'selected' => $claim->product ? [
                            'id'    => $claim->product->id,
                            'name'  => $claim->product->name ?? '',
                            'model' => $claim->product->model ?? '',
                            'serial_number' => $claim->product->serial_number ?? ''
                        ] : null,
                    ];
                ?>
                <div
                    id="search-product"
                    data-view-props='<?= json_encode($productSearchProps, JSON_UNESCAPED_UNICODE) ?>'
                ></div>

                <span class="no-form-control-space"></span>

                <?php
                    // product zone에 미리 렌더링할 데이터
                    $productZoneProps = $template ? ['template' => $template->toArray()] : [];
                ?>
                <div 
                    id="product-zone"
                    data-view-props='<?= json_encode($productZoneProps, JSON_UNESCAPED_UNICODE) ?>'
                ></div>

                <span class="no-form-control-space"></span>
            </div>

            <div class="no-form-group">
                <h2 class="no-form-group-title no-body-lg">고객 정보</h2>

                <?php
                    $customerSearchProps = [
                        'label' => '고객검색',
                        'selected' => $customer ? [
                            'id'      => $customer->id,
                            'name'    => $customer->name ?? '',
                            'email'   => $customer->email ?? '',
                            'phone'   => $customer->phone ?? '',
                            'company' => $customer->company ?? '',
                        ] : null,
                    ];
                ?>
                <div
                    id="search-customer"
                    data-view-props='<?= json_encode($customerSearchProps, JSON_UNESCAPED_UNICODE) ?>'
                ></div>

                <span class="no-form-control-space"></span>

                <?php
                    $customerZoneProps = [
                        'customer' => $customer ? [
                            'name'    => $customer->name ?? '',
                            'email'   => $customer->email ?? '',
                            'phone'   => $customer->phone ?? '',
                            'company' => $customer->company ?? '',
                            'age' => $customer->age ?? '',
                            'description' => $customer->description ?? '',
                            'address' => $customer->address ?? '',
                            'country' => $customer->country ?? '',
                        ] : null,
                    ];
                ?>
                <div 
                    id="customer-zone"
                    data-view-props='<?= json_encode($customerZoneProps, JSON_UNESCAPED_UNICODE) ?>'
                ></div>

                <span class="no-form-control-space"></span>
            </div>

            <hr class="no-form-hr">
            <span class="no-form-control-space"></span>

            <div class="no-form-group">
                <h2 class="no-form-group-title no-body-lg">문의 내용</h2>

                <!-- 시리얼 번호 -->
                <div class="no-form-control --md">
                    <label for="product_serial_number" class="no-form-control-inner">
                        <input 
                            type="text" 
                            name="product_serial_number" 
                            id="product_serial_number" 
                            class="no-form-control-input" 
                            value="<?= e($claim->product_serial_number ?? '') ?>"
                            placeholder=""
                        >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">시리얼 번호</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 제목 -->
                <div class="no-form-control --md">
                    <label for="title" class="no-form-control-inner">
                        <input 
                            type="text" 
                            name="title" 
                            id="title" 
                            class="no-form-control-input" 
                            value="<?= e($claim->title) ?>" 
                            required
                        >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">제목</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 내용 (에디터) -->
                <?php 
                    $editorProps = [
                        "name"  => "content",
                        "label" => "문의내용",
                        "value" => $claim->content ?? '',
                    ];
                ?>
                <div 
                    data-view-type="editor"
                    data-view-props='<?= json_encode($editorProps, JSON_UNESCAPED_UNICODE) ?>'
                ></div>

                <?php 
                    // 파일 첨부(기존 데이터 유지)
                    $attach_1 = $claim->fileattachment->props('attach_1') ?? '{"file_key":"attach_1"}';
                    $attach_2 = $claim->fileattachment->props('attach_2') ?? '{"file_key":"attach_2"}';
                    $attach_3 = $claim->fileattachment->props('attach_3') ?? '{"file_key":"attach_3"}';
                    $attach_4 = $claim->fileattachment->props('attach_4') ?? '{"file_key":"attach_4"}';
                    $attach_5 = $claim->fileattachment->props('attach_5') ?? '{"file_key":"attach_5"}';
                ?>

                <div data-view-type="file" data-view-props='<?= $attach_1 ?>'></div>
                <div data-view-type="file" data-view-props='<?= $attach_2 ?>'></div>
                <div data-view-type="file" data-view-props='<?= $attach_3 ?>'></div>
                <div data-view-type="file" data-view-props='<?= $attach_4 ?>'></div>
                <div data-view-type="file" data-view-props='<?= $attach_5 ?>'></div>
            </div>

            <!-- 액션 버튼 -->
            <div class="no-form-action">
                <a href="<?= route('admin.claims.index') ?>" data-action="cancel" class="no-btn-primary-outline --sm">
                    <span>취소</span>
                </a>
                <button type="button" class="no-btn-error-outline --sm" data-action="delete">
                    <span>삭제</span>
                </button>
                <button type="submit" class="no-btn-primary --sm">
                    <span>저장</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php end_section() ?>

<?php section('script') ?>
<script>
/**
 * 필요 시 여기서
 * - data-action="delete" 처리 (confirm → DELETE 메서드 요청)
 * - product-zone / customer-zone 하이드레이션
 * - search-product / search-customer 선택시 hidden input 세팅
 * 등을 수행하세요.
 *
 * 예시(선택된 product/customer를 hidden으로 반영):
 */
// (function() {
//   const frm = document.getElementById('frm');
//   const productZone = document.getElementById('product-zone');
//   const customerZone = document.getElementById('customer-zone');

//   function ensureHidden(name, value) {
//     let el = frm.querySelector(`input[name="${name}"]`);
//     if (!el) {
//       el = document.createElement('input');
//       el.type = 'hidden';
//       el.name = name;
//       frm.appendChild(el);
//     }
//     el.value = value ?? '';
//   }

//   // 프론트 search 컴포넌트에서 선택 변경 시 커스텀 이벤트로 알려준다고 가정
//   window.addEventListener('product:selected', (e) => {
//     ensureHidden('product_id', e.detail?.id || '');
//   });
//   window.addEventListener('customer:selected', (e) => {
//     ensureHidden('customer_id', e.detail?.id || '');
//   });

//   // 초기값 주입(있다면)
//   try {
//     const pInit = JSON.parse(productZone?.dataset?.initial || '{}');
//     if (pInit?.product?.id) ensureHidden('product_id', pInit.product.id);

//     const cInit = JSON.parse(customerZone?.dataset?.initial || '{}');
//     if (cInit?.customer?.id) ensureHidden('customer_id', cInit.customer.id);
//   } catch (e) {}
// })();
</script>
<?php end_section() ?>
