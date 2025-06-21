import View from "../../core/View";

export default class CheckboxInput extends View {
    _defineProps() {
        return {
            name: "checkbox",
            label: "체크박스",
            value: false,
            disabled: false,
            helperText: "",
            spacing: true,
            checked: false,
            size: 'sm',
            onChange: () => {},
        };
    }

    _defineState() {
        return {
            ...this._props,
        };
    }

    _template() {
        const { name, label, value, disabled, helperText, spacing, checked, size } = this._state;
        const isChecked = checked ? "checked" : "";
        const disabledAttr = disabled ? "disabled" : "";

        return `
            <fieldset class="no-form-group">
                <div class="no-form-checkbox --${size}">
                    <label class="no-form-checkbox-pointer">
                        <input 
                            type="checkbox" 
                            name="${name}" 
                            value="${value}"
                            class="no-form-checkbox-input"
                            ${isChecked}
                            ${disabledAttr}
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
                    ${helperText ? `<p class="no-form-checkbox-helper-text">${helperText}</p>` : ""}
                </div>
                ${spacing ? `<span class="no-form-control-space"></span>` : ``}
            </fieldset>
        `;
    }

    _bindEvents() {
        const checkbox = this._el.querySelector("input[type=checkbox]");

        if (!checkbox) return;

        checkbox.addEventListener("change", (e) => {
            this._state.value = checkbox.checked;

            if (typeof this._props.onChange === "function") {
                this._props.onChange({
                    value: checkbox.checked,
                    view: this,
                    event: e,
                });
            }
        });
    }
}
