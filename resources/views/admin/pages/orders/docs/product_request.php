<?php
/**
 * @var \App\Domains\Order\Entities\OrderDocument $document
 */
$entity = $document->product_request;
$order = $document->order;
$items = $order->items;
?>

<form
    id="frm"
    action="<?= route('admin.order_documents.update', ['id' => $entity->id]) ?>"
    method="POST"
>
    <?= csrf_field() ?>
    <?= method_field('PUT') ?>

    <div class="no-order-doc-edit">

        <!-- Header -->
        <div class="invoice-header">
            <div class="logo">
                <img src="<?= asset_path('img/meta/logo-primary.svg') ?>" alt="ILLUCO">
            </div>
            <div class="invoice-title">
                <h1>LH 생산의뢰서 (Product Request)</h1>
            </div>
        </div>

        <!-- Header Info -->
        <div class="invoice-between">
            <table class="ref-table">
                <tr>
                    <td>국가</td>
                    <td>
                        <input type="text" name="country"
                               placeholder="USA"
                               value="<?= e($entity->country) ?>">
                    </td>
                    <td>고객명</td>
                    <td>
                        <input type="text" name="customer_name"
                               placeholder="CRL"
                               value="<?= e($entity->customer_name) ?>">
                    </td>
                </tr>
                <tr>
                    <td>작성일</td>
                    <td>
                        <input type="date" name="created_date"
                               value="<?= e($entity->created_date) ?>">
                    </td>
                    <td>납기일</td>
                    <td>
                        <input type="date" name="delivery_date"
                               value="<?= e($entity->delivery_date) ?>">
                    </td>
                </tr>
                <tr>
                    <td>담당자</td>
                    <td>
                        <input type="text" name="manager_name"
                               placeholder="Angela"
                               value="<?= e($entity->manager_name) ?>">
                    </td>
                    <td>문서번호</td>
                    <td>
                        <input type="text" name="document_no"
                               placeholder="US-CRL-28"
                               value="<?= e($entity->document_no) ?>">
                    </td>
                </tr>
            </table>
        </div>

        <!-- Items Table -->
        <div class="table-section">
            <table class="item-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Product</th>
                        <th>Model</th>
                        <th>Name</th>
                        <th>수량</th>
                        <th>Engraving</th>
                        <th>WD</th>
                        <th>PD</th>
                        <th>VD</th>
                        <th>Frame</th>
                        <th>시리얼넘버</th>
                        <th>박스번호</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($items as $item): ?>
                        <?php 
                            $product = $item->product; 
                            $subProduct = $item->product->{$product->type};
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= e($product->name) ?></td>
                            <td><?= e($product->model) ?></td>
                            <td><?= e($subProduct->engraving_text ?? '-') ?></td>
                            <td class="right"><?= e($item->quantity) ?></td>
                            <td><?= e($subProduct->engraving_text ?? '-') ?></td>
                            <td><?= e($subProduct->working_distance ?? '-') ?></td>
                            <td><?= e($subProduct->pd_total ?? '-') ?></td>
                            <td><?= e($subProduct->vertex_distance ?? '-') ?></td>
                            <td><?= e($subProduct->frame_type ?? '-') ?></td>
                            <td><?= e($product->serial_no ?? '-') ?></td>
                            <td><!-- 박스번호는 수기로 입력 → 빈칸 유지 --></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Box Info -->
        <div class="table-section">
            <h3>박스 정보</h3>
            <table class="meta-table">
                <thead>
                    <tr>
                        <th>Box No</th>
                        <th>무게</th>
                        <th>사이즈</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <tr>
                            <td>
                                <input type="text" name="box<?= $i ?>_no"
                                       value="<?= e($entity->{"box{$i}_no"}) ?>">
                            </td>
                            <td>
                                <input type="text" name="box<?= $i ?>_weight"
                                       value="<?= e($entity->{"box{$i}_weight"}) ?>">
                            </td>
                            <td>
                                <input type="text" name="box<?= $i ?>_size"
                                       value="<?= e($entity->{"box{$i}_size"}) ?>">
                            </td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <!-- Note -->
        <div class="bank">
            <p><strong>메모</strong></p>
            <textarea name="note" rows="5"
                      placeholder="특이사항 기재"><?= e($entity->note) ?></textarea>
        </div>

        <div class="sign-zone --edit">
            <p class="sign-text">Supplied by</p>
            <div class="sign-img">
                <img src="<?= asset_path('img/meta/sign.png') ?>" alt="ILLUCO CO., LTD">
            </div>
        </div>

        <div class="footer">
            <p>ILLUCO Co., Ltd. 102-304 SK Ventium, #166 Gosan-ro, Gunpo-si, Gyeonggi-do, Korea</p>
            <p>www.illuco.co.kr Tel. +82 31 429 8825 Fax. +82 31 429 8826 info@illuco.co.kr</p>
        </div>

        <div class="no-form-action">
            <a href="<?= route('admin.orders.show', ['id' => $order->id]) ?>" class="no-btn-primary-outline --sm">취소</a>
            <button type="submit" class="no-btn-primary --sm">저장</button>
        </div>

    </div>
</form>
