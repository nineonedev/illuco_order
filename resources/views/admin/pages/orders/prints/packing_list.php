<?php
/**
 * @var \App\Domains\Order\Entities\OrderDocument $document
 */
$entity = $document->packing_list;
$order = $document->order;
$items = $order->items;

// ex) packing_list
$type = $document->type;

// ① 언더스코어 → 하이픈
$type = str_replace('_', '-', $type);

// ② 각 단어 첫 글자 대문자
$type = ucwords($type, '-');

// 최종 파일명
$pdfName = "{$type}-{$document->document_no}.pdf";
?>

<div class="packing-doc" id="print-area" data-pdf-name="<?= $pdfName ?>">

    <!-- Header -->
    <div class="packing-header">
        <div class="packing-logo">
            <img src="<?= asset_path('img/meta/logo-primary.svg') ?>" alt="ILLUCO">
        </div>
        <div class="packing-title">
            <h1>Packing List</h1>
        </div>
    </div>

    <div class="--flex-end">
        <table class="packing-meta-table">
            <tr>
                <th>Ref. No.</th>
                <td><?= e($entity->ref_no) ?></td>
            </tr>
            <tr>
                <th>Date</th>
                <td><?= e($entity->packing_date) ?></td>
            </tr>
            <tr>
                <th>PI No.</th>
                <td><?= e($entity->pi_no) ?></td>
            </tr>
            <tr>
                <th>PO No.</th>
                <td><?= e($entity->po_no) ?></td>
            </tr>
        </table>
    </div>

    <!-- Bill To / Ship To -->
    <div class="packing-between">
        <div class="packing-block">
            <p class="packing-label">BILL TO:</p>
            <p><strong><?= e($entity->bill_to_name) ?></strong></p>
            <p><?= e($entity->bill_to_address) ?></p>
            <p>Tel.: <?= e($entity->bill_to_tel) ?></p>
            <p>Attn.: <?= e($entity->bill_to_attn) ?></p>
            <p>Email: <?= e($entity->bill_to_email) ?></p>
        </div>
        <div class="packing-block">
            <p class="packing-label">SHIP TO:</p>
            <p><strong><?= e($entity->ship_to_name) ?></strong></p>
            <p><?= e($entity->ship_to_address) ?></p>
            <p>Tel.: <?= e($entity->ship_to_tel) ?></p>
            <p>Attn.: <?= e($entity->ship_to_attn) ?></p>
            <p>Email: <?= e($entity->ship_to_email) ?></p>
        </div>
    </div>

    <!-- Delivery Info -->
    <table class="packing-info-table">
        <thead>
            <tr>
                <th>Carrier</th>
                <th>Estimated Delivery Date</th>
                <th>Payment Terms</th>
                <th>Price Terms</th>
                <th>Country of Origin</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= e($entity->carrier) ?></td>
                <td><?= e($entity->estimated_delivery_date) ?></td>
                <td><?= e($entity->payment_terms) ?></td>
                <td><?= e($entity->price_terms) ?></td>
                <td><?= e($entity->country_of_origin) ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Items Table -->
    <div class="packing-items">
        <table class="packing-items-table">
            <thead>
                <tr>
                    <th>Carton No.</th>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Weight (g)</th>
                    <th>Measurement (CM)</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php for ($i = 0; $i < 16; $i++): ?>
                    <?php
                        /** @var \App\Domains\Order\Entities\OrderItem|null $item */
                        $item = $items[$i] ?? null;

                        if ($item) {
                            $product = $item->product;
                            $values = [
                                $no . '/1',
                                e($product->model),
                                e($product->description),
                                e($item->quantity) . ' PC(S)',
                                e($item->net_weight ?? '-'),
                                e($item->measurement_cm ?? '-'),
                            ];
                        } else {
                            $values = array_fill(0, 6, '&nbsp;');
                        }
                    ?>
                    <tr>
                        <?php foreach ($values as $value): ?>
                            <td><?= $value ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php if ($item) $no++; ?>
                <?php endfor; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="right">Grand Total:</td>
                    <td class="right"><?= e($entity->total_quantity) ?> PC(S)</td>
                    <td class="right"><?= e($entity->total_weight) ?> g</td>
                    <td class="right"><?= e($entity->total_volume_cbm) ?> CBM</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="packing-footer">
        <p class="packing-note">
            * Packing Details: <?= e($entity->packing_details) ?>
        </p>
        <p class="packing-hs">
            HS Code: <?= e($entity->hs_code) ?>
        </p>
    </div>

    <div class="packing-sign">
        <p>Supplied by</p>
        <img src="<?= asset_path('img/meta/sign.png') ?>" alt="ILLUCO CO., LTD">
    </div>

</div>
