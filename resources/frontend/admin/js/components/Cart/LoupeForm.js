import CartController from "../../controllers/CartController";
import View from "../../core/View";
import Helper from "../../supports/Helper";
import CheckboxInput from "../Inputs/CheckboxInput";
import NumberInput from "../Inputs/NumberInput";
import RadioInput from "../Inputs/RadioInput";
import TextInput from "../Inputs/TextInput";

export default class LoupeForm extends View {
    static PRECISON_LENS = 'PR-LENS-30';

    _boot() {
        this._attrHookId = this._generateHookId();
        this._optionHookId = this._generateHookId();
        this._lensCount = 0;
        super._boot();
    }

    _defineProps() {
        return {
            sets: [],
            template: {},
            product: {
                type: 'ready-made',
                frame_type: '',
                working_distance: '',
                engraving_text: '',
                add_option: '',
                vertex_distance: '',
                pd_right: '',
                pd_left: '',
                pd_total: '',
                od_sph: '0.00',
                os_sph: '0.00',
                od_cyl: '0.00',
                os_cyl: '0.00',
                od_axis: '0',
                os_axis: '0',
                od_add: '0.00',
                os_add: '0.00',
            },
            onChangeQuantity: (count) => {},
            onUpdateSets: (sets) => {},
        };
    }

    _defineState() {
        return {
            errors: {},
            ...this._props,
        };
    }

    _template() {
        return `
            <div>
                <div id="${this._attrHookId}"></div>
                <div data-ref="errors" class="no-error-hook"></div>
            </div>
        `;
    }

    async _render() {
        super._render();
        const { type, model } = this._state.template;
        
        const {
            type: loupeType,
            engraving_text,
            working_distance, 
            frame_type,
        } = this._state.product; 
        

        RadioInput.make(this._attrHookId, {
            label: "형태",
            name: "loupe[type]",
            value: loupeType,
            options: [
                { label: "Ready-made", value: "ready-made" },
                { label: "Custom-made", value: "custom-made" },
            ],
            onChange: this._handleTypeChange.bind(this),
        }).render();

        const hasEngraving = !(engraving_text === null || engraving_text.trim() === '');
        
        CheckboxInput.make(this._attrHookId, {
            label: "각인 여부",
            name: "loupe[use_engraving]",
            checked: hasEngraving, 
            onChange: this._handleEngraving.bind(this),
            helperText: '각인을 선택하시면 문구 입력이 가능하며, 발주 수량은 1개로 제한됩니다.',
        }).render();

        this.engravingInput = TextInput.make(this._attrHookId, {
            label: '각인 입력',
            name: 'loupe[engraving_text]',
            value: engraving_text,
            required: hasEngraving,
            display: hasEngraving, 
        }).render();

        if (hasEngraving) {
            this._props.onChangeQuantity(1, true);
        }

        
        const attributes = this._state.attributes; 
        if (!attributes) return;
        
        this._logger.success(model, attributes);

        const { frame_type: frame_types, working_distance:wd } = attributes;

        if (frame_types) {
            RadioInput.make(this._attrHookId, {
                label: "테정보",
                name: "loupe[frame_type]",
                value: frame_type,
                options: attributes.frame_types,
                onChange: Helper.debounce(
                    this._handleFrameTypeChange.bind(this),
                    300
                ),
            }).render();
        }

        if (wd) {
            NumberInput.make(this._attrHookId, {
                label: "WD (단위:Cm)",
                name: "loupe[working_distance]",
                value: working_distance,
                min: wd.min,
                max: wd.max,
                step: 0.1,
                onChange: Helper.debounce(
                    this._handleWorkingDistanceChange.bind(this),
                    300
                ),
            }).render();
        }


        if (loupeType === "custom-made") {
            this._renderAttributes();
            this._bindCustomValidation();

            if (this._state.sets && this._state.sets.length > 0) {
                this._checkSphCylAddRules();
            }
        }
    }

    // --------------------------
    // Individual Handlers
    // --------------------------

    _handleEngraving({value}) {
        this.engravingInput.setState({display: value, required: value});

        if (value) {
            this._props.onChangeQuantity(1, true);
        } else {
            this._props.onChangeQuantity(1, false);
        }
    }

