<?php
/**
 * @var \App\Domains\Order\Entities\OrderDocument $document
 */
$entity = $document->commercial_invoice;
$order = $document->order;
$items = $order->items;
?>

<div class="proforma-invoice">
  <div class="invoice-header">
    <div class="logo">
      <img src="<?= asset_path('img/meta/logo-primary.svg') ?>" alt="ILLUCO">
    </div>
    <div class="invoice-title">
      <h1>Commercial Invoice</h1>
    </div>
  </div>

  <div class="invoice-between">
    <div class="buyer-shipping-box">
        <div class="buyer-box">
            <p><strong>BILL TO:</strong></p>
            <div class="buyer-content">
                <p><strong><?= e($entity->buyer_name) ?></strong></p>
                <p><?= nl2br(e($entity->address)) ?></p>
                <p>USA</p>
                <p>Tel.: <?= e($order->orderer_phone ?: '-') ?></p>
                <p>Attn.: <?= e($order->orderer_name) ?></p>
                <p><?= e($order->orderer_email) ?></p>
            </div>
        </div>

        <div class="ship-box">
            <p><strong>SHIP TO:</strong></p>
            <div class="ship-content">
                <p><strong><?= e($entity->buyer_name) ?></strong></p>
                <p><?= nl2br(e($entity->address)) ?></p>
                <p>USA</p>
                <p>Tel.: <?= e($order->orderer_phone ?: '-') ?></p>
                <p>Attn.: <?= e($order->orderer_name) ?></p>
                <p><?= e($order->orderer_email) ?></p>
            </div>
        </div>
    </div>

    <table class="ref-table">
        <tr>
            <td>Ref. No.</td>
            <td><?= 'US-CRL-05' // e($entity->invoice_no) ?></td>
        </tr>
        <tr>
            <td>Date</td>
            <td><?= '23-Feb-24' // e($entity->invoice_date) ?></td>
        </tr>
        <tr>
            <td>P/I No.</td>
            <td><?= 'US-CRL-2402' ?></td>
        </tr>
        <tr>
            <td>P/O No.</td>
            <td>N/A</td>
        </tr>
    </table>
    </div>

  <div class="table-section">
    <small class="--tar">
      <p><u>Currency : USD</u></p>
    </small>
    <table class="meta-table" style="width: 100%;">
      <tr>
        <th style="width: 20%;">Carrier</th>
        <th style="width: 15%;">Estimated Delivery Date</th>
        <th style="width: 20%;">Payment Terms</th>
        <th style="width: 15%;">Price Terms</th>
        <th style="width: 30%;">Country of Origin</th>
      </tr>
      <tr>
        <td>DHL</td>
        <td>On or about 23rd Feb, 2024</td>
        <td>T/T in advance</td>
        <td>DAP</td>
        <td>Republic of Korea</td>
      </tr>
    </table>
  </div>

  <div class="table-section">
    <table class="item-table">
      <thead>
        <tr>
          <th style="width: 5%;">No.</th>
          <th style="width: 15%;">Product</th>
          <th style="width: 15%;">Model</th>
          <th style="width: 20%;">Description</th>
          <th style="width: 8%;">Qty</th>
          <th style="width: 7%;">PC(s)</th>
          <th style="width: 15%;">Unit Price</th>
          <th style="width: 15%;">Total</th>
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
          <td class="right">PC(s)</td>
          <td class="right">$ <?= number_format($item->unit_price, 2) ?></td>
          <td class="right">$ <?= number_format($total, 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <?php
        $totalQty = array_sum(array_map(fn($i) => $i->quantity, $items));
        $freightCharge = 940.00;
        $grandTotal = $totalAmount + $freightCharge;
        ?>
        <tr>
          <td colspan="3" class="empty"></td>
          <td class="right bold">Total quantity:</td>
          <td class="right"><?= $totalQty ?></td>
          <td class="right">PC(s)</td>
          <td class="right">Sub Total:</td>
          <td class="right bold">$ <?= number_format($totalAmount, 2) ?></td>
        </tr>
        <tr>
          <td colspan="6" class="empty"></td>
          <td class="right">Freight charge:</td>
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

  <div class="bank-details">
    <p class="">
        <strong>HS Code:</strong> 
        <div>
            <span>9013.80.2000</span>
            <span>8513.10.2010</span>
        </div>
    </p>
  </div>

  <div class="sign-zone">
    <p class="sign-text">Supplied by</p>
    <div class="sign-img">
      <img src="<?= asset_path('img/meta/sign.png') ?>" alt="ILLUCO CO., LTD">
    </div>
  </div>
</div>
