import Component from "../../Modules/Core/Component";

export default class Text extends Component {
    _boot() {
        this._type = "text";
        super._boot();
    }

    _defineProps() {
        return {
            name: "text",
            label: "텍스트",
            value: '',
            required: false,
            helperText: ''
        };
    }

    _template() {
        const { label, name, value, required, helperText } = this._props;
        const isRequired = required  ? 'required' : '';

        const nodeId = this._generateNodeId();
        return `
            <div class="no-form-control --md">
                <label for="${nodeId}" class="no-form-control-inner">
                    <input 
                        type="text" 
                        name="${name}" 
                        id="${nodeId}" 
                        class="no-form-control-input" 
                        value="${value}" 
                        ${isRequired}
                        placeholder
                    />
                    <fieldset class="no-form-control-label">
                        <legend class="no-form-control-text">${label}</legend>
                    </fieldset>
                </label>
                ${helperText ? `<span class="no-form-control-helper-text">${helperText}</span>` : ''}
                <span class="no-form-control-space"></span>
            </div>
        `;
    }

    _bindEvents() {}
}
