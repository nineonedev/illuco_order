import Component from "../../Modules/Core/Component";
import Choices from "choices.js";
import "choices.js/public/assets/styles/choices.min.css";

export default class CountrySelect extends Component {
    _boot() {
        this._type = "country-select";
        super._boot();
    }

    _defineProps() {
        return {
            name: "country",
            label: "국가 선택",
            value: "KR",
            options: [
                { value: "US", label: "United States" },
                { value: "KR", label: "South Korea" },
                { value: "JP", label: "Japan" },
                { value: "CN", label: "China" },
                { value: "FR", label: "France" },
                { value: "DE", label: "Germany" },
                { value: "ES", label: "Spain" },
                { value: "RU", label: "Russia" },
                { value: "IN", label: "India" },
                { value: "GB", label: "United Kingdom" },
                { value: "IT", label: "Italy" },
                { value: "BR", label: "Brazil" },
                { value: "CA", label: "Canada" },
                { value: "AU", label: "Australia" },
                { value: "MX", label: "Mexico" },
                { value: "NL", label: "Netherlands" },
                { value: "TR", label: "Turkey" },
                { value: "ID", label: "Indonesia" },
                { value: "SA", label: "Saudi Arabia" },
                { value: "AR", label: "Argentina" },
                { value: "TH", label: "Thailand" },
                { value: "VN", label: "Vietnam" },
                { value: "PH", label: "Philippines" },
                { value: "PL", label: "Poland" },
                { value: "SE", label: "Sweden" },
                { value: "CH", label: "Switzerland" },
                { value: "BE", label: "Belgium" },
                { value: "NO", label: "Norway" },
                { value: "FI", label: "Finland" },
                { value: "DK", label: "Denmark" },
                { value: "UA", label: "Ukraine" },
                { value: "ZA", label: "South Africa" },
                { value: "EG", label: "Egypt" },
                { value: "NG", label: "Nigeria" },
                { value: "KE", label: "Kenya" },
                { value: "NZ", label: "New Zealand" },
                { value: "MY", label: "Malaysia" },
                { value: "SG", label: "Singapore" },
                { value: "IL", label: "Israel" },
                { value: "IR", label: "Iran" },
                { value: "GR", label: "Greece" },
                { value: "PT", label: "Portugal" },
                { value: "AT", label: "Austria" },
                { value: "CZ", label: "Czech Republic" },
                { value: "HU", label: "Hungary" },
                { value: "RO", label: "Romania" },
                { value: "BG", label: "Bulgaria" },
                { value: "SK", label: "Slovakia" },
                { value: "HR", label: "Croatia" },
                { value: "SI", label: "Slovenia" },
                { value: "RS", label: "Serbia" },
            ],
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
        const selectedValue = this._props.value;
        return options
            .map((opt) => {
                const value = typeof opt === "string" ? opt : opt.value;
                const label = typeof opt === "string" ? opt : opt.label;
                const selected = selectedValue === value ? "selected" : "";

                return `<option value="${value}" ${selected}>${label}</option>`;
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
