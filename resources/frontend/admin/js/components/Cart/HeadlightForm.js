import View from "../../core/View";
import CheckboxInput from "../Inputs/CheckboxInput";
import TextInput from "../Inputs/TextInput";
import RadioInput from "../Inputs/RadioInput";

export default class HeadlightForm extends View {
    _boot() {
        this._attrHookId = this._generateHookId();
        super._boot();
    }

    _defineProps() {
        return {
            template: {},
            product: {
                engraving_text: "",
                wireless_color: "",
            },
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

        RadioInput.make(this._attrHookId, {
            label: "무선 컬러 선택",
            name: "headlight[wireless_color]",
            value: wireless_color,
            options: [
                { value: "black", label: "블랙" },
                { value: "silver", label: "실버" },
                { value: "blue", label: "블루" },
            ],
            onChange: this._handleColorChange.bind(this),
        }).render();
    }

    _handleEngraving({ value }) {
        this.engravingInput.setState({
            display: value,
        });

        if (!value) {
            this.engravingInput.setState({
                value: "",
            });
        }
    }

    _handleColorChange({ value }) {
        this.setState({
            product: {
                ...this._state.product,
                wireless_color: value,
            },
        });
    }

    validateAllFields() {
        // 벨리데이션 필요 없음
    }

    hasErrors() {
        return false;
    }
}
