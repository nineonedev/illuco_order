import View from "../../core/View";

export default class CounterInput extends View {
    _defineProps() {
        return {
            name: "counter",
            value: 1,
            min: 1,
            disabled: false,
            readOnly: false,
            onChange: () => {},
        };
    }

    _defineState() {
        return {
            ...this._props
        };
    }

    _template() {
        const { name, value, min, disabled, readOnly } = this._state;
        const inputId = this._generateElementId();

        return `
            <div class="no-cart-item-present__counter">
                <button type="button" class="--decrease" data-ref="decrease" ${disabled ? 'disabled' : ''}>
                    <i class="fa-regular fa-minus"></i>
                </button>
                <div class="--input">
                    <input 
                        type="number" 
                        id="${inputId}"
                        name="${name}" 
                        value="${value}" 
                        min="${min}" 
                        ${disabled ? "disabled" : ""} 
                        ${readOnly ? "readonly" : ""}
                        data-ref="input"
                    />
                </div>
                <button type="button" class="--increase" data-ref="increase" ${disabled ? 'disabled' : ''}>
                    <i class="fa-regular fa-plus"></i>
                </button>
            </div>
        `;
    }

    _bindEvents() {
        this.on(this.refs.decrease, 'click', this._decrease.bind(this));
        this.on(this.refs.increase, 'click', this._increase.bind(this));
        this.on(this.refs.input, 'change', this._manualChange.bind(this));
    }

    _decrease() {
        const current = parseInt(this.refs.input.value, 10) || 0;
        const min = this._state.min || 1;
        const newVal = Math.max(current - 1, min);
        this._updateValue(newVal);
    }

    _increase() {
        const current = parseInt(this.refs.input.value, 10) || 0;
        const newVal = current + 1;
        this._updateValue(newVal);
    }

    _manualChange(view, e) {
        let val = parseInt(e.target.value, 10);
        if (isNaN(val)) val = this._state.min || 1;
        if (val < this._state.min) val = this._state.min;
        this._updateValue(val);
    }

    _updateValue(val) {
        this._state.value = val;
        this.refs.input.value = val;
        
        if (typeof this._props.onChange === "function") {
            this._props.onChange({ value: val, view: this });
        }
    }
}
