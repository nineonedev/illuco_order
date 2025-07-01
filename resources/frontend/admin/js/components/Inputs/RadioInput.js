import View from "../../core/View";

export default class RadioInput extends View {
    _defineProps() {
        return {
            name: "radio-group",
            label: "라벨",
            value: "",
            options: [], // [{ value: "M", label: "Frame 1" }, ...]
            spacing: true,
            required: false,
            disabled: false,
            onChange: () => {},
        };
    }

    _defineState() {
        return {
            ...this._props,
        };
    }

    _template() {
        const { label, options, name, spacing } = this._state;

        return `
            <fieldset class="no-form-group">
                <legend class="no-form-base-label">${label}</legend>
                <div>
                    <div class="no-form-listing" data-ref="container" data-error-for="${name}">
                        ${this._renderOptions(options, name)}
                    </div>
                </div>
                ${spacing ? `<span class="no-form-control-space"></span>` : ``}
            </fieldset>
        `;
    }

    _renderOptions(options, name) {
        const { value: selectedValue } = this._state;

        return options
            .map((opt, index) => {
                const optionValue = typeof opt === "string" ? opt : opt.value;
                const optionLabel = typeof opt === "string" ? opt : opt.label;
                const id = `radio_${name}_${index}`;
                const checked = selectedValue === optionValue ? "checked" : "";

                return `
                    <div class="no-form-radio --sm">
                        <label class="no-form-radio-pointer" for="${id}">
                            <input 
                                class="no-form-radio-input" 
                                type="radio" 
                                name="${name}" 
                                id="${id}" 
                                value="${optionValue}" 
                                ${checked}
                                data-ref="input"
                            >
                            <div class="no-form-radio-ripple">
                                <div class="no-form-radio-box">
                                    <span class="no-form-radio-icon"></span>
                                </div>
                            </div>
                            <span class="no-form-radio-text">${optionLabel}</span>
                        </label>
                    </div>
                `;
            })
            .join("");
    }

    _bindEvents() {
        const container = this.refs.container;
        const inputs = container.querySelectorAll("input[type=radio]");

        inputs.forEach((input) => {
            input.addEventListener("change", (e) => {
                const value = e.target.value; 
                this._state.value = value;
                
                if (typeof this._props.onChange === "function") {
                    this._props.onChange({ value: value, view: this }, e);
                }
            });
        });
    }
}
