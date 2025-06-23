<?php 

use App\Domains\Communication\Entities\Notice;

?>

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
        
        <form  
            data-component-props
            method="post" 
            id="frm" 
            enctype="multipart/form-data" 
            action="<?= route('admin.notices.store') ?>"
        >
            <?= csrf_field() ?>
            
            <div class="no-form-inner">
                <div class="no-form-group">
                    <div class="no-form-control --md">
                        <label for="title" class="no-form-control-inner">
                            <input type="text" name="title" id="title" class="no-form-control-input" placeholder="" required>
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">제목</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>
                    <!-- FormControl -->

                    <div class="no-form-control --md">
                        <label for="status" class="no-form-control-inner">
                            <select name="status" id="status" class="no-form-control-input --select">
                                <option value="">-- 상태 선택 --</option>
                                <option value="<?= Notice::STATUS_DRAFT ?>">
                                    작성중
                                </option>
                                <option value="<?= Notice::STATUS_PUBLISHED ?>">
                                    게시중
                                </option>
                                <option value="<?= Notice::STATUS_SCHEDULED ?>">
                                    예약게시
                                </option>
                                <option value="<?= Notice::STATUS_ARCHIVED ?>">
                                    보관됨
                                </option>
                            </select>
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">상태</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>
                    
                    <div class="no-form-flex">
                        <div class="no-form-control --md" data-component-type="datetime" data-component-props='{"name": "visible_from", "label": "노출 시작일"}'>
                            <!-- <label for="visible_from" class="no-form-control-inner">
                                <input type="datetime-local" name="visible_from" id="visible_from" class="no-form-control-input" placeholder="" >
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">노출 시작일</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span> -->
                        </div>
                        <!-- FormControl -->

                        <div class="no-form-control --md" data-component-type="datetime" data-component-props='{"name": "visible_to", "label": "노출 종료일"}'>
                            <!-- <label for="visible_to" class="no-form-control-inner">
                                <input type="datetime-local" name="visible_to" id="visible_to" class="no-form-control-input" placeholder="" >
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">노출 종료일</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span> -->
                        </div>
                        <!-- FormControl -->
                    </div>
                    
                    <div class="no-form-base --md" id="content" data-component-props='{"name": "content"}'>
                        <label for="content" class="no-form-base-label">
                            <span>내용</span>
                        </label>
                        <textarea name="content" id="content" data-text-editor class="no-form-base-input"></textarea>
                        <span class="no-form-control-space"></span>
                    </div>
                    <!-- FormControl -->

                    <div class="no-form-checkbox --sm">
                        <label for="is_pinned" class="no-form-checkbox-pointer">
                            <input type="checkbox" name="is_pinned" id="is_pinned" class="no-form-checkbox-input" value="1">
                            <div class="no-form-checkbox-ripple">
                                <span class="no-form-checkbox-box">
                                    <div class="no-form-checkbox-icon">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </span>
                            </div>
                            <span class="no-form-checkbox-text">상단 고정</span>
                        </label>
                        <p class="no-form-checkbox-helper-text">해당 공지글을 목록의 최상단에 위치시킵니다.</p>
                        <span class="no-form-control-space"></span>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div data-component-type="file" data-component-props='{"file_key": "attach_1"}'></div>
                    <div data-component-type="file" data-component-props='{"file_key": "attach_2"}'></div>
                    <div data-component-type="file" data-component-props='{"file_key": "attach_3"}'></div>
                    <div data-component-type="file" data-component-props='{"file_key": "attach_4"}'></div>
                    <div data-component-type="file" data-component-props='{"file_key": "attach_5"}'></div>
                </div>
                
                <div class="no-form-action">
                    <a href="<?=route('admin.notices.index')?>" data-action="cancel" class="no-btn-primary-outline --sm">
                        <span>취소</span>
                    </a>
                    <button type="submit" class="no-btn-primary --sm">
                        <span>저장</span>
                    </button>
                </div>
            </div>
        </form>

    </div>
    <!-- Row -->
</div>


<?php end_section() ?>

<?php section('script') ?>

<script>
    
</script>
<?php end_section() ?>