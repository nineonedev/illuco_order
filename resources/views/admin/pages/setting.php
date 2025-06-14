
<?php extend('layouts.admin') ?>

<?php section('title') ?>
    설정
<?php end_section() ?>

<?php section('content') ?>

<div class="no-form-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">설정</h1>
        </div>
        <!-- Head -->

        <form action="#" method="post" class="no-form" enctype="multipart/form-data">
            <div class="no-form-group">
                <div class="no-form-block">
                    <div class="no-form-switch --sm">
                        <label for="use_notification" class="no-form-switch-pointer">
                            <input type="checkbox" name="use_notification" id="use_notification" class="no-form-switch-input">
                            <div class="no-form-switch-bar">
                                <div class="no-form-switch-position">
                                    <div class="no-form-switch-ripple">
                                        <span class="no-form-switch-knob"></span>
                                    </div>
                                </div>
                            </div>
                            <span class="no-form-switch-text">알림 사용</span>
                        </label>
                        <p class="no-form-switch-helper-text">주문이 접수될 때 지정한 담당자들에게 알림이 발송됩니다.</p>
                        <span class="no-form-control-space"></span>
                    </div>
                </div>

                <div class="--flex-column">
                    <fieldset class="no-form-group">
                        <legend class="no-form-base-label">테마</legend>
                        <div class="no-form-listing">
                            <div class="no-form-radio --sm">
                                <label class="no-form-radio-pointer" for="theme_system">
                                    <input class="no-form-radio-input" type="radio" name="theme" id="theme_system" value="auto"
                                        <?= cookie()->get('theme') === 'auto' ? 'checked' : '' ?>
                                    >
                                    <div class="no-form-radio-ripple">
                                        <div class="no-form-radio-box">
                                            <span class="no-form-radio-icon"></span>
                                        </div>
                                    </div>
                                    <span class="no-form-radio-text">시스템</span>
                                </label>
                            </div>
                            <div class="no-form-radio --sm">
                                <label class="no-form-radio-pointer" for="theme_light">
                                    <input class="no-form-radio-input" type="radio" name="theme" id="theme_light" value="light"
                                        <?= cookie()->get('theme') === 'light' ? 'checked' : '' ?>
                                    >
                                    <div class="no-form-radio-ripple">
                                        <div class="no-form-radio-box">
                                            <span class="no-form-radio-icon"></span>
                                        </div>
                                    </div>
                                    <span class="no-form-radio-text">라이트</span>
                                </label>
                            </div>
                            <div class="no-form-radio --sm">
                                <label class="no-form-radio-pointer" for="theme_dark">
                                    <input class="no-form-radio-input" type="radio" name="theme" id="theme_dark" value="dark"
                                        <?= cookie()->get('theme') === 'dark' ? 'checked' : '' ?>
                                    >
                                    <div class="no-form-radio-ripple">
                                        <div class="no-form-radio-box">
                                            <span class="no-form-radio-icon"></span>
                                        </div>
                                    </div>
                                    <span class="no-form-radio-text">다크</span>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    <span class="no-form-control-space"></span>
                </div>
                <div class="--flex-column">
                    <fieldset class="no-form-group">
                        <legend class="no-form-base-label">언어</legend>
                        <div class="no-form-listing">
                            <?php foreach (config('translation.languages') ?? [] as $lang => $label) : 
                                $checked = $lang === get_locale() ? 'checked' : '';     
                            ?>
                            <div class="no-form-radio --sm">
                                <label class="no-form-radio-pointer" for="language_<?=$lang?>">
                                    <input 
                                        class="no-form-radio-input" 
                                        type="radio" 
                                        name="locale"
                                        id="language_<?=$lang?>" 
                                        value="<?=$lang?>" 
                                        <?=$checked?>
                                    >
                                    <div class="no-form-radio-ripple">
                                        <div class="no-form-radio-box">
                                            <span class="no-form-radio-icon"></span>
                                        </div>
                                    </div>
                                    <span class="no-form-radio-text"><?=$label?></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>
                    <span class="no-form-control-space"></span>
                </div>

            </div>
            
            <div class="no-form-action">
                <button type="submit" class="no-btn-primary --sm">
                    <span>저장</span>
                </button>
            </div>
        </form>
    </div>
    <!-- Row -->
</div>
<?php end_section() ?>