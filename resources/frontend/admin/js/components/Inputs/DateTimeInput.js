import TextInput from "./TextInput";

export default class DateTimeInput extends TextInput {
    _defineProps() {
        return {
            ...super._defineProps(),
            name: "datetime",
            label: "날짜/시간",
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            defaultDate: null,
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
}
