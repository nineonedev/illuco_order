import Controller from "../../modules/core/Controller";
import File from "../components/File";
import LongText from "../components/LongText";
import DateTime from "../components/DateTime";

export default class NoticeController extends Controller {
    form;
    cancelBtn;

    index() {
        this._logger.info("index");
    }

    _prepare() {
        this.form = document.getElementById("frm");
        this.cancelBtn = document.querySelector('[data-action="cancel"]');

        LongText.make("content", { name: "content" }).render();

        document.querySelectorAll('[data-component-type="file"]').forEach(el => {
            File.make(el).render();
        });

        document.querySelectorAll('[data-component-type="datetime"]').forEach(el => {
            DateTime.make(el).render();
        });
    }

    create() {
        this._logger.info("create");
        this._prepare();

        this.form.addEventListener("submit", async (e) => {
            const result = await this._process(e, (data, action) =>
                this._ajax.post(action, data, true)
            );
            if (result?.success && this.cancelBtn) {
                this.cancelBtn.click();
            }
        });
    }

    edit() {
        this._logger.info("edit");
        this._prepare();

        this.form.addEventListener("submit", async (e) => {
            const result = await this._process(e, (data, action) =>
                this._ajax.put(action, data, true)
            );
            if (result?.success) {
                location.reload();
            }
        });

        const deleteBtn = this.form.querySelector('[data-action="delete"]');
        deleteBtn?.addEventListener("click", () => {
            const data = new FormData(this.form);
            data.set("_method", "delete");
            this._destroy(this.form.action, data);
        });
    }

    async _destroy(action, data) {
        if (!confirm("정말로 삭제하시겠습니까?")) return;

        const result = await this._ajax.delete(action, data);
        this._logger.success(result);

        if (result.success) {
            this.cancelBtn?.click();
        }
    }
}
