import View from "../../core/View";

export default class TextInput extends View {
    _inputType = 'text';

    _defineProps() {
        return {
            name: "text-field",
            label: "라벨",
            value: '',
            disabled: false,
            readOnly: false,
            required: false,
            invalid: false,
            invalidMessage: '',
            spacing: true,
            onChange: (e) => {},
        };
    }

    _defineState(){
        return {
            ...this._props
        }
    }

    _template() {
        const { label, name, value, disabled, readOnly, helperText, invalid, invalidMessage, spacing } = this._state;
        const nodeId = this._generateElementId();
        
        return `
            <div class="no-form-control">
                <label for="${nodeId}" class="no-form-control-inner">
                    <input 
                        data-ref="input"
                        type="${this._inputType}" 
                        name="${name}" 
                        id="${nodeId}" 
                        value="${value}" 
                        class="no-form-control-input ${invalid ? `--invalid` : ``}" 
                        placeholder=""
                        ${disabled ? 'disabled' : ''}
                        ${readOnly ? 'readOnly' : ''}
                    >
                    <fieldset class="no-form-control-label">
                        <legend class="no-form-control-text">${label}</legend>
                    </fieldset>
                </label>
                ${helperText ? `<span class="no-form-control-helper-text">${helperText}</span>` : ``}
                ${invalid ? `<span class="no-form-control-feedback">${invalidMessage}</span>` : ``}
                ${spacing ? `<span class="no-form-control-space"></span>` : ``}
            </div>
        `;
    }

    _bindEvents(){
        this.on(this.refs.input, 'change', (view, e) => {
            const value = e.target.value; 
            this._state.value = value;

            this._props.onChange({value: value, view: this}, e);
        });
    }
}
