<?php extend('layouts.admin'); ?>
<?php section('controller', 'notice') ?>
<?php section('action', 'create') ?>
<?php section('title', '공지사항 생성') ?>


<?php section('content') ?>

<div class="no-form-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">공지사항 생성</h1>
        </div>
        <!-- Head -->
        
        <div id="form-hook">
            <form method="post" enctype="multipart/form-data" action="<?= route('admin.notices.store') ?>">
                <div class="no-form-group">
                    <div class="no-form-control --md">
                        <label for="title" class="no-form-control-inner">
                            <input type="text" name="title" id="title" class="no-form-control-input" placeholder="" >
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">제목</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>
                    <!-- FormControl -->
                    
                    <!-- <div id="editorjs" class="no-form-control-input" style="min-height: 300px;"></div> -->

                    
                    <div class="no-form-base --md">
                        <label for="content" class="no-form-base-label">
                            <span>내용</span>
                        </label>
                        <textarea name="content" id="content" data-text-editor class="no-form-base-input"></textarea>
                        <span class="no-form-control-space"></span>
                    </div>
                    <!-- FormControl -->

                    <div class="no-form-checkbox --sm">
                        <label for="is_notice" class="no-form-checkbox-pointer">
                            <input type="checkbox" name="is_notice" id="is_notice" class="no-form-checkbox-input">
                            <div class="no-form-checkbox-ripple">
                                <span class="no-form-checkbox-box">
                                    <div class="no-form-checkbox-icon">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </span>
                            </div>
                            <span class="no-form-checkbox-text">공지로 등록</span>
                        </label>
                        <p class="no-form-checkbox-helper-text">해당 공지글을 목록의 최상단에 위치시킵니다.</p>
                    </div>

                    <div class="no-form-checkbox --sm">
                        <label for="is_visible" class="no-form-checkbox-pointer">
                            <input type="checkbox" name="is_visible" id="is_visible" class="no-form-checkbox-input" checked>
                            <div class="no-form-checkbox-ripple">
                                <span class="no-form-checkbox-box">
                                    <div class="no-form-checkbox-icon">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </span>
                            </div>
                            <span class="no-form-checkbox-text">노출여부</span>
                        </label>
                        <p class="no-form-checkbox-helper-text">해당 공지글을 노출시킵니다.</p>
                        <span class="no-form-control-space"></span>
                        <span class="no-form-control-space"></span>
                    </div>
                    
                    <div id="file-hook"></div>
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <div class="no-form-control no-form-file">
                        <label for="file_attachable[]" class="no-form-control-inner no-form-file-inner">
                            <input 
                                type="file" 
                                name="file_attachable[]" 
                                id="file_attachable[]" 
                                class="no-form-control-input" 
                                placeholder="" 
                            >
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">첨부파일<?=$i?></legend>
                            </fieldset>
                            <button class="no-form-file-input" type="button">
                                <div class="no-form-file-icon">
                                    <i class="fa-light fa-paperclip-vertical"></i>
                                </div>
                                <span class="no-form-file-text"-text>선택된 파일 없음</span>
                                <span class="no-form-file-button-text">파일선택</span>
                            </button>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>
                    <!-- FormControl -->
                    <?php endfor; ?>

                </div>
                
                <div class="no-form-action">
                    <a href="<?=route('admin.notices.index')?>" class="no-btn-primary-outline --sm">
                        <span>취소</span>
                    </a>
                    <button type="submit" class="no-btn-primary --sm">
                        <span>저장</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
    <!-- Row -->
</div>


<?php end_section() ?>

<?php section('script') ?>

<script>
    
</script>
<?php end_section() ?>