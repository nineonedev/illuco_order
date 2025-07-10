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

            <?php if ($roles) : ?>
                <?php
                    $roleOptions = [];
                    foreach ($roles as $role) {
                        /** @var \App\Domains\Auth\Entities\Role $role */
                        $roleOptions[] = [
                            'label' => $role->label,
                            'value' => $role->id,
                        ];
                    }
                ?>
                <div 
                    id="role_id"
                    class="no-form-control --md"
                    data-view-type="select"
                    data-view-props='<?= json_encode([
                        "label"   => "권한 선택",
                        "name"    => "role_id",
                        "value"   => $employee->roles[0]->id, // 현재 선택된 role_id가 있다면 여기에 채워도 됨
                        "options" => $roleOptions
                    ], JSON_UNESCAPED_UNICODE) ?>'
                ></div>
            <?php endif; ?>


            <div class="no-form-group">
                <!-- 이름 -->
                <div class="no-form-control --md">
                    <label for="name" class="no-form-control-inner">
                        <input type="text" name="name" id="name" class="no-form-control-input" required placeholder="" value="<?= $employee->name ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이름</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 이메일 -->
                <div class="no-form-control --md">
                    <label for="email" class="no-form-control-inner">
                        <input type="email" name="email" id="email" class="no-form-control-input" required placeholder="" value="<?= $employee->email ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이메일</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --md">
                    <label for="phone" class="no-form-control-inner">
                        <input type="tel" name="phone" id="phone" class="no-form-control-input" required placeholder="" value="<?= $employee->phone ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">연락처</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <!-- 비밀번호 -->
                <div class="no-form-control --md">
                    <label for="password" class="no-form-control-inner">
                        <input type="password" name="password" id="password" class="no-form-control-input" placeholder="">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">비밀번호</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-block">
                    <div class="no-form-checkbox --md">
                        <label for="is_active" class="no-form-checkbox-pointer">
                            <input type="checkbox" name="is_active" id="is_active" class="no-form-checkbox-input" <?= $employee->is_active ? 'checked' : '' ?> >
                            <div class="no-form-checkbox-ripple">
                                <span class="no-form-checkbox-box">
                                    <div class="no-form-checkbox-icon">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </span>
                            </div>
                            <span class="no-form-checkbox-text">활성 여부</span>
                        </label>
                        <p class="no-form-radio-helper-text">체크 해제할 경우 해당 계정은 비활성화됩니다.</p>
                    </div>
                </div>
            </div>

            <div class="no-form-action">
                <a href="<?= route('admin.employees.index') ?>" data-action="cancel" class="no-btn-primary-outline --sm">
                    <span>취소</span>
                </a>
                <?php if(can('employee.delete')) : ?>
                <button type="button" class="no-btn-error-outline --sm" data-action="delete">
                    <span>삭제</span>
                </button>
                <?php endif; ?>
                <button type="submit" class="no-btn-primary --sm">
                    <span>저장</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php end_section() ?>
