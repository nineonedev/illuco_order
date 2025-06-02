<?php extend('layouts.admin') ?>

<?php section('title') ?>
    Dashboard
<?php end_section() ?>

<?php section('content') ?>
<div class="no-form-container">
    <div class="no-page-head">
        <h1>Account information</h1>
        <p>This information will be visible to all users of Docker.</p>
    </div>

    <div class="no-form-group">
        <div class="no-form-base --md">
            <label for="title" class="no-form-base-label">
                <span>내용</span>
            </label>
            <input name="title" id="title" class="no-form-base-input" />
            <span class="no-form-control-space"></span>
        </div>
        <!-- FormControl -->
            
        <div class="no-form-control">
            <label for="title" class="no-form-control-inner">
                <input type="text" name="title" id="title" value="Title" class="no-form-control-input --invalid" placeholder="Title" >
                <fieldset class="no-form-control-label">
                    <legend class="no-form-control-text">Title</legend>
                </fieldset>
            </label>
            <span class="no-form-control-helper-text">
                Only used to display avatar. More information at <a href="" class="--underline"> https://en.gravatar.com/.</a>
            </span>
            <span class="no-form-control-feedback">Title must be required.</span>
            <span class="no-form-control-space"></span>
        </div>
        <!-- FormControl -->

        <div class="no-form-control">
            <label for="title" class="no-form-control-inner">
                <input type="text" name="title" id="title" value="Title" class="no-form-control-input --invalid" placeholder="Title" disabled>
                <fieldset class="no-form-control-label">
                    <legend class="no-form-control-text">Title</legend>
                </fieldset>
            </label>
            <span class="no-form-control-helper-text">
                Only used to display avatar. More information at <a href="" class="--underline"> https://en.gravatar.com/.</a>
            </span>
            <span class="no-form-control-feedback">Title must be required.</span>
            <span class="no-form-control-space"></span>
        </div>
        <!-- FormControl -->
        
        <div class="no-form-control">
            <label for="title" class="no-form-control-inner">
                <input type="text" name="title" id="title" class="no-form-control-input" placeholder="" >
                <fieldset class="no-form-control-label">
                    <legend class="no-form-control-text">Title</legend>
                </fieldset>
            </label>
            <span class="no-form-control-space"></span>
        </div>
        <!-- FormControl -->

        <div class="no-form-control">
            <label for="thumb_image" class="no-form-control-inner">
                <input type="file" name="thumb_image" id="thumb_image" class="no-form-control-input" placeholder="" >
                <fieldset class="no-form-control-label">
                    <legend class="no-form-control-text">Image</legend>
                </fieldset>
            </label>
            <span class="no-form-control-space"></span>
        </div>
        <!-- FormControl -->

        <div class="no-form-control --textarea">
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
        </div>
        <!-- FormControl -->

        <!-- 내용 -->
        <div class="no-form-base --md">
            <label for="content" class="no-form-base-label">
                <span>내용</span>
            </label>
            <textarea name="content" id="content" data-text-editor class="no-form-base-input"></textarea>
            <span class="no-form-control-space"></span>
        </div>
    </div>

    <hr>

    <div class="--flex-column">
        <fieldset class="no-form-group">
            <legend class="no-form-base-label">테정보</legend>
            <div class="no-form-listing">
                <div class="no-form-radio --sm">
                    <label class="no-form-radio-pointer" for="frame_1">
                        <input class="no-form-radio-input" type="radio" name="frame" id="frame_1" value="M">
                        <div class="no-form-radio-ripple">
                            <div class="no-form-radio-box">
                                <span class="no-form-radio-icon"></span>
                            </div>
                        </div>
                        <span class="no-form-radio-text">Frame 1</span>
                    </label>
                </div>
                <div class="no-form-radio --sm">
                    <label class="no-form-radio-pointer" for="frame_2">
                        <input class="no-form-radio-input" type="radio" name="frame" id="frame_2" value="F">
                        <div class="no-form-radio-ripple">
                            <div class="no-form-radio-box">
                                <span class="no-form-radio-icon"></span>
                            </div>
                        </div>
                        <span class="no-form-radio-text">Frame 2</span>
                    </label>
                </div>
                <div class="no-form-radio --sm">
                    <label class="no-form-radio-pointer" for="frame_sports">
                        <input class="no-form-radio-input" type="radio" name="frame" id="frame_sports" value="sad">
                        <div class="no-form-radio-ripple">
                            <div class="no-form-radio-box">
                                <span class="no-form-radio-icon"></span>
                            </div>
                        </div>
                        <span class="no-form-radio-text">Sports</span>
                    </label>
                </div>
                <div class="no-form-radio --sm">
                    <label class="no-form-radio-pointer" for="frame_4">
                        <input class="no-form-radio-input" type="radio" name="frame" id="frame_4" value="frame_4">
                        <div class="no-form-radio-ripple">
                            <div class="no-form-radio-box">
                                <span class="no-form-radio-icon"></span>
                            </div>
                        </div>
                        <span class="no-form-radio-text">Frame 4</span>
                    </label>
                </div>
            </div>
        </fieldset>
        <span class="no-form-control-space"></span>
    </div>

    <div class="no-form-group">
        <div class="no-form-radio --lg">
            <label class="no-form-radio-pointer" for="hobbie_1">
                <input class="no-form-radio-input" type="radio" name="hobbies" id="hobbie_1" disabled>
                <div class="no-form-radio-ripple">
                    <div class="no-form-radio-box">
                        <span class="no-form-radio-icon"></span>
                    </div>
                </div>
                <span class="no-form-radio-text">Selected repositories</span>
            </label>
            <p class="no-form-radio-helper-text">This sends you notifications when you are in the Scout Dashboard.</p>
        </div>
        <div class="no-form-radio --md">
            <label class="no-form-radio-pointer" for="hobbie_2">
                <input class="no-form-radio-input" type="radio" name="hobbies" id="hobbie_2">
                <div class="no-form-radio-ripple">
                    <div class="no-form-radio-box">
                        <span class="no-form-radio-icon"></span>
                    </div>
                </div>
                <span class="no-form-radio-text">All repository</span>
            </label>
            <p class="no-form-radio-helper-text">This sends you notifications when you are in the Scout Dashboard.</p>
        </div>
        <div class="no-form-radio --sm">
            <label class="no-form-radio-pointer" for="hobbie_3">
                <input class="no-form-radio-input" type="radio" name="hobbies" id="hobbie_3">
                <div class="no-form-radio-ripple">
                    <div class="no-form-radio-box">
                        <span class="no-form-radio-icon"></span>
                    </div>
                </div>
                <span class="no-form-radio-text">None</span>
            </label>
            <p class="no-form-radio-helper-text">This sends you notifications when you are in the Scout Dashboard.</p>
        </div>
    </div>

    <div class="no-form-group">
        <div class="no-form-block">
            <div class="no-form-checkbox --lg">
                <label for="game_1" class="no-form-checkbox-pointer">
                    <input type="checkbox" name="games" id="game_1" class="no-form-checkbox-input" disabled>
                    <div class="no-form-checkbox-ripple">
                        <span class="no-form-checkbox-box">
                            <div class="no-form-checkbox-icon">
                                <i class="fa-solid fa-check"></i>
                            </div>
                        </span>
                    </div>
                    <span class="no-form-checkbox-text">LOL</span>
                </label>
                <p class="no-form-checkbox-helper-text"></p>
            </div>
        </div>

        <div class="no-form-block">
            <div class="no-form-checkbox --md">
                <label for="game_2" class="no-form-checkbox-pointer">
                    <input type="checkbox" name="games" id="game_2" class="no-form-checkbox-input">
                    <div class="no-form-checkbox-ripple">
                        <span class="no-form-checkbox-box">
                            <div class="no-form-checkbox-icon">
                                <i class="fa-solid fa-check"></i>
                            </div>
                        </span>
                    </div>
                    <span class="no-form-checkbox-text">Stand Off2</span>
                </label>
                <p class="no-form-radio-helper-text">This sends you notifications when you are in the Scout Dashboard.</p>
            </div>
        </div>

        <div class="no-form-block">
            <div class="no-form-checkbox --sm">
                <label for="game_3" class="no-form-checkbox-pointer">
                    <input type="checkbox" name="games" id="game_3" class="no-form-checkbox-input">
                    <div class="no-form-checkbox-ripple">
                        <span class="no-form-checkbox-box">
                            <div class="no-form-checkbox-icon">
                                <i class="fa-solid fa-check"></i>
                            </div>
                        </span>
                    </div>
                    <span class="no-form-checkbox-text">BaletteGround</span>
                </label>
            </div>
        </div>
        
    </div>

    <hr>

    <div class="no-form-group">
        <div class="no-form-block">
            <div class="no-form-switch --lg">
                <label for="enable" class="no-form-switch-pointer">
                    <input type="checkbox" name="enable" id="enable" class="no-form-switch-input" disabled>
                    <div class="no-form-switch-bar">
                        <div class="no-form-switch-position">
                            <div class="no-form-switch-ripple">
                                <span class="no-form-switch-knob"></span>
                            </div>
                        </div>
                    </div>
                    <span class="no-form-switch-text">BaletteGround</span>
                </label>
                <p class="no-form-switch-helper-text">This sends you notifications when you are in the Scout Dashboard.</p>
            </div>
        </div>
        <div class="no-form-block">
            <div class="no-form-switch --md">
                <label for="enable_1" class="no-form-switch-pointer">
                    <input type="checkbox" name="enable_1" id="enable_1" class="no-form-switch-input">
                    <div class="no-form-switch-bar">
                        <div class="no-form-switch-position">
                            <div class="no-form-switch-ripple">
                                <span class="no-form-switch-knob"></span>
                            </div>
                        </div>
                    </div>
                    <span class="no-form-switch-text">BaletteGround</span>
                </label>
                <p class="no-form-switch-helper-text">This sends you notifications when you are in the Scout Dashboard.</p>
            </div>
        </div>
        <div class="no-form-block">
            <div class="no-form-switch --sm">
                <label for="enable_2" class="no-form-switch-pointer">
                    <input type="checkbox" name="enable_2" id="enable_2" class="no-form-switch-input">
                    <div class="no-form-switch-bar">
                        <div class="no-form-switch-position">
                            <div class="no-form-switch-ripple">
                                <span class="no-form-switch-knob"></span>
                            </div>
                        </div>
                    </div>
                    <span class="no-form-switch-text">BaletteGround</span>
                </label>
                <p class="no-form-switch-helper-text">This sends you notifications when you are in the Scout Dashboard.</p>
            </div>
        </div>
    </div>


    <div class="no-form-action">
        <button class="no-btn no-btn-primary">Primary</button>
        <button class="no-btn no-btn-primary-outline">Primary</button>
        <button class="no-btn no-btn-primary-outline" disabled>Primary</button>
        <a href="#" class="no-link-primary">
            <span>Skip</span>
        </a>
    </div>
    
    <div class="no-form-action">
        <button class="no-btn no-btn-warning">Warning</button>
        <button class="no-btn no-btn-warning-outline">Warning</button>
        <a href="#" class="no-link-warning">
            <span>Skip</span>
        </a>
    </div>
    
    <div class="no-form-action">
        <button class="no-btn no-btn-success">Success</button>
        <button class="no-btn no-btn-success-outline">Success</button>
    </div>
    
    <div class="no-form-action">
        <button class="no-btn no-btn-error">Error</button>
        <button class="no-btn no-btn-error-outline">Error</button>
    </div>
    
    <div class="no-form-action">
        <button class="no-btn no-btn-info">Info</button>
        <button class="no-btn no-btn-info-outline">Info</button>
    </div>
    
    <div class="no-form-action">
        <button class="no-btn no-btn-premium">Premium</button>
        <button class="no-btn no-btn-premium-outline">Premium</button>
    </div>
    <div class="no-form-action">
        <button class="no-btn no-btn-white">White</button>
        <button class="no-btn no-btn-white-outline">White</button>
    </div>
    <div class="no-form-action">
        <button class="no-btn no-btn-black">Black</button>
        <button class="no-btn no-btn-black-outline">Black</button>
    </div>
    <div class="no-form-action">
        <button class="no-btn no-btn-gray">Gray</button>
        <button class="no-btn no-btn-gray-outline">Gray</button>
    </div>
    
    <div class="no-form-action">
        <button class="no-btn no-btn-slate">Slate</button>
        <button class="no-btn no-btn-slate-outline">Slate</button>
    </div>
    

</div>

<?php end_section() ?>
