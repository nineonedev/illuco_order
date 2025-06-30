<?php extend('layouts.admin'); ?>
<?php section('controller', 'admin') ?>
<?php section('action', 'edit') ?>
<?php section('title', '관리자 수정') ?>

<?php section('content') ?>
    <div class="no-form-container">
        <div class="no-page-head">
            <h1 class="no-heading-sm">관리자 수정</h1>
        </div>
        
        <form action="<?= route('auth.update', ['id' => $user->id]) ?> " method="post" enctype="multipart/form-data" id="frm">
            <?= csrf_field() ?>
            <div class="no-form-group">
                <div class="no-form-control">
                    <label for="name" class="no-form-control-inner">
                        <input type="text" name="name" id="name"  class="no-form-control-input" value="<?= $user->name ?>">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이름</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>

                <div class="no-form-control">
                    <label for="email" class="no-form-control-inner">
                        <input type="text" name="email" id="email"  class="no-form-control-input" value="<?= $user->email ?>">
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

                <div class="no-form-action">
                    <button type="submit" class="no-btn-primary">저장</button>
                </div>
            </div>
        </form>
    </div>
<?php end_section() ?>

