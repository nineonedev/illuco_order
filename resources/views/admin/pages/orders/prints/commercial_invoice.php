<?php

use App\Domains\Communication\Entities\SalesInfo;
use App\Domains\Communication\Repositories\SalesInfoRepository;
/**
 * @var \App\Domains\Order\Entities\OrderDocument $document
 */
$entity = $document->commercial_invoice;
$order = $document->order;
$items = $order->items;

// ex) commercial_invoice
$type = $document->type;

// ① 언더스코어 → 하이픈
$type = str_replace('_', '-', $type);

// ② 각 단어 첫 글자 대문자
$type = ucwords($type, '-');

// 최종 파일명
$pdfName = "{$type}-{$document->document_no}.pdf";

// items에서 sub total 자동 계산
$subTotal = 0;
$totalQuantity = 0;

foreach ($items as $item) {
    $subTotal += $item->total_price;
    $totalQuantity += $item->quantity;
}

// grand total 계산
$freightCharge = $entity->freight_charge ?? 0;
$grandTotal = $subTotal + $freightCharge;

?>

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

    <div class="commercial-meta-end">
        <table class="commercial-meta-table">
            <tr>
                <th>Ref. No.</th>
                <td><?= e($entity->ref_no) ?></td>
            </tr>
            <tr>
                <th>Date</th>
                <td><?= e($entity->invoice_date) ?></td>
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
    <div class="commercial-between">
        <div class="commercial-block">
            <p class="commercial-label">BILL TO:</p>
            <p><strong><?= e($entity->bill_to_name) ?></strong></p>
            <p><?= e($entity->bill_to_address) ?></p>
            <p>Tel.: <?= e($entity->bill_to_tel) ?></p>
            <p>Attn.: <?= e($entity->bill_to_attn) ?></p>
            <p>Email: <?= e($entity->bill_to_email) ?></p>
        </div>
        <div class="commercial-block">
            <div>
              <p class="commercial-label">SHIP TO:</p>
              <p>Currency: <?= e($entity->currency) ?? 'USD' ?></p>
            </div>
            <p><strong><?= e($entity->ship_to_name) ?></strong></p>
            <p><?= e($entity->ship_to_address) ?></p>
            <p>Tel.: <?= e($entity->ship_to_tel) ?></p>
            <p>Attn.: <?= e($entity->ship_to_attn) ?></p>
            <p>Email: <?= e($entity->ship_to_email) ?></p>
        </div>
    </div>

    <!-- Delivery Info -->
    <table class="commercial-info-table">
        <thead>
            <tr>
                <th style="width:20%;">Carrier</th>
                <th style="width:27.5%;">Estimated Delivery Date</th>
                <th style="width:12.5%;">Payment Terms</th>
                <th style="width:15%;">Price Terms</th>
                <th style="width:25%;">Country of Origin</th>
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
    <div class="commercial-items">
        <table class="commercial-items-table">
            <thead>
                <tr>
                    <th style="width:5%;">No.</th>
                    <th style="width:15%;">Product</th>
                    <th style="width:15%;">Model</th>
                    <th style="width:25%;">Description</th>
                    <th style="width:8%;">Qty</th>
                    <th style="width:7%;">Unit</th>
                    <th style="width:12.5%;">Unit Price</th>
                    <th style="width:12.5%;">Total</th>
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
                          $subProduct = $product->{$product->type} ?? null;

                          $values = [
                              $no,
                              e($product->name),
                              e($product->model ?? '-'),
                              e($product->description ?? '-'),
                              e($item->quantity),
                              'PC(S)',
                              '$'.e($item->unit_price),
                              '$'.e($item->total_price)
                          ];
                      } else {
                          $values = array_fill(0, 8, '&nbsp;');
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
                  <td colspan="4" class="right">Total quantity:</td>
                  <td class="right"><?= e($totalQuantity) ?></td>
                  <td class="right">PC(S)</td>
                  <td class="right">Sub Total:</td>
                  <td class="right">$ <?= number_format($subTotal, 2) ?></td>
              </tr>
              <tr>
                  <td colspan="6" class="right"></td>
                  <td class="right">Freight charge:</td>
                  <td class="right">$ <?= number_format($freightCharge, 2) ?></td>
              </tr>
              <tr>
                  <td colspan="6" class="right"></td>
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
        <p>HS Code: <?= e($entity->hs_code) ?></p>
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
    
</div>
