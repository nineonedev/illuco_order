<?php
/**
 * @var \App\Domains\Order\Entities\OrderDocument $document
 */
$entity = $document->commercial_invoice;
$order = $document->order;
$items = $order->items;

$type = str_replace('_', '-', $document->type);
$type = ucwords($type, '-');
$pdfName = "{$type}-{$document->document_no}.pdf";

$subTotal = 0;
$totalQuantity = 0;
foreach ($items as $item) {
    $subTotal += $item->total_price;
    $totalQuantity += $item->quantity;
}
$freightCharge = $entity->freight_charge ?? 0;
$grandTotal = $subTotal + $freightCharge;
?>

<form id="frm" method="POST" action="<?= route('admin.order_documents.update', ['id' => $entity->id]) ?>" class="no-document__edit">
    <?= csrf_field() ?>
    <?= method_field('PUT') ?>

    <div class="commercial-doc no-preview-container" id="print-area" data-pdf-name="<?= $pdfName ?>">

        <!-- Header -->
        <div class="commercial-header">
            <div class="commercial-logo">
                <img src="<?= asset_path('img/meta/logo-primary.svg') ?>" alt="ILLUCO">
            </div>
            <div class="commercial-title">
                <h1>Commercial Invoice</h1>
            </div>
        </div>

        <!-- Meta -->
        <div class="commercial-meta-end">
            <table class="commercial-meta-table">
                <tr>
                    <th>Ref. No.</th>
                    <td><input type="text" name="ref_no" value="<?= e($entity->ref_no ?: $document->document_no) ?>"></td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td><input type="date" name="invoice_date" value="<?= e($entity->invoice_date ?: date('Y-m-d')) ?>"></td>
                </tr>
                <tr>
                    <th>PI No.</th>
                    <td><input type="text" name="pi_no" value="<?= e($entity->pi_no) ?>"></td>
                </tr>
                <tr>
                    <th>PO No.</th>
                    <td><input type="text" name="po_no" value="<?= e($entity->po_no) ?>"></td>
                </tr>
            </table>
        </div>

        <!-- BILL TO / SHIP TO -->
        <div class="commercial-between">
            <div class="commercial-block">
                <p class="commercial-label">BILL TO:</p>
                <p><strong><input type="text" name="bill_to_name" value="<?= e($entity->bill_to_name ?: $order->customer->name) ?>" placeholder="name"></strong></p>
                <p><input type="text" name="bill_to_address" value="<?= e($entity->bill_to_address ?: $order->customer->address) ?>" placeholder="address"></p>
                <p>Tel.: <input type="text" name="bill_to_tel" value="<?= e($entity->bill_to_tel ?: $order->customer->phone) ?>"></p>
                <p>Attn.: <input type="text" name="bill_to_attn" value="<?= e($entity->bill_to_attn) ?>"></p>
                <p>Email: <input type="email" name="bill_to_email" value="<?= e($entity->bill_to_email ?: $order->customer->email) ?>"></p>
            </div>
            <div class="commercial-block">
                <p class="commercial-label">SHIP TO:</p>
                <p>Currency: <input type="text" name="currency" value="<?= e($entity->currency ?: 'USD') ?>" placeholder="USD"></p>
                <p><strong><input type="text" name="ship_to_name" value="<?= e($entity->ship_to_name ?: $order->customer->name) ?>"placeholder="name"></strong></p>
                <p><input type="text" name="ship_to_address" value="<?= e($entity->ship_to_address ?: $order->customer->address) ?>" placeholder="address"></p>
                <p>Tel.: <input type="text" name="ship_to_tel" value="<?= e($entity->ship_to_tel ?: $order->customer->phone) ?>"></p>
                <p>Attn.: <input type="text" name="ship_to_attn" value="<?= e($entity->ship_to_attn) ?>"></p>
                <p>Email: <input type="email" name="ship_to_email" value="<?= e($entity->ship_to_email ?: $order->customer->email) ?>"></p>
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

        <!-- Items -->
        <div class="commercial-items">
            <table class="commercial-items-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Product</th>
                        <th>Model</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php for ($i = 0; $i < 16; $i++): ?>
                        <?php
                            $item = $items[$i] ?? null;
                            if ($item) {
                                $product = $item->product;
                            }
                        ?>
                        <tr>
                            <td><?= $item ? $no : '&nbsp;' ?></td>
                            <td>
                                <input type="text" name="items[<?= $i ?>][product_name]" value="<?= $item ? e($product->name) : '' ?>">
                            </td>
                            <td>
                                <input type="text" name="items[<?= $i ?>][model]" value="<?= $item ? e($product->model ?? '') : '' ?>">
                            </td>
                            <td>
                                <input type="text" name="items[<?= $i ?>][description]" value="<?= $item ? e($product->description ?? '') : '' ?>">
                            </td>
                            <td>
                                <input type="number" name="items[<?= $i ?>][quantity]" value="<?= $item ? e($item->quantity) : '' ?>">
                            </td>
                            <td>PC(S)</td>
                            <td>
                                <input type="text" name="items[<?= $i ?>][unit_price]" value="<?= $item ? e($item->unit_price) : '' ?>">
                            </td>
                            <td>
                                <input type="text" name="items[<?= $i ?>][total_price]" value="<?= $item ? e($item->total_price) : '' ?>">
                            </td>
                        </tr>
                        <?php if ($item) $no++; ?>
                    <?php endfor; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="right">Total quantity:</td>
                        <td class="right"><?= e($totalQuantity) ?></td>
                        <td class="right">PC(S)</td>
                        <td class="right">Sub Total:</td>
                        <td class="right">$ <?= number_format($subTotal, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="6"></td>
                        <td class="right">Freight charge:</td>
                        <td class="right">
                            $ <input type="text" name="freight_charge" value="<?= number_format($freightCharge, 2) ?>">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6"></td>
                        <td class="right"><strong>Total:</strong></td>
                        <td class="right"><strong>$ <?= number_format($grandTotal, 2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer -->
        <div class="commercial-footer">
            <p>HS Code: <input type="text" name="hs_code" value="<?= e($entity->hs_code) ?>"></p>
            <p>DEV: <input type="text" name="dev" value="<?= e($entity->dev) ?>"></p>
            <p>LST: <input type="text" name="lst" value="<?= e($entity->lst) ?>"></p>
            <p>EIN: <input type="text" name="ein" value="<?= e($entity->ein) ?>"></p>
        </div>

        <div class="commercial-sign">
            <p>Supplied by</p>
            <img src="<?= asset_path('img/meta/sign.png') ?>" alt="ILLUCO CO., LTD">
        </div>

        <div class="no-form-action">
            <a href="<?= route('admin.orders.edit', ['orderNo' => $order->order_no]) ?>" class="no-btn-primary-outline --sm">취소</a>
            <button type="submit" class="no-btn-primary --sm">저장</button>
        </div>
    </div>
</form>
