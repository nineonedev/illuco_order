<?php
/**
 * @var \App\Domains\Order\Entities\OrderDocument $document
 */
$entity = $document->proforma_invoice;
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
                <h1>Proforma Invoice</h1>
            </div>
        </div>

        <!-- Buyer / Ref Info -->
        <div class="invoice-between">
            <div class="buyer-section">
                <p><strong>Buyer</strong></p>
                <p>
                    Company: 
                    <input type="text" name="buyer_name" 
                           placeholder="ILLUCO CO., LTD" 
                           value="<?= e($entity->buyer_name) ?>">
                </p>
                <p>
                    Address: 
                    <input type="text" name="buyer_address" 
                           placeholder="102-304 SK Ventium, Gunpo-si, Korea" 
                           value="<?= e($entity->buyer_address) ?>">
                </p>
                <p>
                    Tel.: 
                    <input type="text" name="buyer_tel" 
                           placeholder="+82 31 429 8825" 
                           value="<?= e($entity->buyer_tel) ?>">
                </p>
                <p>
                    Attn.: 
                    <input type="text" name="buyer_attn" 
                           placeholder="홍길동" 
                           value="<?= e($entity->buyer_attn) ?>">
                </p>
                <p>
                    Email:
                    <input type="email" name="buyer_email" 
                              placeholder="person@domain.com" 
                              value="<?= e($entity->buyer_email) ?>" />
                </p>
            </div>

            <table class="ref-table">
                <tr>
                    <td>Ref. No.</td>
                    <td>
                        <input type="text" name="document_no" 
                               placeholder="JP-CR-2408" 
                               value="<?= e($entity->document_no) ?>">
                    </td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td>
                        <input type="date" name="invoice_date" 
                               placeholder="2024-08-01" 
                               value="<?= e($entity->invoice_date) ?>">
                    </td>
                </tr>
                <tr>
                    <td>P.O. No.</td>
                    <td>
                        <input type="text" name="purchase_order_no" 
                               placeholder="PO-2024-0801" 
                               value="<?= e($entity->purchase_order_no) ?>">
                    </td>
                </tr>
            </table>
        </div>

        <!-- Meta Table -->
        <div class="table-section">
            <small class="--tar">
                <p><u>Currency : USD</u></p>
                <p>* Country of origin : Republic of Korea</p>
            </small>
            <table class="meta-table">
                <tr>
                    <th>Sales Person</th>
                    <th>Tel. No./Email</th>
                    <th>Estimated Date of Delivery</th>
                    <th colspan="2">Price Terms</th>
                    <th>Payment Terms</th>
                    <th>Shipment by</th>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="salesperson_name" 
                               placeholder="JY" 
                               value="<?= e($entity->salesperson_name) ?>">
                    </td>
                    <td>
                        <input type="text" name="salesperson_tel" 
                               placeholder="+82 70 4922 7441" 
                               value="<?= e($entity->salesperson_tel) ?>"><br>
                        <input type="email" name="salesperson_email" 
                               placeholder="kms@illuco.co.kr" 
                               value="<?= e($entity->salesperson_email) ?>">
                    </td>
                    <td>
                        <input type="text" name="estimated_date_of_delivery" 
                               placeholder="Within a week after payment" 
                               value="<?= e($entity->estimated_date_of_delivery) ?>">
                    </td>
                    <td colspan="2">
                        <input type="text" name="price_terms" 
                               placeholder="EXW Korea" 
                               value="<?= e($entity->price_terms) ?>">
                    </td>
                    <td>
                        <input type="text" name="payment_terms" 
                               placeholder="100% T/T in advance" 
                               value="<?= e($entity->payment_terms) ?>">
                    </td>
                    <td>
                        <input type="text" name="shipment_by" 
                               placeholder="Air (DHL)" 
                               value="<?= e($entity->shipment_by) ?>">
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
                        <th>Description</th>
                        <th>Qty</th>
                        <th>PC(S)</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; $totalAmount = 0; ?>
                    <?php foreach ($items as $item): ?>
                        <?php
                            $product = $item->product;
                            $total = $item->total_price;
                            $totalAmount += $total;
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= e($product->name) ?></td>
                            <td><?= e($product->model) ?></td>
                            <td><?= e($product->description) ?></td>
                            <td class="right"><?= e($item->quantity) ?></td>
                            <td class="right">PC(S)</td>
                            <td class="right">$ <?= number_format($item->unit_price, 2) ?></td>
                            <td class="right">$ <?= number_format($total, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <?php
                        $totalQty = array_sum(array_map(fn($i) => $i->quantity, $items));
                    ?>
                    <tr>
                        <td colspan="3"></td>
                        <td class="right bold">Total</td>
                        <td class="right"><?= $totalQty ?></td>
                        <td class="right">PC(S)</td>
                        <td class="right">Sub Total:</td>
                        <td class="right bold">$ <?= number_format($totalAmount, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="empty"></td>
                        <td>Freight Charge</td>
                        <td class="right">
                            $ <input type="number" step="0.01" name="freight_charge" 
                                     placeholder="0.00" 
                                     value="<?= e($entity->freight_charge) ?>">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="empty"></td>
                        <td class="right bold">Total:</td>
                        <td class="right bold">$ <?= number_format($totalAmount + (float)($entity->freight_charge ?? 0), 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Bank Details -->
        <div class="bank">
            <p><strong>Bank Details</strong></p>
            <div class="bank-details input">
                <p>
                    HS Code: 
                    <input type="text" name="hs_code" 
                        placeholder="9018.90.8900" 
                        value="<?= e($entity->hs_code) ?>">
                </p>
                <p>
                    Beneficiary: 
                    <input type="text" name="bank_beneficiary" 
                        placeholder="ILLUCO" 
                        value="<?= e($entity->bank_beneficiary) ?>">
                </p>
                <p>
                    Bank Name: 
                    <input type="text" name="bank_name" 
                        placeholder="Shinhan Bank Korea, Yeouido Branch" 
                        value="<?= e($entity->bank_name) ?>">
                </p>
                <p>
                    Bank Address: 
                    <input type="text" name="bank_address" 
                        placeholder="Shinsong Center Building, Seoul, Korea" 
                        value="<?= e($entity->bank_address) ?>">
                </p>
                <p>
                    Swift Code: 
                    <input type="text" name="bank_swift_code" 
                        placeholder="SHBKKRSE" 
                        value="<?= e($entity->bank_swift_code) ?>">
                </p>
                <p>
                    Account No.: 
                    <input type="text" name="bank_account_no" 
                        placeholder="180-007-045998" 
                        value="<?= e($entity->bank_account_no) ?>">
                </p>
                <p class="full">
                    Note: 
                    <textarea type="text" name="note" rows="8"
                        placeholder="Details of Charges: OUR"><?=e($entity->note)?></textarea>
                </p>
            </div>
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
            <a href="<?= route('admin.orders.edit', ['orderNo' => $document->order->order_no]) ?>" class="no-btn-primary-outline --sm">취소</a>
            <button type="submit" class="no-btn-primary --sm">저장</button>
        </div>

    </div>
</form>
