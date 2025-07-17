<?php 

use App\Domains\Communication\Enums\ClaimStatus;
use Composer\Autoload\ClassLoader;?>

<?php extend('layouts.admin'); ?>
<?php section('controller', 'claim') ?>
<?php section('action', 'show') ?>
<?php section('title', '클레임 상세보기') ?>

<?php section('content') ?>

<div class="no-form-container">
    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">클레임 상세보기</h1>
        </div>

        <?php if (can('claim.update')): ?>
        <div>
            <form action="<?= route('admin.claims.update', ['id' => $claim->id]) ?>" method="post" id="frm">
                <?= csrf_field() ?>
                <?= put_field() ?>

                <?php 
                    $props = [
                        'label' => '상태',
                        'name' => 'status',
                        'value' => $claim->status,
                        'options' => array_map(fn ($s) => [
                            'label' => ClaimStatus::labels()[$s],
                            'value' => $s,
                        ] , ClaimStatus::all()),
                    ]
                ?>
                <div 
                    class="no-form-control --md"
                    data-view-type="select"
                    data-view-props='<?= json_encode($props) ?>'
                ></div>

                <button type="submit" class="no-btn-primary --sm">수정</button>
                
            </form>
        </div>
        <?php endif; ?>

        <div class="no-claim-show">
            <div class="no-form-group">
                <h2 class="no-form-group-title no-body-lg">
                    제품 정보
                </h2>

                <div class="no-product-zone">
                    <?php if ($productImage): ?>
                    <figure class="no-product-zone-img">
                        <img src="<?= e($productImage) ?>" alt="<?= e($claim->product_name) ?>" />
                    </figure>
                    <?php endif; ?>
                    <div class="no-product-zone-content">
                        <div class="no-product-info">
                            <span class="no-product-info__label">제품명</span>
                            <span class="no-product-info__value"><?= e($claim->product_name) ?></span>
                        </div>
                        <div class="no-product-info">
                            <span class="no-product-info__label">모델명</span>
                            <span class="no-product-info__value"><?= e($claim->product_model) ?></span>
                        </div>
                        <div class="no-product-info">
                            <span class="no-product-info__label">코드</span>
                            <span class="no-product-info__value"><?= e($claim->product_code) ?></span>
                        </div>
                    </div>
                </div>

                <dl class="no-claim-show__list">
                    <div class="no-claim-show__item">
                        <dt>시리얼 번호</dt>
                        <dd><?= e($claim->product_serial_number) ?></dd>
                    </div>
                </dl>
            </div>

            <hr class="no-form-hr">
            <span class="no-form-control-space"></span>

            <div class="no-form-group">
                <h2 class="no-form-group-title no-body-lg">
                    문의 접수
                </h2>

                <dl class="no-claim-show__list">
                    <div class="no-claim-show__item">
                        <dt>제목</dt>
                        <dd><?= e($claim->title) ?></dd>
                    </div>

                    <div class="no-claim-show__item">
                        <dt>문의내용</dt>
                        <dd>
                            <div class="no-claim-show__content">
                                <?= $claim->content ?>
                            </div>
                        </dd>
                    </div>
                </dl>

                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php $file = $claim->fileattachment->get("attach_$i"); ?>
                    <?php if ($file): ?>
                        <div class="no-claim-show__file">
                            <span class="no-claim-show__file-label">첨부파일 <?= $i ?>:</span>
                            <a href="<?= $file->upload_path ?>" target="_blank" class="no-claim-show__file-link">
                                <?= e($file->original_name) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>

            <div class="no-form-action">
                <a href="<?= route('admin.claims.index') ?>" class="no-btn-primary-outline --sm">
                    <span>목록</span>
                </a>

                <?php if (can('claim.delete')): ?>
                    <form method="post" action="<?= route('admin.claims.destroy', ['id' => $claim->id]) ?>" style="display:inline;">
                        <?= csrf_field() ?>
                        <?= method_field('DELETE') ?>
                        <button type="submit" class="no-btn-error-outline --sm" onclick="return confirm('삭제하시겠습니까?')">
                            <span>삭제</span>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php end_section() ?>

<?php section('script') ?>
<?php end_section() ?>