    _handleTypeChange({ value }) {
        this.setState({ product: {...this._state.product, type: value} });

        if (value !== "custom-made") {
            this._state.sets = [];
        }

        this._props.onUpdateSets(this._state.sets);
        this._props.onChangeQuantity(1, false);
    }

    _handleFrameTypeChange({ value }) {
        const allowed = this._state.attributes?.frame_type?.map((x) => x.value) || [];
            
        if (!value) {
            this._setFieldError(
                "loupe[frame_type]",
                "테정보는 필수 입력 항목입니다."
            );
        } else if (!allowed.includes(value)) {
            this._setFieldError(
                "loupe[frame_type]",
                "유효하지 않은 테정보입니다."
            );
        } else {
            this._clearFieldError("loupe[frame_type]");
        }
    }

    _handleWorkingDistanceChange({ value }) {
        const spec = this._getSpecForField("loupe[working_distance]");
        const num = parseFloat(value);
        if (!value) {
            this._setFieldError(
                "loupe[working_distance]",
                "WD 값은 필수입니다."
            );
        } else if (num < spec.min || num > spec.max) {
            this._setFieldError(
                "loupe[working_distance]",
                `WD 값은 ${spec.min} ~ ${spec.max} cm 범위여야 합니다.`
            );
        } else {
            this._clearFieldError("loupe[working_distance]");
        }
    }

    _handleAddOptionChange() {
        const el = document.querySelector(`[name="loupe[add_option]"]:checked`);
        const val = el?.value ?? "";
        const allowed = ["ignore", "include", "zero_diopter"];

        

        if (!val) {
            this._setFieldError(
                "loupe[add_option]",
                "ADD 옵션 선택은 필수입니다."
            );
        } else if (!allowed.includes(val)) {
            this._setFieldError(
                "loupe[add_option]",
                "선택하신 ADD 옵션이 유효하지 않습니다."
            );
        } else {
            this._clearFieldError("loupe[add_option]");
        }
    }

    // --------------------------
    // Central Validation Trigger
    // --------------------------

    validateAllFields() {
        this._handleFrameTypeChange({
            value:
                document.querySelector(`[name="loupe[frame_type]"]:checked`)
                    ?.value || "",
        });

        this._handleWorkingDistanceChange({
            value:
                document.querySelector(`[name="loupe[working_distance]"]`)
                    ?.value || "",
        });

        if (this._state.product.type === "custom-made") {
            this._handleAddOptionChange();

            const customFields = [
                "loupe[vertex_distance]",
                "loupe[pd_right]",
                "loupe[pd_left]",
                "loupe[od_sph]",
                "loupe[os_sph]",
                "loupe[od_cyl]",
                "loupe[os_cyl]",
                "loupe[od_axis]",
                "loupe[os_axis]",
                "loupe[od_add]",
                "loupe[os_add]",
            ];

            for (const name of customFields) {
                const el = document.querySelector(`[name="${name}"]`);
                if (!el) continue;

                const spec = this._getSpecForField(name);
                const value = el.value;

                if (spec) {
                    const num = parseFloat(value);
                    if (value === "") {
                        this._setFieldError(
                            name,
                            `${el.dataset.label || name} 값은 필수입니다.`
                        );
                    } else if (num < spec.min || num > spec.max) {
                        this._setFieldError(
                            name,
                            `${el.dataset.label || name} 값은 ${spec.min} ~ ${
                                spec.max
                            } 범위여야 합니다.`
                        );
                    } else {
                        this._clearFieldError(name);
                    }
                }
            }

            this._checkSphCylAddRules();
        }
    }

    // --------------------------
    // Helpers
    // --------------------------

