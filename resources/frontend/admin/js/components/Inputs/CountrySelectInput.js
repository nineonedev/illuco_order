import SelectInput from "./SelectInput";

export default class CountrySelect extends SelectInput {
    _defineProps() {
        return {
            ...super._defineProps(),
            name: "country",
            label: "국가 선택",
            value: "KR",
            fallback: true, // "-- 선택 --" 표시 여부
            onChange: () => {},
        };
    }

    _defineState() {
        return {
            ...super._defineState(),
            options: this._getCountryOptions()
        };
    }

    _getCountryOptions() {
        return [
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
        ];
    }
}
