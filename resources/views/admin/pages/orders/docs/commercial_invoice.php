<?php

use App\Domains\Communication\Entities\SalesInfo;
use App\Domains\Communication\Repositories\SalesInfoRepository;
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

        <?php
            // 주문 기준으로 고객-사용자-대리점까지 선로딩 (N+1 방지)
            $order->load(['customer.user.dealer']);

            /** @var \App\Domains\User\Entities\User|null $dealerUser */
            $dealerUser = $order->customer && $order->customer->user ? $order->customer->user : null;
            /** @var \App\Domains\User\Entities\Dealer|null $dealer */
            $dealer = ($dealerUser && isset($dealerUser->dealer)) ? $dealerUser->dealer : null;

            // ===== BILL TO (Dealer 우선, 엔티티 저장값 > DealerUser/Dealer > Customer)
            $billName    = $entity->bill_to_name    ?: ($dealerUser->name ?? $order->customer->name ?? '');
            $billAddress = $entity->bill_to_address ?: ($dealer->address ?? $order->customer->address ?? '');
            $billTel     = $entity->bill_to_tel     ?: ($dealerUser->phone ?? $order->customer->phone ?? '');
            $billEmail   = $entity->bill_to_email   ?: ($dealerUser->email ?? $order->customer->email ?? '');
            $billAttn    = $entity->bill_to_attn    ?: ($dealerUser->name ?? '');

            // ===== SHIP TO (원하면 BILL TO와 동일 기본값 사용)
            $shipName    = $entity->ship_to_name    ?: $billName;
            $shipAddress = $entity->ship_to_address ?: $billAddress;
            $shipTel     = $entity->ship_to_tel     ?: $billTel;
            $shipEmail   = $entity->ship_to_email   ?: $billEmail;
            $shipAttn    = $entity->ship_to_attn    ?: $billAttn;

            // 통화 기본값
            $currency    = $entity->currency ?: 'USD';
        ?>

        <!-- BILL TO / SHIP TO -->
        <div class="commercial-between">
        <div class="commercial-block">
            <p class="commercial-label">BILL TO:</p>
            <p><strong><input type="text" name="bill_to_name"    value="<?= e($billName) ?>" placeholder="name"></strong></p>
            <p><input type="text"   name="bill_to_address" value="<?= e($billAddress) ?>" placeholder="address"></p>
            <p>Tel.:  <input type="text"   name="bill_to_tel"     value="<?= e($billTel) ?>"></p>
            <p>Attn.: <input type="text"   name="bill_to_attn"    value="<?= e($billAttn) ?>"></p>
            <p>Email: <input type="email"  name="bill_to_email"   value="<?= e($billEmail) ?>"></p>
        </div>

        <div class="commercial-block">
            <p class="commercial-label">SHIP TO:</p>
            <p>Currency: <input type="text" name="currency" value="<?= e($currency) ?>" placeholder="USD"></p>
            <p><strong><input type="text" name="ship_to_name"    value="<?= e($shipName) ?>" placeholder="name"></strong></p>
            <p><input type="text"   name="ship_to_address" value="<?= e($shipAddress) ?>" placeholder="address"></p>
            <p>Tel.:  <input type="text"   name="ship_to_tel"     value="<?= e($shipTel) ?>"></p>
            <p>Attn.: <input type="text"   name="ship_to_attn"    value="<?= e($shipAttn) ?>"></p>
            <p>Email: <input type="email"  name="ship_to_email"   value="<?= e($shipEmail) ?>"></p>
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
                            $ <input type="number" step="0.01" name="freight_charge" value="<?= (float)$freightCharge ?>">
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

        
        <?php 
            $info = SalesInfoRepository::make()->query()->first(); 
            $info = $info ?? SalesInfo::make();
        ?>

        <!-- Footer -->
        <div class="commercial-footer">
            <p>HS Code: <input type="text" name="hs_code" value="<?= e($entity->hs_code) ?>"></p>
                            
            <?php if ($order->use_remarks): ?>
            <p class="no-order-doc__remarks">
                <u>Remarks</u><br> 
                <span><?= $info->remarks ?? '' ?></span>
            </p>
            <?php endif; ?>
        </div>

        <div class="no-order-doc__bottom">
            <footer class="no-order-doc__footer">
                <span class="--full"><?= $info->company_name?></span>
                <span class="--full"><?= $info->company_address?></span>
                <span><?= $info->company_website?></span>
                <span><?= $info->company_tel?></span>
                <span><?= $info->company_fax?></span>
                <span><?= $info->company_email?></span>
            </footer>
            <div class="commercial-sign">
                <p>Supplied by</p>
                <img src="<?= asset_path('img/meta/sign.png') ?>" alt="ILLUCO CO., LTD">
            </div>
        </div>

        <div class="no-form-action">
            <a href="<?= route('admin.orders.edit', ['orderNo' => $order->order_no]) ?>" class="no-btn-primary-outline --sm">취소</a>
            <button type="submit" class="no-btn-primary --sm">저장</button>
        </div>
    </div>
</form>
