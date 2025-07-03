import CartController from "../../controllers/CartController";
import View from "../../core/View";
import CheckboxInput from "../Inputs/CheckboxInput";
import NumberInput from "../Inputs/NumberInput";
import RadioInput from "../Inputs/RadioInput";
import TextInput from "../Inputs/TextInput";

export default class HeadlightForm extends View {
    _boot() {
        this._attrHookId = this._generateHookId();
        this._optionHookId = this._generateHookId();
        super._boot();
    }

    _defineProps() {
        return {
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

        const { type, model } = this._state;

        RadioInput.make(this._attrHookId, {
            label: "형태",
            name: "loupe[type]",
            value: type,
            options: [
                { label: "Ready-made", value: "ready-made" },
                { label: "Custom-made", value: "custom-made" },
            ],
            onChange: this._handleTypeChange.bind(this),
        }).render();

        
        CheckboxInput.make(this._attrHookId, {
            label: "각인 사용",
            name: "loupe[use_engraving]",
            checked: false, 
            onChange: this._handleEngraving.bind(this),
        }).render();

        this.engravingInput =TextInput.make(this._attrHookId, {
            label: '각인 입력',
            name: 'loupe[engraving_text]',
            value: '',
            display: false, 
        }).render();

        const { loupe } = CartController.attributes;
        const specs = loupe[model];

        this._logger.success(model, specs);

        if (!specs) return;

        const { frame_types, working_distance } = specs;

        if (frame_types) {
            RadioInput.make(this._attrHookId, {
                label: "테정보",
                name: "loupe[frame_type]",
                value: this._state.loupe?.frame_type ?? "",
                options: specs.frame_types,
                onChange: this._handleFrameTypeChange.bind(this),
            }).render();
        }

        if (working_distance) {
            NumberInput.make(this._attrHookId, {
                label: "WD (단위:Cm)",
                name: "loupe[working_distance]",
                value: this._state.loupe?.working_distance ?? "",
                min: working_distance.min,
                max: working_distance.max,
                step: 0.1,
                onChange: this._handleWorkingDistanceChange.bind(this),
            }).render();
        }

    }

    // --------------------------
    // Individual Handlers
    // --------------------------
}