    _getSpecForField(name = null) {
        const map = {
            "loupe[vertex_distance]": { min: 10, max: 25 },
            "loupe[pd_right]": { min: 27, max: 40 },
            "loupe[pd_left]": { min: 27, max: 40 },
            "loupe[od_sph]": { min: -20, max: 20, step: 0.25 },
            "loupe[os_sph]": { min: -20, max: 20, step: 0.25 },
            "loupe[od_cyl]": { min: -10, max: 10, step: 0.25 },
            "loupe[os_cyl]": { min: -10, max: 10, step: 0.25 },
            "loupe[od_axis]": { min: 0, max: 180 },
            "loupe[os_axis]": { min: 0, max: 180 },
            "loupe[od_add]": { min: 0, max: 4, step: 0.25 },
            "loupe[os_add]": { min: 0, max: 4, step: 0.25 },
            "loupe[frame_type]": {
                allowedValues:
                    CartController.attributes.loupe[
                        this._state.template.model
                    ]?.frame_types.map((x) => x.value) || [],
            },
            "loupe[working_distance]":
                CartController.attributes.loupe[this._state.template.model]
                    ?.working_distance || null,
        };
        return name ? map[name] : map;
    }

    _setFieldError(name, message) {
        this._state.errors[name] = message;
        this._renderErrors();
    }

    _clearFieldError(name) {
        if (this._state.errors[name]) {
            delete this._state.errors[name];
            this._renderErrors();
        }
    }

    hasErrors() {
        return Object.keys(this._state.errors).length > 0;
    }

    /**
     * 각 인풋 바로 아래에 에러 렌더
     * OD/OS 그룹 에러는 표 밑에 한 번에 렌더링
     */
    _renderErrors() {
        // 먼저 모든 에러 노드를 싹 지운다
        const allErrors = document.querySelectorAll(".no-form-error-msg");
        allErrors.forEach(($el) => $el.remove());

        let odOsErrors = [];

        for (const [name, msg] of Object.entries(this._state.errors)) {
            let el;

            if (name === "loupe[add_option]") {
                el = document.querySelector(
                    `[data-error-for="loupe[add_option]"]`
                );
            } else if (name === "loupe[frame_type]") {
                el = document.querySelector(
                    `[data-error-for="loupe[frame_type]"]`
                );
            } else {
                el = document.querySelector(`[name="${name}"]`);
            }

            if (el) {
                let $error =
                    el.parentElement.querySelector(".no-form-error-msg");
                if (!$error) {
                    $error = document.createElement("div");
                    $error.className = "no-form-error-msg";
                    el.parentElement.appendChild($error);
                }
                $error.innerHTML = msg;
            }
        }

        // OD/OS 표 밑에 출력
        const tableWrapper = document.querySelector(".no-form-table-inner");
        if (tableWrapper) {
            let tableErrorEl = tableWrapper.querySelector(
                ".no-form-table-error"
            );
            if (!tableErrorEl) {
                tableErrorEl = document.createElement("div");
                tableErrorEl.className = "no-form-table-error";
                tableWrapper.appendChild(tableErrorEl);
            }
            tableErrorEl.innerHTML = odOsErrors.join("<br>");
        }
    }

    _bindCustomValidation() {
        const inputHandlers = {
            "loupe[pd_right]": this._handleFieldValidation.bind(this),
            "loupe[pd_left]": this._handleFieldValidation.bind(this),

            "loupe[vertex_distance]": this._handleFieldValidation.bind(this),

            "loupe[od_sph]": this._handleFieldValidation.bind(this),
            "loupe[od_cyl]": this._handleFieldValidation.bind(this),
            "loupe[od_axis]": this._handleFieldValidation.bind(this),
            "loupe[od_add]": this._handleFieldValidation.bind(this),

            "loupe[os_sph]": this._handleFieldValidation.bind(this),
            "loupe[os_cyl]": this._handleFieldValidation.bind(this),
            "loupe[os_axis]": this._handleFieldValidation.bind(this),
            "loupe[os_add]": this._handleFieldValidation.bind(this),
        };

        const allInputs = document.querySelectorAll(
            `#${this._attrHookId} input`
        );
        allInputs.forEach((el) => {
            const name = el.getAttribute("name");
            const handler = inputHandlers[name];
            if (handler) {
                el.addEventListener(
                    "input",
                    Helper.debounce((e) => {
                        handler({ name, value: e.target.value });
                        this._checkSphCylAddRules();
                    }, 300)
                );
            }
        });

        const addOptionEls = document.querySelectorAll(
            '[name="loupe[add_option]"]'
        );
        
        addOptionEls.forEach((el) => {
            el.addEventListener("change", () => {
                this._handleAddOptionChange();
            });
        });

        const pdRightEl = document.querySelector('[name="loupe[pd_right]"]');
        const pdLeftEl = document.querySelector('[name="loupe[pd_left]"]');
        const pdTotalEl = document.querySelector('[name="loupe[pd_total]"]');
        

        if (pdRightEl && pdLeftEl && pdTotalEl) {
            const checkPdDiff = () => {
                const right = parseFloat(pdRightEl.value || "0");
                const left = parseFloat(pdLeftEl.value || "0");
                const total = right + left;
                
                pdTotalEl.value = total ? total.toFixed(1) : "";

                if (Math.abs(right - left) > 2) {
                    this._setFieldError(
                        "loupe[pd_total]",
                        "좌우 PD 차이가 ±2mm를 초과합니다."
                    );
                } else {
                    this._clearFieldError("loupe[pd_total]");
                }
            };

            pdRightEl.addEventListener(
                "input",
                Helper.debounce(checkPdDiff, 300)
            );
            pdLeftEl.addEventListener(
                "input",
                Helper.debounce(checkPdDiff, 300)
            );
        }
    }

