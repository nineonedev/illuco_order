import TextInput from "./TextInput";

export default class NumberInput extends TextInput {
    _defineProps(){
        return {
            ...super._defineProps(),
            type: 'number',
            min: null,
            max: null,
            step: null,
        }
    }

    _template() {
        const { 
            label,
            name,
            value,
            disabled,
            readOnly,
            required,
            helperText,
            invalid,
            invalidMessage,
            spacing,
            type,
            min,
            max,
            step,
        } = this._state;
        
        const nodeId = this._generateElementId();
        const hidden = type === 'hidden' ? `style="display: none;"` : '';

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
                        ${min ? `min="${min}"` : ''}
                        ${max ? `max="${max}"` : ''}
                        ${step ? `step="${step}"` : ''}
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
}
