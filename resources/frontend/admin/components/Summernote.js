import $ from 'jquery';
import 'bootstrap/dist/js/bootstrap.min.js';
import 'summernote/dist/summernote.min.js';

export default class Summernote {
    constructor(selector){
        this.selector = selector;
        this.render(); 
    }

    static make(selector){
        return new Summernote(selector);
    }

    render(){
         $(this.selector).summernote({
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
                    const formData = new FormData();
                    formData.append("file", files[0]);

                    fetch("/upload", {
                        method: "POST",
                        body: formData,
                    })
                        .then((res) => res.json())
                        .then((data) => {
                            $("#editor").summernote(
                                "insertImage",
                                data.location
                            );
                        })
                        .catch((err) =>
                            console.error("Upload failed:", err)
                        );
                },
            },
        });
    }
}