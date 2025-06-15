<?php extend('layouts.admin'); ?>
<?php section('title') ?>
권한 생성
<?php end_section() ?>

<?php section('content') ?>

<div class="no-page-container">
    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">권한 생성</h1>
        </div>
        <!-- Head -->
        
        <form method="post" enctype="multipart/form-data" action="<?= route('admin.roles.store') ?>" class="no-form-container" id="frm">
            <?= csrf_field() ?>
            <div class="no-form-group">
                <div class="no-form-control --md">
                    <label for="name" class="no-form-control-inner">
                        <input type="text" name="name" id="name" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이름</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                <!-- FormControl -->

                <div class="no-form-control --textarea">
                    <label for="description" class="no-form-control-inner">
                        <textarea type="text" name="description" id="description" class="no-form-control-input" placeholder="" rows="8"></textarea>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">설명</legend>
                        </fieldset>
                    </label>
                </div>
                <!-- FormControl -->
            </div>
            <div class="no-form-group">
                <?php
                $permissions = context()->get('permissions', []);
                $actions = context()->get('permission_actions', []); 
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
                        <?php foreach ($permissions as $entityClass => $grants): ?>
                            <tr class="no-table-hover">
                                <td><?= lang('system.'.$entityClass::alias()) ?? class_basename($entityClass) ?></td>
                                <?php foreach ($actions as $action): ?>
                                    <td>
                                        <div class="no-form-checkbox --xs">
                                            <label for="<?= md5($entityClass . $action) ?>" class="no-form-checkbox-pointer">
                                                <input 
                                                    type="checkbox" 
                                                    name="permissions[<?= $entityClass::alias() ?>][]" 
                                                    value="<?= $action ?>"
                                                    id="<?= md5($entityClass . $action) ?>"
                                                    class="no-form-checkbox-input"
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
                <a href="<?=route('admin.roles.index')?>" class="no-btn-primary-outline --sm">
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
<script>
    const form = document.querySelector('#frm');
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault(); 
        e.submitter.disabled = true; 
        
        const fd = new FormData(e.target); 
        
        try {
            const response = await fetch(e.target.action, {
                headers: {
                    'Accept': 'application/json'
                },
                method: e.target.method,
                body: fd,
            }); 

            const resData = await response.json(); 
            const {message, data, success} = resData;

            console.log(resData);
            
            alert(message);

            // if (success && data.redirect){
            //     location.href = data.redirect;
            // }

        } catch ($error) {
            alert($error.message); 

        } finally {

            e.submitter.disabled = false; 
        }
        
        
    })
</script>
<?php end_section() ?>