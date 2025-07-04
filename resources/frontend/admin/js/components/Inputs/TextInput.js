import View from "../../core/View";

export default class TextInput extends View {
    _defineProps() {
        return {
            type: 'text',
            name: "text-field",
            label: "라벨",
            value: '',
            disabled: false,
            readOnly: false,
            required: false,
            invalid: false,
            invalidMessage: '',
            helperText: '',
            spacing: true,
            required: false,
            display: true,
            onChange: (e) => {},
        };
    }

    _defineState(){
        return {
            ...this._props
        }
    }

    _template() {
        const { label, name, value, disabled, readOnly, required, helperText, invalid, invalidMessage, spacing, type } = this._state;
        const nodeId = this._generateElementId();
        const hidden = (!this._state.display || type === 'hidden') 
            ? `style="display: none;"` 
            : '';

        return `
            <div class="no-form-control" ${hidden}>
                <label for="${nodeId}" class="no-form-control-inner">
                    <input 
                        data-label="${label}"
                        data-ref="input"
                        type="${type}" 
                        name="${name}" 
                        id="${nodeId}" 
                        value="${value}" 
                        class="no-form-control-input ${invalid ? `--invalid` : ``}" 
                        placeholder=""
                        ${disabled ? 'disabled' : ''}
                        ${readOnly ? 'readOnly' : ''}
                        ${required ? 'required' : ''}
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
        this.on(this.refs.input, 'input', (view, e) => {
            const value = e.target.value; 
            this.setState({value: value}, false);
            this._props.onChange({value: value, view: this}, e);
        });
    }
}
