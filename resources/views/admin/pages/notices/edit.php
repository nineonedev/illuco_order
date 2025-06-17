<?php extend('layouts.admin'); 

use App\Domains\Communication\Entities\Notice;?>
<?php section('controller', 'notice') ?>
<?php section('action', 'edit') ?>
<?php section('title', '공지사항 수정') ?>

<?php section('content') ?>
<div class="no-form-container">
    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">공지사항 수정</h1>
        </div>

            <form  
                data-component-props
                method="post" 
                id="frm" 
                enctype="multipart/form-data" 
                action="<?= route('admin.notices.update', ['id' => $notice->id]) ?>"
            >
            <?= csrf_field() ?>
            <?= put_field() ?>
            <div class="no-form-inner">
                <div class="no-form-group">

                    <!-- 제목 -->
                    <div class="no-form-control --md">
                        <label for="title" class="no-form-control-inner">
                            <input type="text" name="title" id="title" class="no-form-control-input" value="<?= e(old('title', $notice->title)) ?>">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">제목</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>
                    
                    <!-- 상태 -->
                    <div class="no-form-control --md">
                        <label for="status" class="no-form-control-inner">
                            <select name="status" id="status" class="no-form-control-input">
                                <option value="">-- 상태 선택 --</option>
                                <option value="<?= Notice::STATUS_DRAFT ?>" <?= old('status', $notice->status) === Notice::STATUS_DRAFT ? 'selected' : '' ?>>작성중</option>
                                <option value="<?= Notice::STATUS_PUBLISHED ?>" <?= old('status', $notice->status) === Notice::STATUS_PUBLISHED ? 'selected' : '' ?>>게시중</option>
                                <option value="<?= Notice::STATUS_SCHEDULED ?>" <?= old('status', $notice->status) === Notice::STATUS_SCHEDULED ? 'selected' : '' ?>>예약게시</option>
                                <option value="<?= Notice::STATUS_ARCHIVED ?>" <?= old('status', $notice->status) === Notice::STATUS_ARCHIVED ? 'selected' : '' ?>>보관됨</option>
                            </select>
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">상태</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-flex">
                        <!-- 노출 시작일 -->
                        <div class="no-form-control --md">
                            <label for="visible_from" class="no-form-control-inner">
                                <input type="datetime-local" name="visible_from" id="visible_from" class="no-form-control-input" value="<?= e(old('visible_from', $notice->visible_from)) ?>">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">노출 시작일</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>

                        <!-- 노출 종료일 -->
                        <div class="no-form-control --md">
                            <label for="visible_to" class="no-form-control-inner">
                                <input type="datetime-local" name="visible_to" id="visible_to" class="no-form-control-input" value="<?= e(old('visible_to', $notice->visible_to)) ?>">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">노출 종료일</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                    </div>

                    <!-- 내용 -->
                    <div class="no-form-base --md" id="content" data-component-props='{"name": "content", "value": <?= json_encode($notice->content) ?>}'>
                        <label for="content" class="no-form-base-label">
                            <span>내용</span>
                        </label>
                        <textarea name="content" id="content" data-text-editor class="no-form-base-input"><?= e(old('content', $notice->content)) ?></textarea>
                        <span class="no-form-control-space"></span>
                    </div>


                    <!-- 상단 고정 -->
                    <div class="no-form-checkbox --sm">
                        <label for="is_notice" class="no-form-checkbox-pointer">
                            <input type="checkbox" name="is_notice" id="is_notice" class="no-form-checkbox-input"
                                <?= old('is_notice', $notice->is_pinned) ? 'checked' : '' ?>>
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

                    <?php 
                    $attach_1 = $notice->fileattachment->props('attach_1');
                    $attach_2 = $notice->fileattachment->props('attach_2');
                    $attach_3 = $notice->fileattachment->props('attach_3');
                    $attach_4 = $notice->fileattachment->props('attach_4');
                    $attach_5 = $notice->fileattachment->props('attach_5');

                    ?>

                    <div data-component-type="file" data-component-props='<?=$attach_1?>'></div>
                    <div data-component-type="file" data-component-props='<?=$attach_2?>'></div>
                    <div data-component-type="file" data-component-props='<?=$attach_3?>'></div>
                    <div data-component-type="file" data-component-props='<?=$attach_4?>'></div>
                    <div data-component-type="file" data-component-props='<?=$attach_5?>'></div>
                </div>

                <div class="no-form-action">
                    <a href="<?= route('admin.notices.index') ?>" class="no-btn-primary-outline --sm">
                        <span>취소</span>
                    </a>
                    
                    <button type="button" class="no-btn-error-outline --sm" data-action="delete">
                        <span>삭제</span>
                    </button>
                    <button type="submit" class="no-btn-primary --sm" data-action="put">
                        <span>수정</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php end_section() ?>

<?php section('script') ?>
<script>
    // 필요시 text-editor 초기화
</script>
<?php end_section() ?>
