import Component from "../../Modules/Core/Component";

export default class Checkbox extends Component {
    _boot() {
        this._type = "checkbox";
        super._boot();
    }

    _defineProps() {
        return {
            name: "checkbox",
            label: "체크박스",
            required: false,
            value: '',
            helperText: '',
            spacing: false
        };
    }

    _template() {
        const { label, name, value, required, helperText, spacing } = this._props;
        const nodeId = this._generateNodeId();
        const isRequired = required ? 'required' : '';
        const isChecked = value ? 'checked' : '';

        return `
            <div class="no-form-checkbox --sm">
                <label for="${nodeId}" class="no-form-checkbox-pointer">
                    <input 
                        data-ref="input"
                        type="checkbox" 
                        name="${name}" 
                        id="${nodeId}" 
                        class="no-form-checkbox-input" 
                        value="1" 
                        ${isChecked} 
                        ${isRequired}
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
                    ${helperText ? `<p class="no-form-radio-helper-text">${helperText}</p>` : ''}
                    ${spacing ? `<span class="no-form-control-space"></span>` : ''}
            </div>
        `;
    }


    _bindEvents() {
        
    }
}
