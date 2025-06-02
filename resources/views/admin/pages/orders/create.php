<?php extend('layouts.admin'); ?>
<?php section('title') ?>
주문 생성
<?php endSection() ?>

<?php section('content') ?>

<div class="no-form-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">직원 생성</h1>
        </div>
        <!-- Head -->

        
        <form method="post" enctype="multipart/form-data" action="<?=route('orders.store')?>">
            <div class="no-form-group">
                <div class="no-form-control --md">
                    <label for="title" class="no-form-control-inner">
                        <input type="text" name="title" id="title" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">Title</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                <!-- FormControl -->
                
                <div class="no-form-control --md">
                    <label for="thumb_image" class="no-form-control-inner">
                        <input type="file" name="thumb_image" id="thumb_image" class="no-form-control-input" placeholder="" >
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">Image</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                <!-- FormControl -->

                <div class="no-form-control --textarea --md">
                    <label for="content" class="no-form-control-inner">
                        <textarea 
                            type="text" 
                            name="content" 
                            id="content" 
                            class="no-form-control-input" 
                            placeholder=""
                            rows="8"
                        ></textarea>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">Contents</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
                <!-- FormControl -->
            </div>

            <div class="no-form-action">
                <a href="#" class="no-btn-primary-outline --sm">
                    <span>취소</span>
                </a>
                <button type="submit" class="no-btn-primary --sm">
                    <span>저장</span>
                </button>
            </div>
        </form>

    </div>
    <!-- Row -->
</div>

<?php endSection() ?>

<?php section('script') ?>
<script>
    const form = document.forms[0]; 

    const handleSubmit = async (e) => {
        e.preventDefault(); 
        const t = e.target; 
        const fd = new FormData(t); 

        try {
            const response = await fetch(t.action, {
                method: t.method,
                body: fd,
            });

            if (!response.ok) {
                throw new Error(response.statusText);
            }

            const resData = await response.json(); 

            console.log(resData);
            

        } catch (error) {
            alert(error.message); 
        }
    }

    form.addEventListener('submit', handleSubmit);
</script>
<?php endSection() ?>