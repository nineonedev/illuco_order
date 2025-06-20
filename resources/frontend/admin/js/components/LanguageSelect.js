import Component from "../core/Component";
import Choices from "choices.js";
import "choices.js/public/assets/styles/choices.min.css";

export default class LanguageSelect extends Component {
    _boot() {
        this._type = "language-select";
        super._boot();
    }

    _defineProps() {
        return {
            name: "language",
            label: "언어 선택",
            value: "ko",
            options: [
                // ISO 639-1
                { value: "en", label: "English" },
                { value: "ko", label: "Korean" },
                { value: "ja", label: "Japanese" },
                { value: "zh", label: "Chinese" },
                { value: "fr", label: "French" },
                { value: "de", label: "German" },
                { value: "es", label: "Spanish" },
                { value: "ru", label: "Russian" },
                { value: "ar", label: "Arabic" },
                { value: "hi", label: "Hindi" },
                { value: "af", label: "Afrikaans" },
                { value: "am", label: "Amharic" },
                { value: "ar", label: "Arabic" },
                { value: "az", label: "Azerbaijani" },
                { value: "be", label: "Belarusian" },
                { value: "bg", label: "Bulgarian" },
                { value: "bn", label: "Bengali" },
                { value: "ca", label: "Catalan" },
                { value: "cs", label: "Czech" },
                { value: "da", label: "Danish" },
                { value: "de", label: "German" },
                { value: "el", label: "Greek" },
                { value: "en", label: "English" },
                { value: "es", label: "Spanish" },
                { value: "et", label: "Estonian" },
                { value: "fa", label: "Persian" },
                { value: "fi", label: "Finnish" },
                { value: "fr", label: "French" },
                { value: "gu", label: "Gujarati" },
                { value: "he", label: "Hebrew" },
                { value: "hi", label: "Hindi" },
                { value: "hr", label: "Croatian" },
                { value: "hu", label: "Hungarian" },
                { value: "hy", label: "Armenian" },
                { value: "id", label: "Indonesian" },
                { value: "is", label: "Icelandic" },
                { value: "it", label: "Italian" },
                { value: "ja", label: "Japanese" },
                { value: "jv", label: "Javanese" },
                { value: "ka", label: "Georgian" },
                { value: "kk", label: "Kazakh" },
                { value: "km", label: "Khmer" },
                { value: "kn", label: "Kannada" },
                { value: "ko", label: "Korean" },
                { value: "lo", label: "Lao" },
                { value: "lt", label: "Lithuanian" },
                { value: "lv", label: "Latvian" },
                { value: "ml", label: "Malayalam" },
                { value: "mn", label: "Mongolian" },
                { value: "mr", label: "Marathi" },
                { value: "ms", label: "Malay" },
                { value: "my", label: "Burmese" },
                { value: "ne", label: "Nepali" },
                { value: "nl", label: "Dutch" },
                { value: "no", label: "Norwegian" },
                { value: "pa", label: "Punjabi" },
                { value: "pl", label: "Polish" },
                { value: "pt", label: "Portuguese" },
                { value: "ro", label: "Romanian" },
                { value: "ru", label: "Russian" },
                { value: "si", label: "Sinhala" },
                { value: "sk", label: "Slovak" },
                { value: "sl", label: "Slovenian" },
                { value: "sr", label: "Serbian" },
                { value: "sv", label: "Swedish" },
                { value: "sw", label: "Swahili" },
                { value: "ta", label: "Tamil" },
                { value: "te", label: "Telugu" },
                { value: "th", label: "Thai" },
                { value: "tl", label: "Tagalog" },
                { value: "tr", label: "Turkish" },
                { value: "uk", label: "Ukrainian" },
                { value: "ur", label: "Urdu" },
                { value: "uz", label: "Uzbek" },
                { value: "vi", label: "Vietnamese" },
                { value: "zh", label: "Chinese" },
                { value: "zu", label: "Zulu" },
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
