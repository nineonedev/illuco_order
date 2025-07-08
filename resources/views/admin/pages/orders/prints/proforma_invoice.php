<?php
/**
 * @var \App\Domains\Order\Entities\OrderDocument $document
 */
$entity = $document->proforma_invoice;
$order = $document->order;
$items = $order->items;
$totalAmount = array_reduce(
    $items,
    fn($carry, $item) => $carry + $item->total_price,
    0
);
$totalQty = array_sum(array_map(fn($i) => $i->quantity, $items));
$freightCharge = (float)($entity->freight_charge ?? 0);
$grandTotal = $totalAmount + $freightCharge;


// ex) proforma_invoice
$type = $document->type;

// ① 언더스코어 → 하이픈
$type = str_replace('_', '-', $type);

// ② 각 단어 첫 글자 대문자
$type = ucwords($type, '-');

// 최종 파일명
$pdfName = "{$type}-{$document->document_no}.pdf";

?>

<div class="no-order-doc-edit no-preview-container" id="print-area" data-pdf-name="<?=$pdfName?>"> 
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
      <p>Company: <?= e($entity->buyer_name) ?></p>
      <p>Address: <?= e($entity->buyer_address) ?></p>
      <p>Tel.: <?= e($entity->buyer_tel) ?></p>
      <p>Attn.: <?= e($entity->buyer_attn) ?></p>
      <p>Email: <?= e($entity->buyer_email) ?></p>
    </div>

    <table class="ref-table">
      <tr>
        <td>Ref. No.</td>
        <td><?= e($entity->document_no) ?></td>
      </tr>
      <tr>
        <td>Date</td>
        <td><?= e($entity->invoice_date) ?></td>
      </tr>
      <tr>
        <td>P.O. No.</td>
        <td><?= e($entity->purchase_order_no) ?></td>
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
        <th style="width:17%;">Sales Person</th>
        <th style="width:12%;">Tel. No./Email</th>
        <th style="width:30%;">Estimated Date of Delivery</th>
        <th style="width:14%;" colspan="2">Price Terms</th>
        <th style="width:13%;">Payment Terms</th>
        <th style="width:14%;">Shipment by</th>
      </tr>
      <tr>
        <td><?= e($entity->salesperson_name) ?></td>
        <td>
          <?= e($entity->salesperson_tel) ?><br>
          <?= e($entity->salesperson_email) ?>
        </td>
        <td><?= e($entity->estimated_date_of_delivery) ?></td>
        <td colspan="2"><?= e($entity->price_terms) ?></td>
        <td><?= e($entity->payment_terms) ?></td>
        <td><?= e($entity->shipment_by) ?></td>
      </tr>
    </table>
  </div>

  <!-- Items Table -->
  <div class="table-section">
    <table class="item-table">
      <thead>
        <tr>
          <th style="width:5%;">No.</th>
          <th style="width:12%;">Product</th>
          <th style="width:12%;">Model</th>
          <th style="width:30%;">Description</th>
          <th style="width:6%;">Qty</th>
          <th style="width:8%;">PC(S)</th>
          <th style="width:13%;">Unit Price</th>
          <th style="width:14%;">Total</th>
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
                        $no,
                        e($product->name),
                        e($product->model),
                        e($product->description),
                        e($item->quantity),
                        'PC(S)',
                        '$ ' . number_format($item->unit_price, 2),
                        '$ ' . number_format($item->total_price, 2)
                    ];
                } else {
                    $values = array_fill(0, 8, '&nbsp;');
                }
            ?>
            <tr>
                <?php foreach ($values as $value): ?>
                    <td class="<?= is_numeric(str_replace(['$', ',', '.', '&nbsp;'], '', strip_tags($value))) ? 'right' : '' ?>">
                        <?= $value ?>
                    </td>
                <?php endforeach; ?>
            </tr>
            <?php if ($item) $no++; ?>
        <?php endfor; ?>
    </tbody>

      <tfoot>
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
          <td class="right">$ <?= number_format($freightCharge, 2) ?></td>
        </tr>
        <tr>
          <td colspan="6" class="empty"></td>
          <td class="right bold">Total:</td>
          <td class="right bold">$ <?= number_format($grandTotal, 2) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- Bank Details -->
  <div class="bank">
    
      <p>HS Code: <?= e($entity->hs_code) ?></p>
      
      <div class="bank-details">
        <p><strong>Bank Details</strong></p>
      <p>Beneficiary: <?= e($entity->bank_beneficiary) ?></p>
      <p>Bank Name: <?= e($entity->bank_name) ?></p>
      <p>Bank Address: <?= e($entity->bank_address) ?></p>
      <p>Swift Code: <?= e($entity->bank_swift_code) ?></p>
      <p>Account No.: <?= e($entity->bank_account_no) ?></p>
    </div>
  </div>

  <div class="bottom">
    <div class="notes">
        Note:<br><?= nl2br(e($entity->note)) ?>
    </div>
    <div class="sign-zone">
        <p class="sign-text">Supplied by</p>
        <div class="sign-img">
        <img src="<?= asset_path('img/meta/sign.png') ?>" alt="ILLUCO CO., LTD">
        </div>
    </div>
  </div>
</div>
