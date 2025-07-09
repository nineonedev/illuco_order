import Choices from "choices.js";
import "choices.js/public/assets/styles/choices.min.css";
import View from "../../core/View";

export default class SelectInput extends View {

    _defineProps() {
        return {
            name: "select",
            label: "선택",
            value: "",
            fallback: false,
            options: [], // [{ value: "kr", label: "대한민국" }, ...]
            disabled: false,
            readOnly: false,
            required: false,
            invalid: false,
            invalidMessage: '',
            spacing: true,
            onChange: () => {}
        };
    }

    _defineState(){
        return {
            ...this._props
        }
    }

    _template() {
        const { label, name, options, fallback} = this._state;
        const nodeId = this._generateElementId();
        return `
            <div class="no-form-control --md">   
                <label for="${nodeId}" class="no-form-control-inner">
                    <span class="no-form-label">${label}</span>
                    <select id="${nodeId}" name="${name}" data-ref="select" class="no-form-control-input">
                        ${fallback ? `<option value="">-- 선택 --</option>` : `` }
                        ${this._renderOptions(options)}
                    </select>
                </label>
                <span class="no-form-control-space"></span>
            </div>
        `;
    }

    _renderOptions(options) {
        const {value: selectedValue} = this._state; 

        return options
            .map((opt) => {
                const value = typeof opt === "string" ? opt : opt.value;
                const label = typeof opt === "string" ? opt : opt.label;
                const disabled = opt.disabled ?? false; 
                const selected = selectedValue == value ? 'selected' : '';

                return `<option value="${value}" ${selected} ${disabled ? 'disabled' : ''}>${label}</option>`;
            })
            .join("");
    }

    _bindEvents() {
        const selectEl = this.refs.select;

        new Choices(selectEl, {
            searchEnabled: true,
            itemSelectText: "",
            shouldSort: false,
        });


        if (typeof this._props.onChange === "function") {
            selectEl.addEventListener("change", (e) => {
                const value = e.target.value;
                this._props.onChange({value: value, view: this}, e);
            });
        }
    }
}