    _checkSphCylAddRules() {
        const getVal = (name) =>
            parseFloat(
                document.querySelector(`[name="${name}"]`)?.value || "0"
            );
        const odSph = getVal("loupe[od_sph]");
        const odCyl = getVal("loupe[od_cyl]");
        const odAxis = getVal("loupe[od_axis]");
        const odAdd = getVal("loupe[od_add]");

        const osSph = getVal("loupe[os_sph]");
        const osCyl = getVal("loupe[os_cyl]");
        const osAxis = getVal("loupe[os_axis]");
        const osAdd = getVal("loupe[os_add]");

        const odSum = odSph + odCyl;
        const osSum = osSph + osCyl;

        const errors = [];

        if (odSum < -10 || odSum > 6) {
            errors.push(
                `OD (오른쪽 눈) SPH + CYL 합이 -10 ~ +6 범위를 벗어났습니다. 현재 ${odSum.toFixed(
                    2
                )}`
            );
        }
        if (osSum < -10 || osSum > 6) {
            errors.push(
                `OS (왼쪽 눈) SPH + CYL 합이 -10 ~ +6 범위를 벗어났습니다. 현재 ${osSum.toFixed(
                    2
                )}`
            );
        }
        if (odAxis < 0 || odAxis > 180) {
            errors.push(`OD axis 값은 0~180 사이여야 합니다.`);
        }
        if (osAxis < 0 || osAxis > 180) {
            errors.push(`OS axis 값은 0~180 사이여야 합니다.`);
        }
        if (odAdd < 0 || osAdd < 0) {
            errors.push(`Add 값은 음수일 수 없습니다.`);
        }

        if (errors.length > 0) {
            this._setFieldError("loupe[sph_cyl_rule]", errors.join("<br>"));
        } else {
            this._clearFieldError("loupe[sph_cyl_rule]");
        }

        let count = 0;
        if (odSph + odCyl !== 0) count += 1;
        if (osSph + osCyl !== 0) count += 1;

        this._lensCount = count;
        this._renderAdditionalProducts();
        this._props.onUpdateSets(this._state.sets);
    }

    _handleFieldValidation({ name, value }) {
        const spec = this._getSpecForField(name);
        if (!spec) return;

        const num = parseFloat(value);
        if (value === "") {
            this._setFieldError(
                name,
                `${
                    document.querySelector(`[name="${name}"]`)?.dataset.label ||
                    name
                } 값은 필수입니다.`
            );
        } else if (num < spec.min || num > spec.max) {
            this._setFieldError(
                name,
                `${
                    document.querySelector(`[name="${name}"]`)?.dataset.label ||
                    name
                } 값은 ${spec.min} ~ ${spec.max} 범위여야 합니다.`
            );
        } else {
            this._clearFieldError(name);
        }
    }

