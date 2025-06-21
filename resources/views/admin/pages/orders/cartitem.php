<?php extend('layouts.admin'); ?>

<?php section('title') ?>
주문
<?php end_section() ?>

<?php section('content') ?>
<div class="no-form-outer no-cart">

    <div class="no-cart-grid">
        <div class="no-cart-area">
            <div class="no-page-row">
                <div class="no-page-head">
                    <h1 class="no-heading-sm">주문</h1>
                    <p class="no-text-secondary">해당 대리점에 등록된 사용자의 주문을 대행할 수 있습니다.</p> 
                </div>
                <!-- Head -->

                <form method="post" enctype="multipart/form-data" action="">
                    <div class="no-form-group">
                        <h2 class="no-form-group-title no-body-lg">
                            대상선택
                        </h2>

                        <div class="no-form-control --md">
                            <label for="product_id" class="no-form-control-inner">
                                <input type="text" name="product_id" id="product_id" class="no-form-control-input" value="Wang Bing Ning" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">주문자 선택</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                        <div class="no-form-control --md">
                            <label for="product_id" class="no-form-control-inner">
                                <input type="text" name="product_id" id="product_id" class="no-form-control-input" value="IAL-1020 Angled Loupes" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">제품 선택</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>

                        
                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" value="IMAG35R" placeholder="" readonly>
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">코드</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>

                        <div class="no-form-control --md">
                            <label for="prod_model" class="no-form-control-inner">
                                <input type="text" name="prod_model" id="prod_model" class="no-form-control-input" value="IAL-1020 Angled Loupes" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">제품 모델 번호</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>

                        <div class="no-form-control --md">
                            <label for="price" class="no-form-control-inner">
                                <input type="text" name="price" id="price" class="no-form-control-input" value="$3,050.00 USD" placeholder="" readonly>
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">단가</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>

                        <div class="no-form-control --textarea">
                            <label for="prod_desc" class="no-form-control-inner">
                                <textarea type="text" name="prod_desc" id="prod_desc" rows="4" class="no-form-control-input" placeholder="">Ergonomic Angled Loupes are custom made. After checkout, you'll receive an order form to specify your Pupillary Distance (PD), Working Distance (WD), and prescription details for a perfect fit.</textarea>
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">제품 설명</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                        
                    </div>
                    
                    <hr class="no-form-hr">
                    <span class="no-form-control-space"></span>

                    <div class="no-form-group">
                        <h2 class="no-form-group-title no-body-lg">
                            제품사양
                        </h2>

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

                        
                        <div class="no-form-control --md">
                            <label for="Frame 1" class="no-form-control-inner">
                                <input type="text" name="Frame 1" id="Frame 1" class="no-form-control-input" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">WD (단위 cm)</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>

                        
                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">fad PD, RIGHT (단위 mm)</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>

                        
                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">fad PD, LEFT (단위 mm)</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>

                        
                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">VD 단위</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>

                        
                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">TOTAL PD</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                    </div>

                    <hr class="no-form-hr">
                    <span class="no-form-control-space"></span>

                    <div class="no-form-group">
                        <h2 class="no-form-group-title no-body-lg">
                            시력정보(좌)
                        </h2>
                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">SPH</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">CYL</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">Axis</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">Add</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                    </div>
                    
                    <hr class="no-form-hr">
                    <span class="no-form-control-space"></span>

                    <div class="no-form-group">
                        <h2 class="no-form-group-title no-body-lg">
                            시력정보(우)
                        </h2>
                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">SPH</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">CYL</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">Axis</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                        <div class="no-form-control --md">
                            <label for="title" class="no-form-control-inner">
                                <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                                <fieldset class="no-form-control-label">
                                    <legend class="no-form-control-text">Add</legend>
                                </fieldset>
                            </label>
                            <span class="no-form-control-space"></span>
                        </div>
                    </div>

                    <div class="no-form-control --md">
                        <label for="title" class="no-form-control-inner">
                            <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">처방렌즈 개수</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --md">
                        <label for="title" class="no-form-control-inner">
                            <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">발주수량</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="--flex-column">
                        <span class="no-form-base-label">모렌즈</span>
                        <div class="no-form-group">
                            <div class="no-form-radio --sm">
                                <label class="no-form-radio-pointer" for="hobbie_1">
                                    <input class="no-form-radio-input" type="radio" name="hobbies" id="hobbie_1">
                                    <div class="no-form-radio-ripple">
                                        <div class="no-form-radio-box">
                                            <span class="no-form-radio-icon"></span>
                                        </div>
                                    </div>
                                    <span class="no-form-radio-text">모렌즈에 ADD값 무시 요청 - 원용</span>
                                </label>
                            </div>
                            <div class="no-form-radio --sm">
                                <label class="no-form-radio-pointer" for="hobbie_2">
                                    <input class="no-form-radio-input" type="radio" name="hobbies" id="hobbie_2">
                                    <div class="no-form-radio-ripple">
                                        <div class="no-form-radio-box">
                                            <span class="no-form-radio-icon"></span>
                                        </div>
                                    </div>
                                    <span class="no-form-radio-text">모렌즈에 ADD값 포함 요청 - 근거리용</span>
                                </label>
                            </div>
                            <div class="no-form-radio --sm">
                                <label class="no-form-radio-pointer" for="hobbie_3">
                                    <input class="no-form-radio-input" type="radio" name="hobbies" id="hobbie_3">
                                    <div class="no-form-radio-ripple">
                                        <div class="no-form-radio-box">
                                            <span class="no-form-radio-icon"></span>
                                        </div>
                                    </div>
                                    <span class="no-form-radio-text">모렌즈 0 디옵터 적용 - 안경 미착용자</span>
                                </label>
                            </div>
                        </div>
                        <span class="no-form-control-space"></span>
                    </div>

                    
                    <div class="no-form-control --md">
                        <label for="title" class="no-form-control-inner">
                            <input type="number" name="title" id="title" class="no-form-control-input" placeholder="">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">처방렌즈 개수</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --md">
                        <label for="title" class="no-form-control-inner">
                            <input type="text" name="title" id="title" class="no-form-control-input" placeholder="">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">발주수량</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --textarea">
                        <label for="memo" class="no-form-control-inner">
                            <textarea type="text" name="memo" id="memo" rows="8" class="no-form-control-input" placeholder=""></textarea>
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">메모</legend>
                            </fieldset>
                        </label>
                    </div>

                    <div class="no-cart-aggregation">
                        <dl>
                            <dt>단가</dt>
                            <dd>$1,605.38</dd>
                        </dl>
                        <dl>
                            <dt>처방렌즈</dt>
                            <dd>$31.08</dd>
                        </dl>
                        <dl>
                            <dt>총 제품 가격</dt>
                            <dd><b>$1,636.46</b></dd>
                        </dl>
                    </div>

                    <!-- 액션 버튼 -->
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
        <div class="no-cart-area">
            <div class="no-page-row">
                <div class="no-page-head">
                    <h1 class="no-heading-sm">장바구니 <span class="no-cart-count">3</span></h1>
                </div>
                <!-- Head -->

                <!-- <div class="no-card --xl">
                    <div class="no-cart-empty">
                        <i class="fa-light fa-cart-flatbed-empty"></i>
                        <p>No Selected Products</p>
                    </div>
                </div> -->

                <ul class="no-cart-items">
                    <?php  for ($i = 0; $i < 3; $i++) : ?>
                    <li class="no-cart-item no-card --xs">
                        <div class="no-cart-item-head">
                            <div class="no-form-block">
                                <div class="no-form-checkbox --sm">
                                    <label for="angeld_loupe_<?=$i?>" class="no-form-checkbox-pointer">
                                        <input type="checkbox" name="games" id="angeld_loupe_<?=$i?>" class="no-form-checkbox-input" >
                                        <div class="no-form-checkbox-ripple">
                                            <span class="no-form-checkbox-box">
                                                <div class="no-form-checkbox-icon">
                                                    <i class="fa-solid fa-check"></i>
                                                </div>
                                            </span>
                                        </div>
                                        <span class="no-form-checkbox-text">IAL-1020 Angled Loupes</span>
                                    </label>
                                </div>
                            </div>
                            <!-- FormControl -->
                            <button type="button" class="no-btn-icon --sm">
                                <i class="fa-light fa-xmark"></i>
                            </button>
                        </div>
                        <div class="no-cart-item-present">
                            <div class="no-cart-item-present-block">
                                <div class="no-cart-item-present__img">
                                    <figure>
                                        <img src="" alt="">
                                    </figure>
                                </div>
                                <div class="no-cart-item-present__counter">
                                    <button type="button" class="--decrease">
                                        <i class="fa-light fa-minus"></i>
                                    </button>
                                    <div class="--input">
                                        <input type="text" value="1">
                                    </div>   
                                    <button type="button" class="--increase">
                                        <i class="fa-light fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <span class="no-cart-item-present__price">$2,485.21</span>
                        </div>
                    </li>
                    <?php endfor; ?>
                </ul>


                <div>
                    <div class="no-form-control --textarea">
                        <label for="memo" class="no-form-control-inner">
                            <textarea type="text" name="memo" id="memo" rows="4" class="no-form-control-input" placeholder=""></textarea>
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">주문 메모</legend>
                            </fieldset>
                        </label>
                    </div>
                    <div class="no-cart-aggregation">
                        <dl>
                            <dt>총 제품 가격</dt>
                            <dd><b>$4,970.42</b></dd>
                        </dl>
                    </div>
                    <button type="submit" class="no-btn-primary --sm">
                        <span>총 2개 제품 주문하기</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- Area -->
    </div>
</div>

<?php end_section() ?>