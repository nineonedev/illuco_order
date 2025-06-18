import Component from "../../Modules/Core/Component";
import flatpickr from "flatpickr";
import { Korean } from "flatpickr/dist/l10n/ko.js"; // 한글 설정
import "flatpickr/dist/flatpickr.min.css";

export default class DateTime extends Component {
    _boot() {
        this._type = "datetime";
        super._boot();
    }

    _defineProps() {
        return {
            name: "datetime",
            label: "날짜/시간",
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            defaultDate: null,
            value: '',
        };
    }

    _template() {
        const { label, name, value } = this._props;
        const nodeId = this._generateNodeId();
        return `
            <div class="no-form-control --md">
                <label for="${nodeId}" class="no-form-control-inner">
                    <input type="text" name="${name}" id="${nodeId}" data-ref="input" class="no-form-control-input" placeholder="" value="${value}">
                    <fieldset class="no-form-control-label">
                        <legend class="no-form-control-text">${label}</legend>
                    </fieldset>
                </label>
                <span class="no-form-control-space"></span>
            </div>
        `;
    }

    _bindEvents() {
        const { enableTime, dateFormat, defaultDate } = this._props;

        flatpickr(this.refs.input, {
            enableTime,
            dateFormat,
            defaultDate,
            time_24hr: true,
            locale: Korean,
        });
    }
}
