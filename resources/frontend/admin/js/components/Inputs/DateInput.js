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
