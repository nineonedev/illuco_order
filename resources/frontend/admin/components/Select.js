import Component from "../../Modules/Core/Component";
import Choices from "choices.js";
import "choices.js/public/assets/styles/choices.min.css";

export default class Select extends Component {
    _boot() {
        this._type = "select";
        super._boot();
    }

    _defineProps() {
        return {
            name: "select",
            label: "선택",
            options: [], // [{ value: "kr", label: "대한민국" }, ...]
        };
    }

    _template() {
        const { label, name, options } = this._props;
        const nodeId = this._generateNodeId();
        return `
            <div class="no-form-control --md">   
                <label for="${nodeId}" class="no-form-control-inner">
                    <span class="no-form-label">${label}</span>
                    <select id="${nodeId}" name="${name}" data-ref="select" class="no-form-control-input">
                        ${this._renderOptions(options)}
                    </select>
                </label>
                <span class="no-form-control-space"></span>
            </div>
        `;
    }

    _renderOptions(options) {
        return options
            .map((opt) => {
                const value = typeof opt === "string" ? opt : opt.value;
                const label = typeof opt === "string" ? opt : opt.label;
                return `<option value="${value}">${label}</option>`;
            })
            .join("");
    }

    _bindEvents() {
        new Choices(this.refs.select, {
            searchEnabled: true,
            itemSelectText: "",
            shouldSort: false,
        });
    }
}
