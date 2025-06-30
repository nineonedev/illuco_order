import CartController from "../../controllers/CartController";
import View from "../../core/View";
import RadioInput from "../Inputs/RadioInput";

export default class LoupeForm extends View {
    _boot(){
        this._attrHookId = this._generateHookId();
        super._boot();
    }

    _defineProps(){
        return {};
    }

    _defineState(){
        return {
            ...this._props,
        }
    }

    _template(){
        const {type} = this._state;
        
        return `
            <div>
                <div data-ref="type"></div>
                
                <fieldset class="no-form-group">
                    <legend class="no-form-base-label">테정보</legend>
                    <div class="no-form-listing">
                        <div class="no-form-radio --sm">
                            <label class="no-form-radio-pointer" for="frame_1">
                                <input class="no-form-radio-input" type="radio" name="frame_type" id="frame_1" value="M">
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
                                <input class="no-form-radio-input" type="radio" name="frame_type" id="frame_2" value="F">
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
                                <input class="no-form-radio-input" type="radio" name="frame_type" id="frame_sports" value="sad">
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
                                <input class="no-form-radio-input" type="radio" name="frame_type" id="frame_4" value="frame_4">
                                <div class="no-form-radio-ripple">
                                    <div class="no-form-radio-box">
                                        <span class="no-form-radio-icon"></span>
                                    </div>
                                </div>
                                <span class="no-form-radio-text">Frame 4</span>
                            </label>
                        </div>
                    </div>
                    <span class="no-form-control-space"></span>
                </fieldset>

                <div class="no-form-control">
                    <label for="working_distance" class="no-form-control-inner">
                        <input type="text" name="working_distance" id="working_distance" class="no-form-control-input" placeholder="">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">WD (단위: Cm)</legend>
                        </fieldset>
                    </label>
                    <!-- <span class="no-form-control-helper-text">
                        해당 모델의 권장 작업 거리(Working Distance)는 <em>35~45cm</em> 범위 내로 입력해주세요.
                    </span> -->
                    <span class="no-form-control-space"></span>
                </div>

                <div id="${this._attrHookId}"></div>
            </div>
        `;
    }

    _render(){
        const {type} = this._state.type;
        super._render();

        RadioInput.make(this.refs.type, {
            label: '형태',
            name: 'type',
            value: type,
            options: [
                {label: 'Ready-made', value: 'ready-made'},
                {label: 'Custom-made', value: 'custom-made'},
            ],
            onChange: this._handleTypeChange.bind(this),
        }).render();

        console.log(CartController.attributes);

        // RadioInput.make(this.refs.frame, {
            
        // });

        // if (type !== 'custom-made') return; 

        // this._renderAttributes();
    }

    _handleTypeChange({value: type, view}, e) {
        this.setState({type: type});
    }

