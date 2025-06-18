<?php extend('layouts.admin'); ?>
<?php section('controller', 'employee') ?>
<?php section('action', 'edit') ?>
<?php section('title', '직원 수정') ?>

<?php section('content') ?>
<div class="no-form-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">직원 수정</h1>
        </div>
        
        <form id="frm" method="post" enctype="multipart/form-data" action="<?= route('admin.employees.update', ['id' => $employee->id]) ?>">
            <?= csrf_field() ?>
            <?= method_field('patch') ?>

            <div class="no-form-group">
                <div class="no-form-control --md">
                    <label for="name" class="no-form-control-inner">
                        <input type="text" name="name" id="name" class="no-form-control-input" value="<?= e($employee->user ? $employee->user->name : '') ?>" required>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이름</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>


                <div class="no-form-control --md">
                    <label for="email" class="no-form-control-inner">
                        <input type="email" name="email" id="email" class="no-form-control-input" value="<?= e($employee->user ? $employee->user->email : '') ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이메일</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                
                <div class="no-form-control --md">
                    <label for="password" class="no-form-control-inner">
                        <input type="password" name="password" id="password" class="no-form-control-input" placeholder="변경 시 입력">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">비밀번호</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-helper-text">비밀번호는 변경 시 입력해주세요.</span>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --md">
                    <label for="department" class="no-form-control-inner">
                        <input type="text" name="department" id="department" class="no-form-control-input" placeholder="" value="<?=e($employee->department ?? '')?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">부서</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --md">
                    <label for="position" class="no-form-control-inner">
                        <input type="text" name="position" id="position" class="no-form-control-input" placeholder="" value="<?=e($employee->position ?? '')?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">직책</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 연락처 -->
                <div class="no-form-control --md">
                    <label for="phone_number" class="no-form-control-inner">
                        <input type="tel" name="phone_number" id="phone_number" class="no-form-control-input" placeholder="" value="<?=e($employee->phone_number ?? '')?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">연락처</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

            </div>

            <div class="no-form-action">
                <a href="<?= route('admin.employees.index') ?>" data-action="cancel" class="no-btn-primary-outline --sm">
                    <span>취소</span>
                </a>
                <button type="button" class="no-btn-error-outline --sm" data-action="delete">
                    <span>삭제</span>
                </button>
                <button type="submit" class="no-btn-primary --sm">
                    <span>저장</span>
                </button>
            </div>

        </form>
    </div>
</div>
<?php end_section() ?>
