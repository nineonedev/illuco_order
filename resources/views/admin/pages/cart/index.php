<?php 

?>
<?php extend('layouts.admin'); ?>
<?php section('controller', 'cart') ?>
<?php section('action', 'index') ?>
<?php section('title', '주문') ?>

<?php section('content') ?>
<div class="no-page-row">
    <div class="no-page-head">
        <h1 class="no-heading-sm">대상 선택</h1>
        <p>등록된 사용자의 주문을 대행 할 수 있습니다.</p>
    </div>

    <div class="no-page-flex">
        <div id="template-hook"></div>
        <div id="cart-hook"></div>
        <!-- <div>
            <ol id="view-hook-16" class="no-cart-items">
                <li class="no-cart-item" id="view-element-55">
                    <div class="no-cart-item-head">
                        <div id="view-hook-36"><fieldset class="no-form-group" id="view-element-56">
                    <div class="no-form-checkbox --md">
                        <label class="no-form-checkbox-pointer">
                            <input type="checkbox" name="id" value="26" class="no-form-checkbox-input" checked="">
                            <div class="no-form-checkbox-ripple">
                                <span class="no-form-checkbox-box">
                                    <div class="no-form-checkbox-icon">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </span>
                            </div>
                            <span class="no-form-checkbox-text">Angled22</span>
                        </label>
                    </div>
                    
                </fieldset></div>
                        <div id="view-hook-37"><button type="button" class="no-btn-move --md" aria-label="아이템 제거" id="view-element-57">
                    <i class="fa-regular fa-xmark"></i>
                </button></div>
                    </div>

                    <div class="no-cart-item-present">
                        <div class="no-cart-item-present-block">
                            <div class="no-cart-item-present__img">
                                <figure>
                                    <img src="/static/uploads/producttemplate/c02f1ae3bd0799b2ee8847ca8e0bb791.jpg" alt="Angled22">
                                </figure>
                            </div>
                            <div class="no-cart-item-present-detail">
                                <div class="no-cart-item-present__info">
                                    <p class="no-text-sm">코드: LP</p>
                                    <p class="no-text-sm">모델명: ITL-1040P</p>
                                </div>
                                <div class="no-cart-item-present__price">
                                    <p>가격: <b data-ref="price">$2,040.82</b></p>
                                </div>
                            </div>
                        </div>
                        <div id="view-hook-35" class="no-cart-item-action">
                            <button type="button" class="no-btn-primary-outline --sm" aria-label="아이템 수정" id="view-element-58">
                                <span>수정</span>
                            </button>
                            <fieldset class="no-form-group" id="view-element-59">
                                <div class="no-cart-item-present__counter">
                                    <button type="button" class="--decrease" data-ref="decrease">
                                        <i class="fa-regular fa-minus"></i>
                                    </button>
                                    <div class="--input">
                                        <input type="number" id="view-element-60" name="quantity" value="1" min="1" data-ref="input">
                                    </div>
                                    <button type="button" class="--increase" data-ref="increase">
                                        <i class="fa-regular fa-plus"></i>
                                    </button>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div>
                        <ol class="no-cart-sublist">
                            <li class="no-cart-subitem">
                                <figure class="no-cart-subitem-img">
                                <img src="/static/uploads/producttemplate/c02f1ae3bd0799b2ee8847ca8e0bb791.jpg" alt="">
                                </figure>
                                <div class="no-cart-subitem-info">
                                    <p class="no-text-sm">코드: LP</p>
                                    <p class="no-text-sm">모델명: ITL-1040P</p>
                                    <p class="no-text-sm">수량: <em>3</em></p>
                                    <p class="no-text-sm">가격: $60</p>
                                </div>
                            </li>
                        </ol>

                    </div>
                </li>
            </ol>
        </div> -->
    </div>
</div>


<?php end_section() ?>

<?php section('portal') ?>
<div id="modal-hook"></div>
<?php end_section() ?>