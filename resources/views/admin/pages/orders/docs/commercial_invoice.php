<?php
/**
 * @var \App\Domains\Order\Entities\OrderDocument $document
 */
$entity = $document->commercial_invoice;
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

    <div class="commercial-doc">

        <!-- Header -->
        <div class="commercial-header">
            <div class="commercial-logo">
                <img src="<?= asset_path('img/meta/logo-primary.svg') ?>" alt="ILLUCO">
            </div>
            <div class="commercial-title">
                <h1>Commercial Invoice</h1>
            </div>
        </div>

        <div class="--flex-end">
            <table class="commercial-meta-table">
                <tr>
                    <th>Ref. No.</th>
                    <td>
                        <input type="text" name="ref_no" value="<?= e($entity->ref_no) ?>">
                    </td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td>
                        <input type="date" name="invoice_date" value="<?= e($entity->invoice_date) ?>">
                    </td>
                </tr>
                <tr>
                    <th>PI No.</th>
                    <td>
                        <input type="text" name="pi_no" value="<?= e($entity->pi_no) ?>">
                    </td>
                </tr>
                <tr>
                    <th>PO No.</th>
                    <td>
                        <input type="text" name="po_no" value="<?= e($entity->po_no) ?>">
                    </td>
                </tr>
            </table>
        </div>

        <!-- Bill To / Ship To -->
        <div class="commercial-between">
            <div class="commercial-block">
                <p class="commercial-label">BILL TO:</p>
                <p><input type="text" name="bill_to_name" value="<?= e($entity->bill_to_name) ?>" placeholder="Company Name"></p>
                <p><input type="text" name="bill_to_address" value="<?= e($entity->bill_to_address) ?>" placeholder="Address"></p>
                <p>Tel.: <input type="text" name="bill_to_tel" value="<?= e($entity->bill_to_tel) ?>"></p>
                <p>Attn.: <input type="text" name="bill_to_attn" value="<?= e($entity->bill_to_attn) ?>"></p>
                <p>Email: <input type="text" name="bill_to_email" value="<?= e($entity->bill_to_email) ?>"></p>
            </div>
            <div class="commercial-block">
                <p class="commercial-label">SHIP TO:</p>
                <p><input type="text" name="ship_to_name" value="<?= e($entity->ship_to_name) ?>" placeholder="Company Name"></p>
                <p><input type="text" name="ship_to_address" value="<?= e($entity->ship_to_address) ?>" placeholder="Address"></p>
                <p>Tel.: <input type="text" name="ship_to_tel" value="<?= e($entity->ship_to_tel) ?>"></p>
                <p>Attn.: <input type="text" name="ship_to_attn" value="<?= e($entity->ship_to_attn) ?>"></p>
                <p>Email: <input type="text" name="ship_to_email" value="<?= e($entity->ship_to_email) ?>"></p>
            </div>
        </div>

        <!-- Delivery Info -->
        <table class="commercial-info-table">
            <thead>
                <tr>
                    <th>Carrier</th>
                    <th>Estimated Delivery Date</th>
                    <th>Payment Terms</th>
                    <th>Price Terms</th>
                    <th>Country of Origin</th>
                    <th>Currency</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="carrier" value="<?= e($entity->carrier) ?>"></td>
                    <td><input type="text" name="estimated_delivery_date" value="<?= e($entity->estimated_delivery_date) ?>"></td>
                    <td><input type="text" name="payment_terms" value="<?= e($entity->payment_terms) ?>"></td>
                    <td><input type="text" name="price_terms" value="<?= e($entity->price_terms) ?>"></td>
                    <td><input type="text" name="country_of_origin" value="<?= e($entity->country_of_origin) ?>"></td>
                    <td><input type="text" name="currency" value="<?= e($entity->currency) ?>"></td>
                </tr>
            </tbody>
        </table>

        <!-- Items Table -->
        <div class="commercial-items">
            <table class="commercial-items-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Product</th>
                        <th>Model</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($items as $item): ?>
                        <?php $product = $item->product; ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= e($product->name) ?></td>
                            <td><?= e($product->model) ?></td>
                            <td><?= e($product->description) ?></td>
                            <td class="right"><?= e($item->quantity) ?> PC(S)</td>
                            <td class="right">$ <?= number_format($item->unit_price, 2) ?></td>
                            <td class="right">$ <?= number_format($item->total_price, 2) ?></td>
                        </tr>
                        <?php $no++; ?>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="right">Total quantity:</td>
                        <td class="right"><?= e($entity->total_quantity) ?> PC(S)</td>
                        <td class="right">Sub Total:</td>
                        <td class="right">$ <?= number_format($entity->sub_total, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="right">Freight charge</td>
                        <td class="right">$ <?= number_format($entity->freight_charge, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="right"><strong>Total:</strong></td>
                        <td class="right"><strong>$ <?= number_format($entity->grand_total, 2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="commercial-footer">
            <p class="commercial-hs">
                HS Code:
                <input type="text" name="hs_code" value="<?= e($entity->hs_code) ?>">
            </p>
            <p class="commercial-dev">
                DEV:
                <input type="text" name="dev" value="<?= e($entity->dev) ?>">
            </p>
            <p class="commercial-lst">
                LST:
                <input type="text" name="lst" value="<?= e($entity->lst) ?>">
            </p>
            <p class="commercial-ein">
                EIN:
                <input type="text" name="ein" value="<?= e($entity->ein) ?>">
            </p>
        </div>

        <div class="commercial-sign">
            <p>Supplied by</p>
            <img src="<?= asset_path('img/meta/sign.png') ?>" alt="ILLUCO CO., LTD">
        </div>

        <div class="no-form-action">
            <a href="<?= route('admin.orders.show', ['id' => $order->id]) ?>" class="no-btn-primary-outline --sm">취소</a>
            <button type="submit" class="no-btn-primary --sm">저장</button>
        </div>
    </div>
</form>
