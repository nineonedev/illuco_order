<?php
/**
 * @var \App\Domains\Order\Entities\OrderDocument $document
 */
$entity = $document->proforma_invoice;
$order = $document->order;
$items = $order->items;
?>

<div class="proforma-invoice">
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
            <p>Company: <?= e($entity->buyer_name) ?></p>
            <p>Address: <?= e($entity->buyer_address) ?></p>
            <p>Tel.: <?= e($order->orderer_phone ?: '-') ?></p>
            <p>Attn.: <?= e($order->orderer_name) ?> &lt;<?= e($order->orderer_email) ?>&gt;</p>
        </div>

        <table class="ref-table">
            <tr>
                <td>Ref. No.</td>
                <td><?='JP-CR-2408' //e($entity->invoice_no) ?></td>
            </tr>
            <tr>
                <td>Date</td>
                <td><?= '1-Aug-24' // e($entity->invoice_date) ?></td>
            </tr>
            <tr>
                <td>P.O. No.</td>
                <td>Aug. 1, 2024</td>
            </tr>
        </table>
    </div>

    <div class="table-section">
        <small class="--tar">
            <p><u>Currency : USD</u></p>
            <p>* Country of origin :  Republic of Korea</p>
        </small>
        <table class="meta-table" style="width: 100%;">
            <tr>
                <th style="width: 20%;">Sales Person</th>
                <th style="width: 15%;">Tel. No./Email</th>
                <th style="width: 25%;">Estimated Date of Delivery</th>
                <th style="width: 10%;" colspan="2">Price Terms</th>
                <th style="width: 15%;">Price Terms</th>
                <th style="width: 15%;">Shipment by</th>
            </tr>
            <tr>
                <td>JY</td>
                <td>+82 70 4922 7441 <br> kms@illuco.co.kr</td>
                <td>Within a week after payment</td>
                <td colspan="2">EXW Korea</td>
                <td>100% T/T in advance</td>
                <td>Air (DHL)</td>
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
                    <th style="width: 25%;">Description</th>
                    <th style="width: 5%;">Qty</th>
                    <th style="width: 5%;">PC(S)</th>
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
                    <td colspan="3" class="empty"></td>
                    <td colspan="1" class="right bold">Total</td>
                    <td colspan="1" class="right"><?= $totalQty ?></td>
                    <td colspan="1" class="right">PC(S)</td>
                    <td class="right">Sub Total:</td>
                    <td class="right bold">$ <?= number_format($totalAmount, 2) ?></td>
                </tr>
                <tr>
                    <td colspan="6" class="empty"></td>
                    <td>Freight Charge</td>
                    <td class="right">$ -</td>
                </tr>
                <tr>
                    <td colspan="6" class="empty"></td>
                    <td  class="right bold">Total:</td>
                    <td class="right bold">$ <?= number_format($totalAmount, 2) ?></td>
                </tr>
            </tfoot>
        </table>
  </div>

  <div class="bank-details">
    <p><strong>Bank Details</strong></p>
    <p>Beneficiary: ILLUCO</p>
    <p>Bank Name: Shinhan Bank Korea, Yeouido Branch</p>
    <p>Bank Address: Shinsong Center Building, 25-12 Yeouido-dong, Yeoungdeungpo-gu, 07327 Seoul, Korea</p>
    <p>Swift Code: SHBKKRSE Account No.: 180-007-045998</p>
    <p>Details of Charges: OUR</p>
  </div>

  <div class="sign-zone">
    <p class="sign-text">Supplied by</p>
    <div class="sign-img">
        <img src="<?= asset_path('img/meta/sign.png') ?>" alt="ILLUCO CO., LTD">
    </div>
  </div>

  <div class="footer">
    <p>ILLUCO Co., Ltd. 102-304 SK Ventium, #166 Gosan-ro, Gunpo-si, Gyeonggi-do, Korea</p>
    <p>www.illuco.co.kr Tel. +82 31 429 8825 Fax. +82 31 429 8826 info@illuco.co.kr</p>
  </div>
</div>
