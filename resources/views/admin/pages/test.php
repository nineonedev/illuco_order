<?php

use App\User\Repositories\FileRepository;
use App\User\Repositories\UserRepository;

 extend('layouts.admin') ?>

<?php section('title') ?>
    Test
<?php end_section() ?>

<?php section('content') ?>
    <div class="no-form-container">
        <div class="no-page-head">
            <h1>Account information</h1>
            <p>This information will be visible to all users of Docker.</p>
        </div>

        <form action="<?= route('auth.login') ?> " method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="no-form-group">
                <div class="no-form-control">
                    <label for="name" class="no-form-control-inner">
                        <input type="text" name="name" id="name"  class="no-form-control-input --invalid" placeholder="Title" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">이름</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                
                <div class="no-form-control">
                    <label for="profile_image" class="no-form-control-inner">
                        <input type="file" name="profile_image" id="profile_image" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">Image</legend>
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


<?php section('script') ?>
<script>
    const frm = document.querySelector('form'); 
    frm.addEventListener('submit', async (e) => {
        e.preventDefault(); 
        
        const fd = new FormData(e.target); 

        const response = await fetch(e.target.action, {
            method: e.target.method,
            body: fd,
            headers: {
                'Accept': 'application/json',
            },
        }); 
        const resData = await response.json(); 

        console.log(resData);
        
    })
</script>
<?php end_section() ?>