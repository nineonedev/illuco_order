import TextInput from "./TextInput";

export default class DateInput extends TextInput {
    _defineProps() {
        return {
            ...super._defineProps(),
            name: "date",
            label: "날짜",
            enableTime: false,
            dateFormat: "Y-m-d",
            defaultDate: null,
            topLabel: false,
        };
    }

    _bindEvents() {
        const { enableTime, dateFormat, defaultDate } = this._state;

        flatpickr(this.qs("input"), {
            enableTime,
            dateFormat,
            defaultDate,
            time_24hr: true,
            locale: window.flatpickr.l10ns.ko,
        });
    }

    _template(){
        if (!this._state.topLabel) {
            return super._template(); 
        }

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
            maxlength,
            minlength,
        } = this._state;
        const nodeId = this._generateElementId();

        return `
            <div class="no-form-control --md">   
                <label for="${nodeId}" class="no-form-control-inner">
                    <span class="no-form-label">${label}</span>
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
                        ${maxlength ? `maxlength=${maxlength}` : ``}
                        ${minlength ? `minlength=${minlength}` : ``}
                    >
                </label>
                ${spacing ? `<span class="no-form-control-space"></span>` : ``}
            </div>
        `;
    }
}