    _renderAdditionalProducts() {
        const lens = CartController.findSetGroupItemByModel(LoupeForm.PRECISON_LENS);
        this._state.sets = [];

        if (!lens || this._lensCount === 0) {
            document.getElementById(this._optionHookId).innerHTML = "";
            return;
        }

        const quantity = this._lensCount;
        const subTotal = lens.price * this._lensCount;

        const formattedPrice = Helper.formatCurrency(lens.price);
        this._state.sets.push({
            name: lens.name,
            price: lens.price,
            quantity: quantity,
            subTotal: subTotal,
        });

        const image =
            lens.fileattachment?.[0]?.upload_path ||
            "/static/app/img/meta/thumb.jpg";

        const index = 0;

        const html = `
            <hr class="no-hr --xl">
            <fieldset class="no-form-section">
                <legend class="no-form-section__title">추가 제품</legend>
                <ul>
                    <li class="no-cartitem-set">
                        <input type="hidden" name="sets[${index}][product][template_id]" value="${lens.id}" />
                        <input type="hidden" name="sets[${index}][product][code]" value="${lens.code}" />
                        <input type="hidden" name="sets[${index}][product][model]" value="${lens.model}" />
                        <input type="hidden" name="sets[${index}][product][price]" value="${lens.price}" />
                        <input type="hidden" name="sets[${index}][product][name]" value="${lens.name}" />
                        <input type="hidden" name="sets[${index}][product][description]" value="${lens.name}" />

                        <div class="no-cartitem-set__present">
                            <div class="no-cartitem-set__present-block">
                                <div class="no-cartitem-set__img">
                                    <figure>
                                        <img src="${image}" alt="${lens.name}">
                                    </figure>
                                </div>
                                <div class="no-cartitem-set__detail">
                                    <div class="no-cartitem-set__info">
                                        <p class="no-text-sm">처방렌즈</p>
                                    </div>
                                    <div class="no-cartitem-set__price">
                                        <p><b data-ref="price">${formattedPrice}</b></p>
                                    </div>
                                </div>
                            </div>
                            <div class="no-cartitem-set__action">
                                <div class="no-cartitem-set__quantity-control">
                                    <button type="button" class="no-btn --minus" disabled>-</button>
                                    <input type="text" name="sets[${index}][quantity]" readonly value="${quantity}">
                                    <button type="button" class="no-btn --plus" disabled>+</button>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </fieldset>
        `;
        document.getElementById(this._optionHookId).innerHTML = html;
    }

    _renderAttributes() {
        const template = document.createElement("template");
        template.innerHTML = this._attributesHtml().trim();
        const element = template.content.firstElementChild;
        document.getElementById(this._attrHookId).append(element);
    }

