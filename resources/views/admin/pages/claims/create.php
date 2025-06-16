<?php extend('layouts.admin'); ?>

<?php section('title') ?>
클레임 생성
<?php end_section() ?>

<?php section('content') ?>
<div class="no-form-container">
    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">클레임 생성</h1>
        </div>
        
        <form method="post" enctype="multipart/form-data" action="<?= route('admin.claims.store') ?>">
            <div class="no-form-group">
                <h2 class="no-form-group-title no-body-lg">
                    제품 정보
                </h2>

                <div class="no-form-control --md">
                    <label for="product_id" class="no-form-control-inner">
                        <input type="text" name="product_id" id="product_id" class="no-form-control-input" value="IDS3100" placeholder="">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">제품군 선택</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --md">
                    <label for="prod_model" class="no-form-control-inner">
                        <input type="text" name="prod_model" id="prod_model" class="no-form-control-input" value="iMag 3.5x-R" placeholder="">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">제품 모델 번호</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --md">
                    <label for="prod_desc" class="no-form-control-inner">
                        <input type="text" name="prod_desc" id="prod_desc" class="no-form-control-input" value="고배율 정밀 진료용 헤드마운트 루페 (3.5배율, 오른쪽 LED 부착형)" placeholder="">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">제품 설명</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 제목 -->
                <div class="no-form-control --md">
                    <label for="title" class="no-form-control-inner">
                        <input type="text" name="title" id="title" class="no-form-control-input" value="IMAG35R" placeholder="" readonly>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">코드</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 제목 -->
                <div class="no-form-control --md">
                    <label for="title" class="no-form-control-inner">
                        <input type="text" name="title" id="title" class="no-form-control-input" value="Eric McDonald" placeholder="" readonly>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">고객</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 제목 -->
                <div class="no-form-control --md">
                    <label for="title" class="no-form-control-inner">
                        <input type="text" name="title" id="title" class="no-form-control-input" value="IM20240515-0037" placeholder="" readonly>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">시리얼 번호</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
            </div>
            
            <hr class="no-form-hr">
            <span class="no-form-control-space"></span>

            <div class="no-form-group">
                <h2 class="no-form-group-title no-body-lg">
                    문의 접수
                </h2>

                <!-- 제목 -->
                <div class="no-form-control --md">
                    <label for="title" class="no-form-control-inner">
                        <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">제목</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 내용 -->
                <div class="no-form-base --md">
                    <label for="content" class="no-form-base-label">
                        <span>내용</span>
                    </label>
                    <textarea name="content" id="content" data-text-editor class="no-form-base-input"></textarea>
                    <span class="no-form-control-space"></span>
                </div>


                <!-- 첨부파일 -->
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                <div class="no-form-control no-form-file">
                    <label for="file_attachment_<?=$i?>" class="no-form-control-inner">
                        <input 
                            type="file" 
                            name="file_attachment_<?=$i?>" 
                            id="file_attachment_<?=$i?>" 
                            class="no-form-control-input">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">첨부파일<?=$i?></legend>
                        </fieldset>
                        <button class="no-form-file-input" type="button" data-input-file>
                            <div class="no-form-file-icon">
                                <i class="fa-light fa-paperclip-vertical"></i>
                            </div>
                            <span class="no-form-file-text" data-input-file-text>선택된 파일 없음</span>
                            <span class="no-form-file-button-text">파일선택</span>
                        </button>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                <?php endfor; ?>

            </div>

            <!-- 액션 버튼 -->
            <div class="no-form-action">
                <a href="<?= route('admin.claims.index') ?>" class="no-btn-primary-outline --sm">
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
