<?php
/**
 * @var \App\Domains\Order\Entities\OrderDocument $document
 */
$entity = $document->product_request;
$order = $document->order;
$items = $order->items;

$type = str_replace('_', '-', $document->type);
$type = ucwords($type, '-');
$pdfName = "{$type}-{$document->document_no}.pdf";

?>

<div class="document-container">
<form class="document-editor" id="frm" method="POST" action="<?= route('admin.order_documents.update', ['id' => $entity->id]) ?>">
    <?= csrf_field() ?>
    <?= method_field('PUT') ?>

    <div class="lh-doc" id="print-area" data-pdf-name="<?= $pdfName ?>" data-page="landscape">

        <div class="lh-header">
            <div class="logo">
                <img src="<?= asset_path('img/meta/logo-primary.svg') ?>" alt="ILLUCO">
            </div>

            <div class="lh-title">
                LH 생산의뢰서
            </div>
        </div>

        <div class="lh-info-no">
            <dl>
                <dt>문서번호</dt>
                <dd><?= e($entity->document->document_no ?? $document->document_no) ?></dd>
            </dl>
        </div>

        <table class="lh-info-table">
            <thead>
                <tr>
                    <th>국가</th>
                    <th>고객명</th>
                    <th>작성일</th>
                    <th>납기일</th>
                    <th>담당자</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="country" value="<?= e($entity->country ?: __('system.countries.'.$order->customer->country)) ?>"></td>
                    <td><input type="text" name="customer_name" value="<?= e($entity->customer_name ?: $order->customer->name) ?>"></td>
                    <td><input type="date" name="created_date" value="<?= e($entity->created_date ?: date('Y-m-d')) ?>"></td>
                    <td><input type="date" name="delivery_date" value="<?= e($entity->delivery_date) ?>"></td>
                    <td><input type="text" name="manager_name" value="<?= e($order->manager_name ?: $order->orderer_name) ?>"></td>
                </tr>
            </tbody>
        </table>

        <table class="lh-items-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Product</th>
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
                <?php for ($i = 0; $i < 16; $i++): ?>
                    <?php
                        $item = $items[$i] ?? null;
                        $serials = '-'; 

                        if ($item) {
                            $product = $item->product;
                            $product->load(['serials']);
                            $sub = $product->{$product->type};

                            if ($product->serials) {
                                $serials = $product->serials[0]->serial_number . ' 등 ' . count($product->serials) . '개';
                            }
                        }
                        
                        $itemIdx = $i+1;
                    ?>
                    <tr>
                        <td><?= $item ? $no++ : '&nbsp;' ?></td>
                        <td><?= $item ? e($product->name) : '&nbsp;' ?></td>
                        <td><?= $item ? e($product->model) : '&nbsp;' ?></td>
                        <td><?= $item ? e($item->quantity) : '&nbsp;' ?></td>
                        <td><?= $item ? e($sub->engraving_text ?? '') : '&nbsp;' ?></td>
                        <td><?= $item ? e($sub->working_distance ?? '') : '&nbsp;' ?></td>
                        <td><?= $item ? e($sub->pd_total ?? '') : '&nbsp;' ?></td>
                        <td><?= $item ? e($sub->vertex_distance ?? '') : '&nbsp;' ?></td>
                        <td><?= $item ? e($sub->frame_type ?? '') : '&nbsp;' ?></td>
                        <td><?= $item ? e($serials) : '&nbsp;' ?></td>
                        <td>
                            <input type="number" min="1" max="5" name="item<?= $itemIdx ?>_no" value="<?= e($entity->{"item{$itemIdx}_no"} ?? '') ?>">
                        </td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <div class="lh-bottom">
            <div class="lh-box-section">
                <table>
                    <thead>
                        <tr>
                            <th>박스번호</th>
                            <th>무게</th>
                            <th>사이즈</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <tr>
                                <td><input type="number" min="1" max="5" name="box<?= $i ?>_no" value="<?= e($entity->{"box{$i}_no"}) ?>"></td>
                                <td><input type="text" name="box<?= $i ?>_weight" value="<?= e($entity->{"box{$i}_weight"}) ?>"></td>
                                <td><input type="text" name="box<?= $i ?>_size" value="<?= e($entity->{"box{$i}_size"}) ?>"></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <div class="lh-note">
                <label for="note">메모:</label>
                <textarea name="note" id="note" rows="6"><?= e($entity->note) ?></textarea>
            </div>
        </div>

        <div class="no-form-action">
            <a href="<?= route('admin.orders.edit', ['orderNo' => $order->order_no]) ?>" class="no-btn-primary-outline --sm" data-action="cancel">취소</a>
            <button type="submit" class="no-btn-primary --sm">저장</button>
        </div>

    </div>
</form>
</div>