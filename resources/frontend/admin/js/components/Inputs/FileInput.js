import View from "../../core/View";

export default class FileInput extends View {
    static UPDATE_INPUT_KEY = "_file_attachment_updates";
    static DELETE_INPUT_KEY = "_file_attachment_deleted";

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
            file_key: "file_attachable[]",
            sort_order: 0,
            id: null,
            upload_path: null,
        };
    }

    _defineState() {
        return {
            ...this._props
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
            extension,
        } = this._state;

        const deleteCheckboxId = `delete_file_${id}`;
        const fileInputId = `file_input_${id ?? this._generateElementId()}`;

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

                ${
                    id && name && path
                        ? `
                    <div class="no-form-file-preview">
                        <div class="no-form-file-preview__viewer">
                            ${this._renderPreview(path, original_name, extension, upload_path)}
                        </div>
                        <div class="no-form-checkbox --sm">
                            <label for="${deleteCheckboxId}" class="no-form-checkbox-pointer">
                                <input 
                                    type="checkbox" 
                                    name="${FileInput.DELETE_INPUT_KEY}[]" 
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

                        <input type="hidden" name="${FileInput.UPDATE_INPUT_KEY}[${id}][file_key]" value="${file_key}" />
                        <input type="hidden" name="${FileInput.UPDATE_INPUT_KEY}[${id}][sort_order]" value="${sort_order}" />
                    </div>
                    `
                        : ``
                }

                <span class="no-form-control-space"></span>
            </div>
        `;
    }

    _renderPreview(fileUrl, fileName, extension, resolvedUrl) {
        const ext = (extension || "").toLowerCase();

        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            return `<div class="no-form-file-preview__image"><img src="${resolvedUrl}" alt="${fileName}"></div>`;
        }

        if (['mp4', 'webm', 'ogg'].includes(ext)) {
            return `<video controls class="no-form-file-preview__video"><source src="${resolvedUrl}" type="video/${ext}"></video>`;
        }

        if (ext === 'pdf') {
            return `<iframe src="${resolvedUrl}" class="no-form-file-preview__doc" frameborder="0"></iframe>`;
        }

        return `<a href="${resolvedUrl}" target="_blank" class="no-form-file-preview__download">📎 ${fileName ?? '파일 다운로드'}</a>`;
    }

    _bindEvents() {
        this.on(this.refs.button, "click", () => this.refs.input.click());

        this.on(this.refs.input, "change", (_, e) => {
            const input = e.target;
            if (!input?.files?.length) return;

            const file = input.files[0];
            this.refs.fallback.textContent = file.name;

            this.setState({
                original_name: file.name,
                name: file.name,
                size: file.size,
                mime_type: file.type,
                extension: file.name.split('.').pop()
            }, false);
        });
    }
}
