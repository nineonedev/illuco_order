<?php


?>
<?php extend('layouts.admin'); ?>
<?php section('controller', 'claim') ?>
<?php section('action', 'create') ?>
<?php section('title', '클레임 생성') ?>

<?php section('content') ?>

<div class="no-form-container">
    <div class="no-page-row">
        <div class="no-page-head__between">
            <h1 class="no-heading-sm">클레임 생성</h1>
            <button type="submit" class="no-btn-success-outline --xs" id="refresh-btn">
                <span>초기화</span>
            </button>
        </div>
        
        <form method="post" id="frm" enctype="multipart/form-data" action="<?= route('admin.claims.store') ?>">
            <div class="no-form-group">
                <h2 class="no-form-group-title no-body-lg">
                    제품 정보
                </h2>

                <?php 
                    $props = [
                        'label' => '제품검색',
                    ];
                ?>
                <div
                    id="search-product"
                    data-view-props='<?=json_encode($props)?>'
                ></div>
                <span class="no-form-control-space"></span>
                <div id="product-zone"></div>
                <span class="no-form-control-space"></span>
            </div>

            <div class="no-form-group">
                <h2 class="no-form-group-title no-body-lg">
                    고객 정보
                </h2>

                <?php 
                    $props = [
                        'label' => '고객검색',
                    ];
                ?>
                <div
                    id="search-customer"
                    data-view-props='<?=json_encode($props)?>'
                ></div>
                <span class="no-form-control-space"></span>
                <div id="customer-zone"></div>
                <span class="no-form-control-space"></span>
            </div>
            
            <hr class="no-form-hr">
            <span class="no-form-control-space"></span>

            <div class="no-form-group">
                <h2 class="no-form-group-title no-body-lg">
                    문의 접수
                </h2>

                <!-- <div class="no-form-control --md">
                    <label for="order_no" class="no-form-control-inner">
                        <input type="text" name="order_no" id="order_no" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">주문번호</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-helper-text">주문관련 문의일 경우 주문번호를 입력해주세요.</span>
                    <span class="no-form-control-space"></span>
                </div> -->

                <div class="no-form-control --md">
                    <label for="product_serial_number" class="no-form-control-inner">
                        <input type="text" name="product_serial_number" id="product_serial_number" class="no-form-control-input" required  placeholder="">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">시리얼 번호</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 제목 -->
                <div class="no-form-control --md">
                    <label for="title" class="no-form-control-inner">
                        <input type="text" name="title" id="title" class="no-form-control-input" placeholder="" required>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">제목</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 내용 -->
                <?php 
                    $props = [
                        "name" => "content",
                        "label" => "문의내용",
                    ];
                ?>
                <div 
                    data-view-type="editor"
                    data-view-props='<?=json_encode($props, true) ?>'    
                ></div>


                <div data-view-type="file" data-view-props='{"file_key": "attach_1"}'></div>
                <div data-view-type="file" data-view-props='{"file_key": "attach_2"}'></div>
                <div data-view-type="file" data-view-props='{"file_key": "attach_3"}'></div>
                <div data-view-type="file" data-view-props='{"file_key": "attach_4"}'></div>
                <div data-view-type="file" data-view-props='{"file_key": "attach_5"}'></div>

            </div>

            <!-- 액션 버튼 -->
            <div class="no-form-action">
                <a href="<?= route('admin.claims.index') ?>" data-action="cancel" class="no-btn-primary-outline --sm">
                    <span>취소</span>
                </a>
                <button type="submit" class="no-btn-primary --sm">
                    <span>저장</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php end_section() ?>

<?php section('script') ?>
<?php end_section() ?>
