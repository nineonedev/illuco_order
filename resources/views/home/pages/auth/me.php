<?php extend('layouts.admin') ?>

<?php section('title') ?>
    Test
<?php end_section() ?>

<?php section('content') ?>
    <div class="no-form-container">
        <div class="no-page-head">
            <h1>Account information</h1>
            <p>This information will be visible to all users of Docker.</p>
        </div>

        <form action="<?= route('admin.me.update') ?> " method="post" enctype="multipart/form-data">
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
                

                <div class="no-form-action">
                    <button type="submit" class="no-btn-primary">전송</button>
                </div>
            </div>
        </form>
    </div>
<?php end_section() ?>

