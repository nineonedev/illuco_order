<?php

use App\Domains\Product\Entities\Headlight;
use App\Domains\Product\Entities\Loupe;
/**
 * View: admin/pages/orders/edit-original.php
 *
 * 기대 데이터
 * - $order       : Order (relations: customer, user.dealer?, items[].product.template.fileattachment)
 * - $dealers     : User[] (->dealer 로드됨)  // 대리점 선택 모달에서 사용
 * - $dealerMemo  : DealerMemo|null          // 컨트롤러에서 함께 전달 권장
 * - $query       : array
 */
?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'orderOriginal') ?>
<?php section('action', 'index') ?>
<?php section('title', '기존오더 수정') ?>

<?php section('content') ?>
<div class="no-page-container order-edit-original">
  <div class="no-page-row">
    <div class="no-page-head">
      <div class="no-page-head__title">
        <h1 class="no-heading-sm">기존오더 수정 <span class="no-text-muted">(#<?= e($order->order_no) ?>)</span></h1>
        <div class="no-text-xs no-mt-6">
          <span>주문일: <?= e(substr($order->created_at ?? '', 0, 10)) ?></span>
        </div>
      </div>

      <div class="no-head-information">
        <div class="no-head-information__block">
            <strong class="no-head-information__title">
                주문자 정보 
                <!-- <small class="no-text-xs">from <?= $order->user->isDealer() ? '대리점('.$order->user->dealer->code.')' : '일루코'?></small> -->
            </strong>
            <div class="no-head-information__comp">
                <dl>
                    <dt>이름</dt>
                    <dd><?= $order->user->name ?></dd>
                </dl>
                <dl>
                    <dt>이메일</dt>
                    <dd><?= $order->user->email ?></dd>
                </dl>
                <dl>
                    <dt>연락처</dt>
                    <dd><?= $order->user->phone ?></dd>
                </dl>
                <?php if ($order->user->isDealer()) : 
                    $orderDealer = $order->user->dealer; 
                ?>
                <dl class="no-head-information__comp">
                    <dt>대리점 국가</dt>
                    <dd><?= __('countries.'.$orderDealer->country) ?></dd>
                </dl>
                <dl>
                    <dt>대리점 코드</dt>
                    <dd><?= $orderDealer->code ?></dd>
                </dl>
                <dl>
                    <dt>대리점 주소</dt>
                    <dd><?= $orderDealer->address ?></dd>
                </dl>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="no-head-information__block">
            <strong class="no-head-information__title">고객 정보</strong>
            <div class="no-head-information__comp">
                <dl>
                    <dt>고객명</dt>
                    <dd><?= $order->customer->name ?></dd>
                </dl>
                <dl>
                    <dt>이메일</dt>
                    <dd><?= $order->customer->email?></dd>
                </dl>
                <dl>
                    <dt>연락처</dt>
                    <dd><?= $order->customer->phone ?></dd>
                </dl>
            </div>
        </div>
      </div>

      <?php if ($dealerMemo) : ?>
      <div class="no-head-information__call">
        <strong class="no-head-information__call-title">오더 메모</strong>
        <div class="no-head-information__content">
            <?= $dealerMemo->memo_general ?>
        </div>
      </div>
      <?php endif; ?>

      <div class="no-form-action">
        <a href="<?= route('admin.orders.edit', ['orderNo' => $order->order_no]) ?>" class="no-btn-primary-outline">
            <span>돌아가기</span>
        </a>
      </div>
    </div>

    <hr class="no-hr --xl">

    <div class="no-base-wrapper">
        <section class="no-base-section">
            <!-- 저장 폼 -->
            <form 
                id="order-original-form"
                method="post"
                action="<?= route('admin.orderitems.store') ?>"
            >
                <input type="hidden" name="order_id" value="<?= $order->id ?>">
                <?= csrf_field() ?> 
        
                <div class="no-order-hooks">
                    <div id="template-hook"></div>
                    <!-- <div id="customer-hook"></div> -->
                    <!-- <div id="dealer-hook"></div> -->
                </div>
            </form>
        </section>

        <hr class="no-hr">

        <section class="no-base-section">
            <!-- 3. 상품 복원 카드 -->
            <div >
                <div class="no-flex-between no-heading-margin">
                    <h2 class="no-order-restore__title">주문 제품 목록</h2>
                </div>

                <div class="no-page-index-table-outer">
                    <table class="no-page-index-table">
                        <thead>
                            <tr>
                                <th>이미지</th>
                                <th>제품명</th>
                                <th>모델명</th>
                                <th>가격</th>
                                <th>시리얼번호</th>
                                <th>옵션</th>
                                <th>수량</th>
                                <th>관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order->items as $item): 
                                $product = $item->product;
                                $product->load(['serials']);
                                $type = $item->product->type;
                                $sub = $type ? $item->product->{$type} : null;
                                $serials = $product->serials 
                                    ? implode(', <br>', array_map(fn($s) => $s->serial_number, $product->serials))
                                    : '-';
                            ?>
                            <tr>
                                <td style="width: 10rem">
                                    <?php if ($img = $product->template->fileattachment[0] ?? null): ?>
                                        <img src="<?= e($img->upload_path) ?>" alt="제품 이미지" style="width: 100%; min-width: 8rem; border-radius: .4rem;">
                                    <?php else: ?>
                                        <div class="no-order-restore__placeholder">No Image</div>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($product->name) ?></td>
                                <td><?= e($product->model) ?></td>
                                <td><?= '$'. e($product->price) ?></td>
                                <td><?= $serials ?></td>
                                <td>
                                    <div class="no-order-options">
                                        <?php if ($sub) : ?>
                                            <?php
                                            $subClass = null;

                                            switch ($type) {
                                                case Loupe::alias():
                                                    $subClass = Loupe::class;
                                                    break;
                                                case Headlight::alias():
                                                    $subClass = Headlight::class;
                                                    break;
                                            }

                                            if ($subClass) {
                                                // ① 테이블(시력정보 + 계산요약)
                                                echo '<div class="no-order-option-table">';
                                                echo $subClass::renderTable($type, $sub->getAttributes());
                                                echo  '</div>';

                                                // ② 나머지 옵션 태그
                                                echo '<div class="no-order-option-tags">';
                                                echo $subClass::renderTag($type, $sub->getAttributes());
                                                echo  '</div>';
                                            }
                                            ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?= $item->quantity ?></td>
                                <td>
                                    <div class="no-prod-attr-list__action">
                                        <button 
                                            type="button" 
                                            data-action="update"
                                            class="no-btn-primary-outline"
                                        >
                                            <span>수정</span>
                                        </button>
                                        <button 
                                            type="button" 
                                            data-action="delete"
                                            class="no-btn-error-outline"
                                        >
                                            <span>삭제</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php if (!empty($item->sets)) : ?>
                                <?php foreach ($item->sets as $setItem): 
                                    $setProduct = $setItem->product;
                                    $setType = $setProduct->type;
                                    $setSub = $setType ? $setProduct->{$setType} : null;
                                    $setProduct->load(['serials']);
                                    $serials = $setProduct->serials 
                                    ? implode(', <br>', array_map(fn($s) => $s->serial_number, $setProduct->serials))
                                    : '-';
                                ?>
                                <tr class="no-order-subitem">
                                    <td style="padding-left: 2rem">
                                        <?php if ($img = $setProduct->template->fileattachment[0] ?? null): ?>
                                            <img src="<?= e($img->upload_path) ?>" alt="제품 이미지" style="width: 100%; min-width: 8rem; border-radius: .4rem;">
                                        <?php else: ?>
                                            <div class="no-order-restore__placeholder">No Image</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= e($setProduct->name) ?></td>
                                    <td><?= e($setProduct->model) ?></td>
                                    <td><?= '$'.e($setProduct->price) ?></td>
                                    <td><?= $serials ?></td>
                                    <td>
                                        <div class="no-order-option-tags">
                                            <?php if ($setSub) : ?>
                                                <?php foreach ($setSub->getAttributes() as $field => $value) :
                                                    if (in_array($field, ['id'])) continue;
                                                ?>
                                                    <div class="no-order-option-tag">
                                                        <span class="label"><?= __('loupe.' . $field) ?></span>
                                                        <span class="value"><?= e($value) ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?= $setItem->quantity ?></td>
                                    <td></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
  </div>
</div>

<?php end_section() ?>