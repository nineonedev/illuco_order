import TextInput from "./TextInput";

export default class LongTextInput extends TextInput {

    _defineProps(){
        return {
            ...super._defineProps(),
            rows: 6,
        };
    }
    
    _template() {
        const { 
            label, 
            name, 
            value, 
            disabled, 
            readOnly, 
            helperText, 
            invalid, 
            spacing, 
            invalidMessage,
            rows,
        } = this._state;
        const nodeId = this._generateElementId();
        
        return `
            <div class="no-form-control --textarea">
                <label for="${nodeId}" class="no-form-control-inner">
                    <textarea  
                        data-ref="input"
                        name="${name}" 
                        id="${nodeId}" 
                        class="no-form-control-input" 
                        placeholder="" 
                        rows="${rows}"
                        ${disabled ? 'disabled' : ''}
                        ${readOnly ? 'readOnly' : ''}
                        >${value}</textarea>
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
