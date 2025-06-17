import Component from "../../Modules/Core/Component";

export default class File extends Component {
    static UPDATE_INPUT_KEY = '_file_attachment_updates';
    static DELETE_INPUT_KEY = '_file_attachment_deleted';
 
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
            id: null,
            upload_path: null,
        };
    }

    _template() {
        const {
            fallback,
            file_key,
            path,
            name,
            original_name,
            label,
            id,
            upload_path,
            sort_order,
        } = this._props;

        const deleteCheckboxId = `delete_file_${id}`;
        const fileInputId = `file_input_${id ?? Math.random().toString(36).slice(2)}`;

        return `
            <div class="no-form-control no-form-file">
                <label for="${fileInputId}" class="no-form-control-inner no-form-file-inner">
                    <input 
                        type="file" 
                        name="${file_key}" 
                        id="${fileInputId}" 
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

                ${id && name && path ? `
                    <div class="no-form-file-preview">
                        <div class="no-form-file-preview__img">
                            <img src="${upload_path}" alt="${original_name ?? ''}" />
                        </div>
                        <div class="no-form-checkbox --sm">
                            <label for="${deleteCheckboxId}" class="no-form-checkbox-pointer">
                                <input 
                                    type="checkbox" 
                                    name="${File.DELETE_INPUT_KEY}[]" 
                                    id="${deleteCheckboxId}" 
                                    class="no-form-checkbox-input" 
                                    value="${id}"
                                    data-ref="deleteCheckbox"
                                />
                                <div class="no-form-checkbox-ripple">
                                    <span class="no-form-checkbox-box">
                                        <div class="no-form-checkbox-icon">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                    </span>
                                </div>
                                <span class="no-form-checkbox-text">파일 삭제</span>
                            </label>
                        </div>

                        <!-- 수정용 데이터 전달 -->
                        <input type="hidden" name="${File.UPDATE_INPUT_KEY}[${id}][file_key]" value="${file_key}" />
                        <input type="hidden" name="${File.UPDATE_INPUT_KEY}[${id}][sort_order]" value="${sort_order}" />
                    </div>
                ` : ``}

                <span class="no-form-control-space"></span>
            </div>
        `;
    }


    _bindEvents() {
        this.on(this.refs.button, "click", this._handleSelect.bind(this));
        this.on(this.refs.input, "change", this._handleChange.bind(this));
    }

    _handleSelect() {
        this.refs.input.click();
    }

    _handleChange(e) {
        const file = e.target.files[0];
        if (!file) return;

        this.refs.fallback.textContent = file.name;
    }
}
