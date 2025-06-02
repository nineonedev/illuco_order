import $ from "jquery";

window.$ = $;
window.jQuery = $;

import "../scss/index.scss";

import Product from "./Product";

class App {
    async init() {
        await import("bootstrap");
        await import("summernote/dist/summernote-bs5");
        await import("summernote/dist/lang/summernote-ko-KR");

        const product = new Product("asddas", 232);
        product.showInfo();

        document.addEventListener("DOMContentLoaded", this.run);
    }

    run() {
        $("[data-text-editor]").summernote({
            height: 300,
            placeholder: "내용을 입력하세요",
            lang: "ko-KR",
            toolbar: [
                ["style", ["style"]],
                [
                    "font",
                    ["bold", "italic", "underline", "strikethrough", "clear"],
                ],
                ["fontname", ["fontname"]],
                ["fontsize", ["fontsize"]],
                ["color", ["color"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["table", ["table"]],
                ["insert", ["link", "picture", "video"]],
                ["view", ["fullscreen", "codeview", "help"]],
            ],
        });
    }
}

const app = new App();
app.init();
