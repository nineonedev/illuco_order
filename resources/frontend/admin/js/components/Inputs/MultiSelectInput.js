import View from "../../core/View";

export default class MultiSelectInput extends View {
    _defineProps() {
        return {
            name: "multiselect",
            label: "다중 선택",
            value: [], // ['kr', 'us']
            fallback: false,
            options: [], // [{ value: "kr", label: "대한민국" }, ...]
            disabled: false,
            readOnly: false,
            required: false,
            invalid: false,
            invalidMessage: "",
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
        const { label, name, options, fallback } = this._props;
        const nodeId = this._generateElementId();

        return `
            <div class="no-form-control --md">
                <label for="${nodeId}" class="no-form-control-inner">
                    <span class="no-form-label">${label}</span>
                    <select id="${nodeId}" name="${name}[]" data-ref="select" class="no-form-control-input" multiple>
                        ${
                            fallback
                                ? `<option value="">-- 선택 --</option>`
                                : ``
                        }
                        ${this._renderOptions(options)}
                    </select>
                </label>
                <span class="no-form-control-space"></span>
            </div>
        `;
    }

    _renderOptions(options) {
        const selectedValues = Array.isArray(this._state.value)
            ? this._state.value
            : [];

        return options
            .map((opt) => {
                const value = typeof opt === "string" ? opt : opt.value;
                const label = typeof opt === "string" ? opt : opt.label;
                const selected = selectedValues.includes(value)
                    ? "selected"
                    : "";

                return `<option value="${value}" ${selected}>${label}</option>`;
            })
            .join("");
    }

    _bindEvents() {
        const selectEl = this.refs.select;

        const instance = new Choices(selectEl, {
            removeItemButton: true,
            shouldSort: false,
        });

        if (typeof this._props.onChange === "function") {
            selectEl.addEventListener("change", (e) => {
                const selectedOptions = Array.from(
                    selectEl.selectedOptions
                ).map((opt) => opt.value);
                this._props.onChange({ value: selectedOptions, view: this }, e);
            });
        }

        instance.setChoiceByValue(this._state.value);
    }
}
