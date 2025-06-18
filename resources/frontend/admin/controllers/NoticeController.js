import Controller from "../../modules/core/Controller";
import File from "../components/File";
import LongText from "../components/LongText";
import DateTime from '../components/DateTime';

export default class NoticeController extends Controller {
    form;
    cancelBtn;

    index() {
        this._logger.info("index");
    }

    create() {
        this._logger.info("create");

        this._prepare();

        this.form = document.getElementById("frm");
        this.cancelBtn = document.querySelector('[data-action="cancel"]');

        this.form.addEventListener("submit", this._store.bind(this));
    }

    edit() {
        this._logger.info("edit");

        this._prepare();

        this.form = document.getElementById("frm");
        this.cancelBtn = document.querySelector('[data-action="cancel"]');

        this.form.addEventListener("submit", this._update.bind(this));

        const deleteBtn = this.form.querySelector('[data-action="delete"]');
        deleteBtn?.addEventListener("click", () => {
            const data = new FormData(this.form);
            data.set("_method", "delete");
            this._destroy(this.form.action, data);
        });
    }

    _prepare() {
        LongText.make("content", { name: "content" }).render();

        document.querySelectorAll('[data-component-type="file"]').forEach(el => {
            File.make(el).render();
        });

        document.querySelectorAll('[data-component-type="datetime"]').forEach(el => {
            DateTime.make(el).render();
        })
        
    }

    async _store(e) {
        e.preventDefault();

        const form = e.target;
        const data = new FormData(form);
        const action = form.action;

        e.submitter.disabled = true;
        const result = await this._ajax.post(action, data);
        e.submitter.disabled = false;

        this._logger.success(result);
        if (result.success) {
            this.cancelBtn?.click();
        }
    }

    async _update(e) {
        e.preventDefault();

        const form = e.target;
        const data = new FormData(form);
        const action = form.action;

        e.submitter.disabled = true;
        const result = await this._ajax.put(action, data);
        e.submitter.disabled = false;

        this._logger.success(result);
        if (result.success) {
            location.reload();
        }
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
