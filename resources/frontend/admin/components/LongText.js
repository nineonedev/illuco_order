import $ from "jquery";
import "bootstrap/dist/js/bootstrap.min.js";
import "summernote/dist/summernote.min.js";
import Component from "../../Modules/Core/Component";
import Ajax from "../../Modules/Core/Ajax";

export default class LongText extends Component {
    _boot() {
        this._type = "longText";
        super._boot();
    }

    _defineProps() {
        return {
            uploadUrl: "/admin/file-attachments",
            label: "내용",
            name: "content",
            value: "",
        };
    }

    _template() {
        const { label, value, name } = this._props;
        const nodeId = this._generateNodeId();

        return `<div class="no-form-base --md">
                    <label for="${nodeId}" class="no-form-base-label">
                        <span>${label}</span>
                    </label>
                    <textarea name="${name}" id="${nodeId}" data-ref="editor" class="no-form-base-input">${value}</textarea>
                    <span class="no-form-control-space"></span>
                </div>`;
    }

    _bindEvents() {
        const target = $(this.refs.editor);
        const uploadUrl = this._props.uploadUrl;
        const ajax = new Ajax();

        target.summernote({
            height: 300,
            lang: "ko-KR",
            toolbar: [
                ["style", ["style"]],
                ["font", ["bold", "italic", "underline", "clear"]],
                ["fontname", ["fontname"]],
                ["fontsize", ["fontsize"]],
                ["color", ["color"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["table", ["table"]],
                ["insert", ["link", "picture"]],
                ["view", ["codeview"]],
            ],
            callbacks: {
                onImageUpload: function (files) {
                    const fd = new FormData();

                    [...files].forEach((file) => {
                        fd.append("file_attachable[]", file);
                    });

                    console.log(Object.fromEntries(files));
                    return;

                    ajax.post(uploadUrl, formData)
                        .then((res) => {
                            target.summernote("insertImage", data.location);
                        })
                        .catch((err) => {
                            console.error("Upload failed:", err);
                        });
                },
            },
        });
    }
}