    _attributesHtml() {
        const {
            pd_right,
            pd_left,
            pd_total,
            od_sph,
            os_sph,
            od_cyl,
            os_cyl,
            od_axis,
            os_axis,
            od_add,
            os_add,
            vertex_distance,
            add_option,
        } = this._state.product; 
        

        return `
            <div>
                <div>
                <div class="no-form-flex">
                    <!-- far PD, RIGHT -->
                    <div class="no-form-control">
                        <label for="pd_right" class="no-form-control-inner">
                            <input 
                                type="number" 
                                name="loupe[pd_right]" 
                                id="pd_right" 
                                class="no-form-control-input" 
                                placeholder=""
                                min="27" 
                                max="40" 
                                data-label="far PD, RIGHT (단위: mm)"
                                value="${pd_right}"
                            >
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
                            <input 
                                type="number" 
                                name="loupe[pd_left]" 
                                id="pd_left" 
                                class="no-form-control-input" 
                                placeholder=""
                                min="27" 
                                max="40" 
                                data-label="far PD, LEFT (단위: mm)" 
                                value="${pd_left}"
                            >
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
                            <input 
                                type="number" 
                                name="loupe[pd_total]" 
                                id="pd_total" 
                                class="no-form-control-input" 
                                placeholder="" 
                                readonly
                                value="${pd_total}"
                            >
                            <fieldset class="no-form-control-label" data-label="TOTAL PD (단위: mm)">
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
                            <input 
                                type="number" 
                                name="loupe[vertex_distance]" 
                                id="vd" 
                                class="no-form-control-input" 
                                placeholder=""
                                min="10" 
                                max="25" 
                                data-label="VD (단위: mm)" 
                                value="${vertex_distance}"
                            >
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
                                                    name="loupe[od_sph]" 
                                                    data-label="OD-SPH"
                                                    id="od_sph" 
                                                    class="no-form-control-input" 
                                                    placeholder=""
                                                    step="0.25" 
                                                    value="${od_sph}"
                                                    min="-20" max="20"
                                                />
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="no-form-control">
                                            <label for="od_cyl" class="no-form-control-inner">
                                                <input type="number" 
                                                    name="loupe[od_cyl]" 
                                                    id="od_cyl" 
                                                    data-label="OD-CYL"
                                                    class="no-form-control-input" 
                                                    placeholder=""
                                                    step="0.25" 
                                                    value="${od_cyl}"
                                                    min="-10" max="10">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="no-form-control">
                                            <label for="od_axis" class="no-form-control-inner">
                                                <input type="number" 
                                                    data-label="OD-Axis"
                                                    name="loupe[od_axis]" 
                                                    id="od_axis" 
                                                    class="no-form-control-input" 
                                                    placeholder=""
                                                    value="${od_axis}"
                                                    min="0" max="180">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="no-form-control">
                                            <label for="od_add" class="no-form-control-inner">
                                                <input type="number" 
                                                    name="loupe[od_add]" 
                                                    id="od_add" 
                                                    data-label="OD-Add"
                                                    class="no-form-control-input" 
                                                    placeholder=""
                                                    step="0.25"
                                                    value="${od_add}"
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
                                                    name="loupe[os_sph]" 
                                                    id="os_sph" 
                                                    data-label="OS-SPH"
                                                    class="no-form-control-input" 
                                                    placeholder=""
                                                    step="0.25" 
                                                    value="${os_sph}"
                                                    min="-20" max="20">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="no-form-control">
                                            <label for="os_cyl" class="no-form-control-inner">
                                                <input type="number" 
                                                    name="loupe[os_cyl]" 
                                                    id="os_cyl" 
                                                    data-label="OS-CYL"
                                                    class="no-form-control-input" 
                                                    placeholder=""
                                                    step="0.25" 
                                                    value="${os_cyl}"
                                                    min="-10" max="10">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="no-form-control">
                                            <label for="os_axis" class="no-form-control-inner">
                                                <input type="number" 
                                                    name="loupe[os_axis]"
                                                    data-label="OS-Axis" 
                                                    id="os_axis" 
                                                    class="no-form-control-input" 
                                                    placeholder=""
                                                    value="${os_axis}"
                                                    min="0" max="180">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="no-form-control">
                                            <label for="os_add" class="no-form-control-inner">
                                                <input type="number" 
                                                    name="loupe[os_add]" 
                                                    data-label="OS-Add"
                                                    id="os_add" 
                                                    class="no-form-control-input" 
                                                    placeholder=""
                                                    step="0.25"
                                                    value="${os_add}"
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
                    <div data-error-for="loupe[add_option]">
                        <!-- 옵션 사항 1 -->
                        <div class="no-form-radio --sm">
                            <label class="no-form-radio-pointer" for="add_option_1">
                                <input 
                                    class="no-form-radio-input" 
                                    type="radio" 
                                    name="loupe[add_option]" 
                                    id="add_option_1" 
                                    value="ignore" 
                                    data-label="모렌즈 ADD 값 선택"
                                    ${add_option === 'ignore' ? 'checked' : ''}
                                />
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
                                <input 
                                    class="no-form-radio-input" 
                                    type="radio" 
                                    name="loupe[add_option]" 
                                    id="add_option_2" 
                                    value="include" 
                                    data-label="모렌즈 ADD 값 선택"
                                    ${add_option === 'include' ? 'checked' : ''}
                                />
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
                                <input 
                                    class="no-form-radio-input" 
                                    type="radio" 
                                    name="loupe[add_option]" 
                                    id="add_option_3" 
                                    value="zero_diopter" 
                                    data-label="모렌즈 ADD 값 선택"
                                    ${add_option === 'zero_diopter' ? 'checked' : ''}
                                />
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
                </div>
                <div id="${this._optionHookId}"></div>
            </div>
        `;
    }
}
