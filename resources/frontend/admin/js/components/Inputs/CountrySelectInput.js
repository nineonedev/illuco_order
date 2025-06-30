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
            { value: "US", label: "미국" },
            { value: "KR", label: "대한민국" },
            { value: "JP", label: "일본" },
            { value: "CN", label: "중국" },
            { value: "FR", label: "프랑스" },
            { value: "DE", label: "독일" },
            { value: "ES", label: "스페인" },
            { value: "RU", label: "러시아" },
            { value: "IN", label: "인도" },
            { value: "GB", label: "영국" },
            { value: "IT", label: "이탈리아" },
            { value: "BR", label: "브라질" },
            { value: "CA", label: "캐나다" },
            { value: "AU", label: "호주" },
            { value: "MX", label: "멕시코" },
            { value: "NL", label: "네덜란드" },
            { value: "TR", label: "터키" },
            { value: "ID", label: "인도네시아" },
            { value: "SA", label: "사우디아라비아" },
            { value: "AR", label: "아르헨티나" },
            { value: "TH", label: "태국" },
            { value: "VN", label: "베트남" },
            { value: "PH", label: "필리핀" },
            { value: "PL", label: "폴란드" },
            { value: "SE", label: "스웨덴" },
            { value: "CH", label: "스위스" },
            { value: "BE", label: "벨기에" },
            { value: "NO", label: "노르웨이" },
            { value: "FI", label: "핀란드" },
            { value: "DK", label: "덴마크" },
            { value: "UA", label: "우크라이나" },
            { value: "ZA", label: "남아프리카공화국" },
            { value: "EG", label: "이집트" },
            { value: "NG", label: "나이지리아" },
            { value: "KE", label: "케냐" },
            { value: "NZ", label: "뉴질랜드" },
            { value: "MY", label: "말레이시아" },
            { value: "SG", label: "싱가포르" },
            { value: "IL", label: "이스라엘" },
            { value: "IR", label: "이란" },
            { value: "GR", label: "그리스" },
            { value: "PT", label: "포르투갈" },
            { value: "AT", label: "오스트리아" },
            { value: "CZ", label: "체코" },
            { value: "HU", label: "헝가리" },
            { value: "RO", label: "루마니아" },
            { value: "BG", label: "불가리아" },
            { value: "SK", label: "슬로바키아" },
            { value: "HR", label: "크로아티아" },
            { value: "SI", label: "슬로베니아" },
            { value: "RS", label: "세르비아" },
            { value: "TW", label: "대만" },
            { value: "AE", label: "아랍에미리트" },
            { value: "PK", label: "파키스탄" },
            { value: "MA", label: "모로코" },
            { value: "HK", label: "홍콩" },
            { value: "IQ", label: "이라크" },
            { value: "AM", label: "아르메니아" },
        ];
    }
}
