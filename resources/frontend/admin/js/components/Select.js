import Component from "../core/Component";
import Choices from "choices.js";
import "choices.js/public/assets/styles/choices.min.css";

export default class Select extends Component {
    _type = "select";

    _defineProps() {
        return {
            name: "select",
            label: "선택",
            value: "",
            fallback: false,
            options: [], // [{ value: "kr", label: "대한민국" }, ...]
            onChange: () => {}
        };
    }

    _defineState(){
        return {
            ...this._props
        }
    }

    _template() {
        const { label, name, options, fallback} = this._props;
        const nodeId = this._generateNodeId();
        return `
            <div class="no-form-control --md">   
                <label for="${nodeId}" class="no-form-control-inner">
                    <span class="no-form-label">${label}</span>
                    <select id="${nodeId}" name="${name}" data-ref="select" class="no-form-control-input">
                        ${fallback ? `<option value="">-- 선택 --</option>` : `` }
                        ${this._renderOptions(options)}
                    </select>
                </label>
                <span class="no-form-control-space"></span>
            </div>
        `;
    }

    _renderOptions(options) {
        const {value: selectedValue} = this._state; 

        return options
            .map((opt) => {
                const value = typeof opt === "string" ? opt : opt.value;
                const label = typeof opt === "string" ? opt : opt.label;
                const selected = selectedValue === value ? 'selected' : '';

                return `<option value="${value}" ${selected}>${label}</option>`;
            })
            .join("");
    }

    _bindEvents() {
        const selectEl = this.refs.select;

        new Choices(selectEl, {
            searchEnabled: true,
            itemSelectText: "",
            shouldSort: false,
        });


        if (typeof this._props.onChange === "function") {
            selectEl.addEventListener("change", (e) => {
                const value = e.target.value;
                this._props.onChange({value: value, component: this}, e);
            });
        }
    }
}
