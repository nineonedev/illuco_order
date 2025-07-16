<?php
/**
 * @var \App\Domains\Order\Entities\OrderDocument $document
 */
$entity = $document->packing_list;
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

    <div class="packing-doc">

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
                    <td>
                        <input type="text" name="ref_no"
                               value="<?= e($entity->ref_no) ?>">
                    </td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td>
                        <input type="date" name="packing_date"
                               value="<?= e($entity->packing_date) ?>">
                    </td>
                </tr>
                <tr>
                    <th>PI No.</th>
                    <td>
                        <input type="text" name="pi_no"
                               value="<?= e($entity->pi_no) ?>">
                    </td>
                </tr>
                <tr>
                    <th>PO No.</th>
                    <td>
                        <input type="text" name="po_no"
                               value="<?= e($entity->po_no) ?>">
                    </td>
                </tr>
            </table>
        </div>

        <!-- Bill To / Ship To -->
        <div class="packing-between">
            <div class="packing-block">
                <p class="packing-label">BILL TO:</p>
                <p><input type="text" name="bill_to_name" value="<?= e($entity->bill_to_name) ?>" placeholder="Company Name"></p>
                <p><input type="text" name="bill_to_address" value="<?= e($entity->bill_to_address) ?>" placeholder="Address"></p>
                <p>Tel.: <input type="text" name="bill_to_tel" value="<?= e($entity->bill_to_tel) ?>"></p>
                <p>Attn.: <input type="text" name="bill_to_attn" value="<?= e($entity->bill_to_attn) ?>"></p>
                <p>Email: <input type="text" name="bill_to_email" value="<?= e($entity->bill_to_email) ?>"></p>
            </div>
            <div class="packing-block">
                <p class="packing-label">SHIP TO:</p>
                <p><input type="text" name="ship_to_name" value="<?= e($entity->ship_to_name) ?>" placeholder="Company Name"></p>
                <p><input type="text" name="ship_to_address" value="<?= e($entity->ship_to_address) ?>" placeholder="Address"></p>
                <p>Tel.: <input type="text" name="ship_to_tel" value="<?= e($entity->ship_to_tel) ?>"></p>
                <p>Attn.: <input type="text" name="ship_to_attn" value="<?= e($entity->ship_to_attn) ?>"></p>
                <p>Email: <input type="text" name="ship_to_email" value="<?= e($entity->ship_to_email) ?>"></p>
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
                    <td><input type="text" name="carrier" value="<?= e($entity->carrier) ?>"></td>
                    <td><input type="text" name="estimated_delivery_date" value="<?= e($entity->estimated_delivery_date) ?>"></td>
                    <td><input type="text" name="payment_terms" value="<?= e($entity->payment_terms) ?>"></td>
                    <td><input type="text" name="price_terms" value="<?= e($entity->price_terms) ?>"></td>
                    <td><input type="text" name="country_of_origin" value="<?= e($entity->country_of_origin) ?>"></td>
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
                    <?php foreach ($items as $item): ?>
                        <?php $product = $item->product; ?>
                        <tr>
                            <td><?= $no ?>/1</td>
                            <td><?= e($product->model) ?></td>
                            <td><?= e($product->description) ?></td>
                            <td class="right"><?= e($item->quantity) ?> PC(S)</td>
                            <td class="right"><?= e($item->net_weight ?? '-') ?></td>
                            <td><?= e($item->measurement_cm ?? '-') ?></td>
                        </tr>
                        <?php $no++; ?>
                    <?php endforeach; ?>
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
                * Packing Details:
                <textarea name="packing_details" rows="2" placeholder="e.g. 1CTN(S) / 3240g / 0.023125CBM"><?= e($entity->packing_details) ?></textarea>
            </p>
            <p class="packing-hs">
                HS Code:
                <input type="text" name="hs_code" value="<?= e($entity->hs_code) ?>">
            </p>
        </div>

        <div class="packing-sign">
            <p>Supplied by</p>
            <img src="<?= asset_path('img/meta/sign.png') ?>" alt="ILLUCO CO., LTD">
        </div>

        <div class="no-form-action">
            <a href="<?= route('admin.orders.edit', ['orderNo' => $document->order->order_no]) ?>" class="no-btn-primary-outline --sm">취소</a>
            <button type="submit" class="no-btn-primary --sm">저장</button>
        </div>
    </div>
</form>
