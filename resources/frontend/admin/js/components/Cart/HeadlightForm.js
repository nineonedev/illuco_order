import View from "../../core/View";
import CheckboxInput from "../Inputs/CheckboxInput";
import TextInput from "../Inputs/TextInput";
import RadioInput from "../Inputs/RadioInput";
import CartController from "../../controllers/CartController";
import Helper from "../../supports/Helper";

export default class HeadlightForm extends View {
    _boot() {
        this._attrHookId = this._generateHookId();
        super._boot();
    }

    _defineProps() {
        return {
            template: null,
            attributes: null,
            product: {
                engraving_text: "",
                wireless_color: "",
            },
            errors: {},
        };
    }

    _defineState() {
        return {
            ...this._props,
        };
    }

    _template() {
        return `
            <div>
                <div id="${this._attrHookId}"></div>
            </div>
        `;
    }

    _render() {
        super._render();

        const { engraving_text, wireless_color } = this._state.product;

        const hasEngraving = !(engraving_text === null || engraving_text.trim() === "");

        CheckboxInput.make(this._attrHookId, {
            label: "각인 여부",
            name: "headlight[use_engraving]",
            checked: hasEngraving,
            onChange: this._handleEngraving.bind(this),
            helperText: "각인을 선택하시면 문구 입력이 가능하며, 발주 수량은 1개로 제한됩니다.",
        }).render();

        this.engravingInput = TextInput.make(this._attrHookId, {
            label: "각인 입력",
            name: "headlight[engraving_text]",
            value: engraving_text,
            display: hasEngraving,
        }).render();


        if (hasEngraving) {
            this._props.onChangeQuantity(1, true);
        }

        // Attributes
        const attributes = this._state.attributes; 
        if (!attributes) return; 

        const {
            wireless_colors
        } = attributes;

        if (wireless_colors) {
            RadioInput.make(this._attrHookId, {
                label: "무선 컬러 선택",
                name: "headlight[wireless_color]",
                value: wireless_color,
                options: wireless_colors,
                onChange: Helper.debounce(
                this._handleColorChange.bind(this),
                    300
                ),
            }).render();
        }
        
    }

    _handleEngraving({ value }) {
        this.engravingInput.setState({display: value, required: value});

        if (value) {
            this._props.onChangeQuantity(1, true);
        } else {
            this._props.onChangeQuantity(1, false);
        }
    }

    _handleColorChange({ value }) {
        this.setState({
            product: {
                ...this._state.product,
                wireless_color: value,
            },
        }, false);
    }


    validateAllFields() {
        const errors = {};

        const wirelessColors = (this._state.attributes?.wireless_colors || []).map(x => x.value);
        const colorValue = this._state.product.wireless_color;

        if (!colorValue) {
            errors["headlight[wireless_color]"] = "무선 컬러는 필수 선택 항목입니다.";
        } else if (!wirelessColors.includes(colorValue)) {
            errors["headlight[wireless_color]"] = "선택한 무선 컬러가 유효하지 않습니다.";
        }

        this._state.errors = errors;

        this._renderErrors();
    }

    _renderErrors() {
        // 먼저 모든 에러 노드를 싹 지운다
        const allErrors = document.querySelectorAll(".no-form-error-msg");
        allErrors.forEach(($el) => $el.remove());

        for (const [name, msg] of Object.entries(this._state.errors)) {
            let el;

            if (name === "headlight[wireless_color]") {
                el = document.querySelector(
                    `[data-error-for="headlight[wireless_color]"]`
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
    }

    hasErrors() {
        return Object.keys(this._state.errors).length > 0;
    }
}
