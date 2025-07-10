<?php 

?>
<?php extend('layouts.admin'); ?>
<?php section('controller', 'role') ?>
<?php section('action', 'edit') ?>
<?php section('title', '권한 수정') ?>


<?php section('content') ?>
<div class="no-page-container">
    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">권한 수정</h1>
        </div>

        <form 
            method="post" 
            enctype="multipart/form-data" 
            action="<?= route('admin.roles.update', ['id' => $role->id]) ?>" 
            class="no-form-container" 
            id="frm"
        >
            <?= csrf_field() ?>
            <?= put_field() ?>

            <div class="no-form-group">
                <div class="no-form-control --md">
                    <label for="label" class="no-form-control-inner">
                        <input type="text" name="label" id="label" class="no-form-control-input" value="<?= e($role->label) ?>" placeholder="" required>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이름</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --md">
                    <label for="name" class="no-form-control-inner">
                        <input type="text" name="name" id="name" class="no-form-control-input" value="<?= e($role->name) ?>" placeholder="" required>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">식별자</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control --textarea">
                    <label for="description" class="no-form-control-inner">
                        <textarea name="description" id="description" class="no-form-control-input" rows="8"><?= e($role->description) ?></textarea>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">설명</legend>
                        </fieldset>
                    </label>
                </div>
            </div>

            <div class="no-form-group">
                <?php
                $permissions = context()->get('permissions', []);
                $actions = context()->get('permission_actions', []); 
                $rolePermissions = $role->get('permissions');
                
                ?>

                <table class="no-page-index-table">
                    <thead>
                        <tr>
                            <th>항목</th>
                            <?php foreach ($actions as $action): ?>
                                <th><?= lang('system.'.$action) ?? ucfirst($action) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($permissions as $roleName => $available): ?>
                            <tr class="no-table-hover">
                                <td><?= lang('system.'.$roleName) ?? class_basename($roleName) ?></td>
                                <?php foreach ($available as $action): ?>
                                    <?php
                                        $alias = $roleName;
                                        $isChecked = false;

                                        foreach ($rolePermissions as $permission) {
                                            if ($permission->matches($alias, $action)) {
                                                $isChecked = true; 
                                            }
                                        }
                                    ?>
                                    <td>
                                        <div class="no-form-checkbox --xs">
                                            <label for="<?= md5($roleName . $action) ?>" class="no-form-checkbox-pointer">
                                                <input 
                                                    type="checkbox" 
                                                    name="permissions[<?= $alias ?>][]" 
                                                    value="<?= $action ?>"
                                                    id="<?= md5($roleName . $action) ?>"
                                                    class="no-form-checkbox-input"
                                                    <?= $isChecked ? 'checked' : '' ?>
                                                >
                                                <div class="no-form-checkbox-ripple">
                                                    <span class="no-form-checkbox-box">
                                                        <div class="no-form-checkbox-icon">
                                                            <i class="fa-solid fa-check"></i>
                                                        </div>
                                                    </span>
                                                </div>
                                            </label>
                                        </div>
                                    </td>
                                    <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="no-form-action">
                <a href="<?= route('admin.roles.index') ?>" data-action="cancel" class="no-btn-primary-outline --sm">
                    <span>취소</span>
                </a>
                <button type="button" class="no-btn-error-outline --sm" data-action="delete">
                    <span>삭제</span>
                </button>
                <button type="submit" class="no-btn-primary --sm">
                    <span>수정</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php end_section() ?>
