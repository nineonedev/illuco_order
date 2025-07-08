<?php
/**
 * @var \App\Domains\Order\Entities\OrderDocument $document
 */
$entity = $document->product_request;
$order = $document->order;
$items = $order->items;

// ex) product_request
$type = $document->type;

// ① 언더스코어 → 하이픈
$type = str_replace('_', '-', $type);

// ② 각 단어 첫 글자 대문자
$type = ucwords($type, '-');

// 최종 파일명
$pdfName = "{$type}-{$document->document_no}.pdf";
?>

<div class="lh-doc" id="print-area" data-pdf-name="<?= $pdfName ?>" data-page="landscape">

    <div class="lh-header">
        <div class="logo">
            <img src="<?= asset_path('img/meta/logo-primary.svg') ?>" alt="ILLUCO">
        </div>

        <div class="lh-title">
            LH 생산의뢰서
        </div>
    </div>

    <div class="lh-info-no">
        <dl>
            <dt>문서번호</dt>
            <dd><?= e($entity->document->document_no ?? $document->document_no) ?></dd>
        </dl>
    </div>

    <table class="lh-info-table">
        <thead>
            <tr>
                <th style="width:16%;">국가</th>
                <th style="width:9%;">고객명</th>
                <th style="width:29%;">작성일</th>
                <th style="width:38%;">납기일</th>
                <th style="width:8%;">담당자</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= e($entity->country) ?></td>
                <td><?= e($entity->customer_name) ?></td>
                <td><?= e($entity->created_date) ?></td>
                <td><?= e($entity->delivery_date) ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table class="lh-items-table">
        <thead>
            <tr>
                <th style="width:4%;">No.</th>
                <th style="width:12%;">Product</th>
                <th style="width:9%;">Name</th>
                <th style="width:5%;">수량</th>
                <th style="width:10%;">Engraving</th>
                <th style="width:7%;">WD</th>
                <th style="width:7%;">PD</th>
                <th style="width:7%;">VD</th>
                <th style="width:10%;">Frame</th>
                <th style="width:21%;">시리얼넘버</th>
                <th style="width:8%;">박스번호</th>
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
                        $subProduct = $item->product->{$product->type};
                        $values = [
                            $no,
                            e($product->name),
                            e($subProduct->engraving_text ?? '-'),
                            e($item->quantity),
                            e($subProduct->engraving_text ?? '-'),
                            e($subProduct->working_distance ?? '-'),
                            e($subProduct->pd_total ?? '-'),
                            e($subProduct->vertex_distance ?? '-'),
                            e($subProduct->frame_type ?? '-'),
                            e($product->serial_no ?? '-'),
                            '' // 박스번호 수기로 기재 → 빈칸
                        ];
                    } else {
                        $values = array_fill(0, 11, '&nbsp;');
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

    </table>

    <div class="lh-bottom">
        <div class="lh-box-section">
            <table>
                <thead>
                    <tr>
                        <th>박스번호</th>
                        <th>무게</th>
                        <th>사이즈</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php
                            $no = $entity->{"box{$i}_no"} ?: '&nbsp;';
                            $weight = $entity->{"box{$i}_weight"} ?: '&nbsp;';
                            $size = $entity->{"box{$i}_size"} ?: '&nbsp;';
                        ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $weight ?></td>
                            <td><?= $size ?></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <div class="lh-note">
            <?= nl2br(e($entity->note)) ?>
        </div>
    </div>
</div>