    _renderAttributes(){
        if (this._state.type !== 'custom-made') {
            throw new Error(`잘못된 타입입니다. : ${this._state.type}`);
        }

        return `
            <div class="no-form-flex">
                <!-- far PD, RIGHT -->
                <div class="no-form-control">
                    <label for="pd_right" class="no-form-control-inner">
                        <input type="number" name="pd_right" id="pd_right" class="no-form-control-input" placeholder=""
                            min="27" max="40">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">far PD, RIGHT (단위: mm)</legend>
                        </fieldset>
                    </label>
                    <!-- <span class="no-form-control-helper-text">
                        입력 가능 범위: <em>27 ~ 40 mm</em>
                    </span> -->
                </div>

                <!-- far PD, LEFT -->
                <div class="no-form-control">
                    <label for="pd_left" class="no-form-control-inner">
                        <input type="number" name="pd_left" id="pd_left" class="no-form-control-input" placeholder=""
                            min="27" max="40">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">far PD, LEFT (단위: mm)</legend>
                        </fieldset>
                    </label>
                    <!-- <span class="no-form-control-helper-text">
                        입력 가능 범위: <em>27 ~ 40 mm</em>
                    </span> -->
                </div>

                <!-- TOTAL PD -->
                <div class="no-form-control">
                    <label for="pd_total" class="no-form-control-inner">
                        <input type="number" name="pd_total" id="pd_total" class="no-form-control-input" placeholder="" readonly>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">TOTAL PD (단위: mm)</legend>
                        </fieldset>
                    </label>
                    <!-- <span class="no-form-control-helper-text">
                        좌우 편차가 ±2mm를 넘으면 다시 확인해주세요.
                    </span> -->
                </div>

                <!-- VD -->
                <div class="no-form-control">
                    <label for="vd" class="no-form-control-inner">
                        <input type="number" name="vd" id="vd" class="no-form-control-input" placeholder=""
                            min="10" max="25">
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">VD (단위: mm)</legend>
                        </fieldset>
                    </label>
                    <!-- <span class="no-form-control-helper-text">
                        입력 가능 범위: <em>10 ~ 25 mm</em>
                    </span> -->
                </div>
            </div>


            <div class="no-form-group">
                <span class="no-form-control-space"></span>

                <div class="no-form-table-inner">
                    <span class="no-form-base-label">시력정보</span>
                    <table class="no-form-table">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <span class="--blind">Eye</span>
                                </th>
                                <th scope="col">SPH</th>
                                <th scope="col">CYL</th>
                                <th scope="col">Axis</th>
                                <th scope="col">Add</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- OD -->
                            <tr>
                                <th scope="row">OD</th>
                                <td>
                                    <div class="no-form-control">
                                        <label for="od_sph" class="no-form-control-inner">
                                            <input type="number" 
                                                name="od_sph" 
                                                id="od_sph" 
                                                class="no-form-control-input" 
                                                placeholder=""
                                                step="0.25" 
                                                value="0.00"
                                                min="-20" max="20">
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="no-form-control">
                                        <label for="od_cyl" class="no-form-control-inner">
                                            <input type="number" 
                                                name="od_cyl" 
                                                id="od_cyl" 
                                                class="no-form-control-input" 
                                                placeholder=""
                                                step="0.25" 
                                                value="0.00"
                                                min="-10" max="10">
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="no-form-control">
                                        <label for="od_axis" class="no-form-control-inner">
                                            <input type="number" 
                                                name="od_axis" 
                                                id="od_axis" 
                                                class="no-form-control-input" 
                                                placeholder=""
                                                value="0"
                                                min="0" max="180">
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="no-form-control">
                                        <label for="od_add" class="no-form-control-inner">
                                            <input type="number" 
                                                name="od_add" 
                                                id="od_add" 
                                                class="no-form-control-input" 
                                                placeholder=""
                                                step="0.25"
                                                value="0.00"
                                                min="0" max="4">
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            <!-- OS -->
                            <tr>
                                <th scope="row">OS</th>
                                <td>
                                    <div class="no-form-control">
                                        <label for="os_sph" class="no-form-control-inner">
                                            <input type="number" 
                                                name="os_sph" 
                                                id="os_sph" 
                                                class="no-form-control-input" 
                                                placeholder=""
                                                step="0.25" 
                                                value="0.00"
                                                min="-20" max="20">
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="no-form-control">
                                        <label for="os_cyl" class="no-form-control-inner">
                                            <input type="number" 
                                                name="os_cyl" 
                                                id="os_cyl" 
                                                class="no-form-control-input" 
                                                placeholder=""
                                                step="0.25" 
                                                value="0.00"
                                                min="-10" max="10">
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="no-form-control">
                                        <label for="os_axis" class="no-form-control-inner">
                                            <input type="number" 
                                                name="os_axis" 
                                                id="os_axis" 
                                                class="no-form-control-input" 
                                                placeholder=""
                                                value="0"
                                                min="0" max="180">
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="no-form-control">
                                        <label for="os_add" class="no-form-control-inner">
                                            <input type="number" 
                                                name="os_add" 
                                                id="os_add" 
                                                class="no-form-control-input" 
                                                placeholder=""
                                                step="0.25"
                                                value="0.00"
                                                min="0" max="4">
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <p class="no-feedback-info">
                    <i class="fa-regular fa-circle-info"></i>
                    <span>사시나 특수 시력은 루페 제작이 불가능합니다.</span>
                </p>
            </div>



            <div class="no-form-group">
                <span class="no-form-control-space"></span>
                <span class="no-form-base-label">모렌즈 ADD 값 선택</span>
                <!-- 옵션 사항 1 -->
                <div class="no-form-radio --sm">
                    <label class="no-form-radio-pointer" for="add_option_1">
                        <input class="no-form-radio-input" type="radio" name="add_option" id="add_option_1" value="ignore">
                        <div class="no-form-radio-ripple">
                            <div class="no-form-radio-box">
                                <span class="no-form-radio-icon"></span>
                            </div>
                        </div>
                        <span class="no-form-radio-text">모렌즈에 ADD값 무시 요청 - 원용</span>
                    </label>
                    <p class="no-form-radio-helper-text">
                        ADD 값을 적용하지 않고 단일 초점(원용)으로 제작합니다.
                    </p>
                </div>

                <!-- 옵션 사항 2 -->
                <div class="no-form-radio --sm">
                    <label class="no-form-radio-pointer" for="add_option_2">
                        <input class="no-form-radio-input" type="radio" name="add_option" id="add_option_2" value="include">
                        <div class="no-form-radio-ripple">
                            <div class="no-form-radio-box">
                                <span class="no-form-radio-icon"></span>
                            </div>
                        </div>
                        <span class="no-form-radio-text">모렌즈에 ADD값 포함 요청 - 근거리용</span>
                    </label>
                    <p class="no-form-radio-helper-text">
                        ADD 값을 포함해 근거리 작업에 적합한 렌즈로 제작합니다.
                    </p>
                </div>

                <!-- 옵션 사항 3 -->
                <div class="no-form-radio --sm">
                    <label class="no-form-radio-pointer" for="add_option_3">
                        <input class="no-form-radio-input" type="radio" name="add_option" id="add_option_3" value="zero_diopter">
                        <div class="no-form-radio-ripple">
                            <div class="no-form-radio-box">
                                <span class="no-form-radio-icon"></span>
                            </div>
                        </div>
                        <span class="no-form-radio-text">모렌즈 0 디옵터 적용 - 안경 미착용자</span>
                    </label>
                    <p class="no-form-radio-helper-text">
                        안경을 착용하지 않는 분들을 위해 ADD 0 디옵터로 제작됩니다.
                    </p>
                </div>
            </div>

            <hr class="no-hr --xl">

            <fieldset class="no-form-section">
                <legend class="no-form-section__title">추가 제품</legend>
                <ul>
                    <li class="no-cartitem-set">
                        <div class="no-cartitem-set__present">
                            <div class="no-cartitem-set__present-block">
                                <div class="no-cartitem-set__img">
                                    <figure>
                                    <img src="/static/app/img/sub/mirrors.jpg'" alt="처방렌즈 이미지">
                                    </figure>
                                </div>
                                <div class="no-cartitem-set__detail">
                                    <div class="no-cartitem-set__info">
                                        <p class="no-text-sm">처방렌즈</p>
                                    </div>
                                    <div class="no-cartitem-set__price">
                                        <p>
                                            <b data-ref="price">$24.44</b>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="no-cartitem-set__action">
                                <div class="no-cartitem-set__quantity-control">
                                    <button type="button" class="no-btn --minus" disabled>-</button>
                                        <input type="text" readonly value="2">
                                    <button type="button" class="no-btn --plus" disabled>+</button>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </fieldset>
        `;
    }
}