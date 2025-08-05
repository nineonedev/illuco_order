<?php
/**
 * @var \App\Domains\Order\Entities\OrderDocument $document
 */
$entity = $document->proforma_invoice;
$order = $document->order;
$items = $order->items;
$totalQty = array_sum(array_map(fn($i) => $i->quantity, $items));
$totalAmount = array_sum(array_map(fn($i) => $i->total_price, $items));
$freightCharge = (float)($entity->freight_charge ?? 0);
$grandTotal = $totalAmount + $freightCharge;

$type = str_replace('_', '-', $document->type);
$type = ucwords($type, '-');
$pdfName = "{$type}-{$document->document_no}.pdf";
?>

<div class="document-container">
<form class="document-editor no-document__edit" id="frm" action="<?= route('admin.order_documents.update', ['id' => $entity->id]) ?>" method="POST">
<?= csrf_field() ?>
<?= method_field('PUT') ?>

<div class="no-order-doc-edit" id="print-area" data-pdf-name="<?= $pdfName ?>"> 
  <div class="invoice-header">
    <div class="logo">
      <img src="<?= asset_path('img/meta/logo-primary.svg') ?>" alt="ILLUCO">
    </div>
    <div class="invoice-title">
      <h1>Proforma Invoice</h1>
    </div>
  </div>

  <div class="invoice-between">
    <div class="buyer-section">
      <p><strong>Buyer</strong></p>
      <p>Company: <input type="text" name="buyer_name" value="<?= e($entity->buyer_name) ?>"></p>
      <p>Address: <input type="text" name="buyer_address" value="<?= e($entity->buyer_address) ?>"></p>
      <p>Tel.: <input type="text" name="buyer_tel" value="<?= e($entity->buyer_tel) ?>"></p>
      <p>Attn.: <input type="text" name="buyer_attn" value="<?= e($entity->buyer_attn) ?>"></p>
      <p>Email: <input type="email" name="buyer_email" value="<?= e($entity->buyer_email) ?>"></p>
    </div>

    <table class="ref-table">
      <tr>
        <td>Ref. No.</td>
        <td><input type="text" name="document_no" value="<?= e($entity->document_no) ?>"></td>
      </tr>
      <tr>
        <td>Date</td>
        <td><input type="date" name="invoice_date" value="<?= e($entity->invoice_date) ?>"></td>
      </tr>
      <tr>
        <td>P.O. No.</td>
        <td><input type="text" name="purchase_order_no" value="<?= e($entity->purchase_order_no) ?>"></td>
      </tr>
    </table>
  </div>

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
        <td><input type="text" name="salesperson_name" value="<?= e($entity->salesperson_name) ?>"></td>
        <td>
          <input type="text" name="salesperson_tel" value="<?= e($entity->salesperson_tel) ?>"><br>
          <input type="email" name="salesperson_email" value="<?= e($entity->salesperson_email) ?>">
        </td>
        <td><input type="text" name="estimated_date_of_delivery" value="<?= e($entity->estimated_date_of_delivery) ?>"></td>
        <td colspan="2"><input type="text" name="price_terms" value="<?= e($entity->price_terms) ?>"></td>
        <td><input type="text" name="payment_terms" value="<?= e($entity->payment_terms) ?>"></td>
        <td><input type="text" name="shipment_by" value="<?= e($entity->shipment_by) ?>"></td>
      </tr>
    </table>
  </div>

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
        <?php $no = 1; ?>
        <?php for ($i = 0; $i < 16; $i++): ?>
          <?php $item = $items[$i] ?? null; ?>
          <tr>
            <td><?= $item ? $no++ : '&nbsp;' ?></td>
            <td><?= $item ? e($item->product->name) : '&nbsp;' ?></td>
            <td><?= $item ? e($item->product->model) : '&nbsp;' ?></td>
            <td><?= $item ? e($item->product->description) : '&nbsp;' ?></td>
            <td class="right"><?= $item ? e($item->quantity) : '&nbsp;' ?></td>
            <td class="right">PC(S)</td>
            <td class="right"><?= $item ? '$ ' . number_format($item->unit_price, 2) : '&nbsp;' ?></td>
            <td class="right"><?= $item ? '$ ' . number_format($item->total_price, 2) : '&nbsp;' ?></td>
          </tr>
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
          <td class="right">$ <input type="number" name="freight_charge" step="0.01" value="<?= e($entity->freight_charge) ?>"></td>
        </tr>
        <tr>
          <td colspan="6" class="empty"></td>
          <td class="right bold">Total:</td>
          <td class="right bold">$ <?= number_format($grandTotal, 2) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <div class="bank">
    <p>HS Code: <input type="text" name="hs_code" value="<?= e($entity->hs_code) ?>"></p>
    <div class="bank-details">
      <p><strong>Bank Details</strong></p>
      <p>Beneficiary: <input type="text" name="bank_beneficiary" value="<?= e($entity->bank_beneficiary) ?>"></p>
      <p>Bank Name: <input type="text" name="bank_name" value="<?= e($entity->bank_name) ?>"></p>
      <p>Bank Address: <input type="text" name="bank_address" value="<?= e($entity->bank_address) ?>"></p>
      <p>Swift Code: <input type="text" name="bank_swift_code" value="<?= e($entity->bank_swift_code) ?>"></p>
      <p>Account No.: <input type="text" name="bank_account_no" value="<?= e($entity->bank_account_no) ?>"></p>
    </div>
  </div>

  <div class="bottom">
    <div class="notes">
      Note:<br>
      <textarea name="note" rows="5"><?= e($entity->note) ?></textarea>
    </div>
    <div class="sign-zone">
      <p class="sign-text">Supplied by</p>
      <div class="sign-img">
        <img src="<?= asset_path('img/meta/sign.png') ?>" alt="ILLUCO CO., LTD">
      </div>
    </div>
  </div>

  <div class="no-form-action">
    <a href="<?= route('admin.orders.edit', ['orderNo' => $order->order_no]) ?>" class="no-btn-primary-outline --sm" data-action="cancel">취소</a>
    <button type="submit" class="no-btn-primary --sm">저장</button>
  </div>
</div>

</form>
</div>