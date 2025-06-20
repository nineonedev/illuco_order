import View from "../../core/View";

export default class CheckboxGroupInput extends View {
    _defineProps() {
        return {
            name: "checkbox-group",
            label: "라벨",
            value: [], // ["game_1", "game_3"]
            options: [], // [{ value: "game_1", label: "LOL", helper: "", disabled: false }, ...]
            spacing: true,
            onChange: () => {},
        };
    }

    _defineState() {
        return {
            ...this._props,
        };
    }

    _template() {
        const { label, spacing } = this._state;
        return `
            <fieldset class="no-form-group">
                <legend class="no-form-base-label">${label}</legend>
                <div class="no-form-listing" data-ref="container">
                    ${this._renderOptions()}
                </div>
                ${spacing ? `<span class="no-form-control-space"></span>` : ``}
            </fieldset>
        `;
    }

    _renderOptions() {
        const { options, name, value: selectedValues } = this._state;

        return options
            .map((opt, i) => {
                const val = opt.value;
                const label = opt.label ?? val;
                const helper = opt.helper ?? "";
                const disabled = opt.disabled ? "disabled" : "";
                const id = `${name}_${i}`;
                const checked = selectedValues.includes(val) ? "checked" : "";

                return `
                    <div class="no-form-block">
                        <div class="no-form-checkbox --sm">
                            <label for="${id}" class="no-form-checkbox-pointer">
                                <input 
                                    type="checkbox" 
                                    name="${name}[]" 
                                    id="${id}" 
                                    class="no-form-checkbox-input"
                                    value="${val}"
                                    ${checked}
                                    ${disabled}
                                >
                                <div class="no-form-checkbox-ripple">
                                    <span class="no-form-checkbox-box">
                                        <div class="no-form-checkbox-icon">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                    </span>
                                </div>
                                <span class="no-form-checkbox-text">${label}</span>
                            </label>
                            ${helper ? `<p class="no-form-checkbox-helper-text">${helper}</p>` : ""}
                        </div>
                    </div>
                `;
            })
            .join("");
    }

    _bindEvents() {
        const container = this.refs.container;
        const checkboxes = container.querySelectorAll("input[type=checkbox]");


        checkboxes.forEach((box) => {
            box.addEventListener("change", () => {
                
                const checkedValues = [...checkboxes]
                    .filter((cb) => cb.checked)
                    .map((cb) => cb.value);
                    
                this._state.value = checkedValues;
                

                if (typeof this._props.onChange === "function") {
                    this._props.onChange({ value: checkedValues, view: this });
                }
            });
        });
    }
}
