import Component from "../../Modules/Core/Component";

export default class File extends Component {
    _boot() {
        this._type = "file";
        super._boot();
    }

    _defineProps() {
        return {
            label: "첨부파일",
            fallback: "선택된 파일 없음",
            original_name: null,
            mime_type: null,
            path: null,
            name: null,
            size: null,
            extension: null,
            file_key: `file_attachable[]`,
            sort_order: 0,
        };
    }

    _template() {
        const { fallback, name, file_key, path, original_name, label } =
            this._props;

        return `
            <div class="no-form-control no-form-file">
                <label for="${file_key}" class="no-form-control-inner no-form-file-inner">
                    <input 
                        type="file" 
                        name="${file_key}" 
                        id="${file_key}" 
                        class="no-form-control-input"
                        data-ref="input"
                    >
                    <fieldset class="no-form-control-label">
                        <legend class="no-form-control-text">${label}</legend>
                    </fieldset>
                    <button class="no-form-file-input" type="button" data-ref="button">
                        <div class="no-form-file-icon">
                            <i class="fa-light fa-paperclip-vertical"></i>
                        </div>
                        <span class="no-form-file-text" data-ref="fallback">
                            ${original_name ?? fallback}
                        </span>
                        <span class="no-form-file-button-text">파일 선택</span>
                    </button>
                </label>
                ${
                    path
                        ? `
                    <div>
                        <img 
                            src="${path}/${name}" 
                            alt="${original_name ?? ""}"/>
                    </div>
                `
                        : ``
                }
                
                <span class="no-form-control-space"></span>
            </div>
        `;
    }

    _bindEvents() {
        this.on(this.refs.button, "click", this._handleSelect.bind(this));
        this.on(this.refs.input, "change", this._handleChange.bind(this));
    }

    _handleSelect(e) {
        this._logger.info("hadleSelect");
        this.refs.input.click();
    }

    _handleChange(e) {
        const value = e.target.value;
        if (!value) return;

        this.refs.fallback.textContent = value;
    }
}
