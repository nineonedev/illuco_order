<?php extend('layouts.admin'); ?>

<?php section('title') ?>
카테고리
<?php end_section() ?>

<?php section('content') ?>
<div class="no-form-outer no-cart">

    <div class="no-cart-grid">
        <div class="no-cart-area">
            <div class="no-page-row">
                <div class="no-page-head">
                    <h1 class="no-heading-sm">카테고리</h1>
                    <p class="no-text-secondary">등록된 카테고리를 확인하실 수 있습니다.</p> 
                </div>
                <!-- Head -->

                <div id="category-hook">
                    <div class="no-category-menu">
                        Category Items
                    </div>

                    <ul class="no-category-items">
                        <li class="no-category-item" data-node="root">
                            <div class="no-category-item-block">
                                <div class="no-category-item-head">
                                    <div class="no-category-item-head-move">
                                        <button type="button" data-action="drag" class="no-btn-icon --narrow --xs">
                                            <i class="fa-regular fa-grip-dots-vertical"></i>
                                        </button>
                                        <button type="button" data-action="toggle" class="no-btn-icon --narrow --xs">
                                            <i class="fa-solid fa-caret-right"></i>
                                        </button>
                                    </div>
                                    <div class="no-category-item-head-text">
                                        <span>Loupe</span>
                                    </div>
                                </div>
                                <div class="no-category-item-action">
                                    <button type="button" data-action="edit" class="no-btn-icon --xs">
                                        <i class="fa-light fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" data-action="delete" class="no-btn-icon --xs">
                                        <i class="fa-light fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="no-category-children">
                                <ul class="no-category-items">
                                    <li class="no-category-item" data-node="leaf">
                                        <div class="no-category-item-block">
                                            <div class="no-category-item-head">
                                                <div class="no-category-item-head-move">
                                                    <button type="button" data-action="drag" class="no-btn-icon --narrow --xs">
                                                        <i class="fa-regular fa-grip-dots-vertical"></i>
                                                    </button>
                                                    <button type="button" data-action="toggle" class="no-btn-icon --narrow --xs">
                                                        <i class="fa-solid fa-caret-right"></i>
                                                    </button>
                                                </div>
                                                <div class="no-category-item-head-text">
                                                    <span>Loupe</span>
                                                </div>
                                            </div>
                                            <div class="no-category-item-action">
                                                <button type="button" data-action="edit" class="no-btn-icon --xs">
                                                    <i class="fa-light fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" data-action="delete" class="no-btn-icon --xs">
                                                    <i class="fa-light fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="no-category-item" data-node="leaf">
                                        <div class="no-category-item-block">
                                            <div class="no-category-item-head">
                                                <div class="no-category-item-head-move">
                                                    <button type="button" data-action="drag" class="no-btn-icon --narrow --xs">
                                                        <i class="fa-regular fa-grip-dots-vertical"></i>
                                                    </button>
                                                    <button type="button" data-action="toggle" class="no-btn-icon --narrow --xs">
                                                        <i class="fa-solid fa-caret-right"></i>
                                                    </button>
                                                </div>
                                                <div class="no-category-item-head-text">
                                                    <span>Loupe</span>
                                                </div>
                                            </div>
                                            <div class="no-category-item-action">
                                                <button type="button" data-action="edit" class="no-btn-icon --xs">
                                                    <i class="fa-light fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" data-action="delete" class="no-btn-icon --xs">
                                                    <i class="fa-light fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="no-category-item" data-node="leaf">
                                        <button type="button" class="no-category-item-block --add">
                                            <div class="no-category-item-add__icon">
                                                <i class="fa-light fa-plus"></i>
                                            </div>
                                            <span class="no-category-item-add__text">Add category item to ___</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="no-category-item" data-node="root">
                            <div class="no-category-item-block">
                                <div class="no-category-item-head">
                                    <div class="no-category-item-head-move">
                                        <button type="button" data-action="drag" class="no-btn-icon --narrow --xs">
                                            <i class="fa-regular fa-grip-dots-vertical"></i>
                                        </button>
                                        <button type="button" data-action="toggle" class="no-btn-icon --narrow --xs">
                                            <i class="fa-solid fa-caret-right"></i>
                                        </button>
                                    </div>
                                    <div class="no-category-item-head-text">
                                        <span>Loupe</span>
                                    </div>
                                </div>
                                <div class="no-category-item-action">
                                    <button type="button" data-action="edit" class="no-btn-icon --xs">
                                        <i class="fa-light fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" data-action="delete" class="no-btn-icon --xs">
                                        <i class="fa-light fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </li>
                        <li class="no-category-item" data-node="root">
                            <div class="no-category-item-block">
                                <div class="no-category-item-head">
                                    <div class="no-category-item-head-move">
                                        <button type="button" data-action="drag" class="no-btn-icon --narrow --xs">
                                            <i class="fa-regular fa-grip-dots-vertical"></i>
                                        </button>
                                        <button type="button" data-action="toggle" class="no-btn-icon --narrow --xs">
                                            <i class="fa-solid fa-caret-right"></i>
                                        </button>
                                    </div>
                                    <div class="no-category-item-head-text">
                                        <span>Loupe</span>
                                    </div>
                                </div>
                                <div class="no-category-item-action">
                                    <button type="button" data-action="edit" class="no-btn-icon --xs">
                                        <i class="fa-light fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" data-action="delete" class="no-btn-icon --xs">
                                        <i class="fa-light fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </li>
                        <li class="no-category-item" data-node="root">
                            <button type="button" class="no-category-item-block --add">
                                <div class="no-category-item-add__icon">
                                    <i class="fa-light fa-plus"></i>
                                </div>
                                <span class="no-category-item-add__text">Add category item to ___</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Area -->
        <div class="no-cart-area">
            <div class="no-page-row">
                <div class="no-page-head">
                    <h1 class="no-heading-sm">카테고리 수정</h1>
                </div>
                
                <form action="" class="no-card">
                    <div class="no-form-group">
                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" value="Loupe" placeholder="" >
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">이름</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                        <!-- FormControl -->

                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" value="LP" placeholder="" >
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">코드</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                        <!-- FormControl -->

                        <div class="no-form-control no-form-file">
                            <label for="file_attachment_1" class="no-form-control-inner">
                                <input 
                                    type="file" 
                                    name="file_attachment_1" 
                                    id="file_attachment_1" 
                                    class="no-form-control-input" 
                                    placeholder="" 
                                >
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">썸네일 이미지</legend>
                                </fieldset>
                                <button class="no-form-file-input" type="button" data-input-file>
                                    <div class="no-form-file-icon">
                                        <i class="fa-light fa-paperclip-vertical"></i>
                                    </div>
                                    <span class="no-form-file-text" data-input-file-text>선택된 파일 없음</span>
                                    <span class="no-form-file-button-text">파일선택</span>
                                </button>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>

                        <div class="no-form-checkbox --sm">
                            <label for="is_notice" class="no-form-checkbox-pointer">
                                <input type="checkbox" name="is_notice" id="is_notice" class="no-form-checkbox-input" checked>
                                <div class="no-form-checkbox-ripple">
                                    <span class="no-form-checkbox-box">
                                        <div class="no-form-checkbox-icon">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                    </span>
                                </div>
                                <span class="no-form-checkbox-text">노출여부 등록</span>
                            </label>
                            <p class="no-form-checkbox-helper-text">해당 카테고리를 노출합니다.</p>
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
        </div>
        <!-- Area -->
    </div>
</div>

<?php end_section() ?>