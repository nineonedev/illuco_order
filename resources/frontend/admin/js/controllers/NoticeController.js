import Controller from "../core/Controller";
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";
import SelectInput from "../components/Inputs/SelectInput";
import FileInput from "../components/Inputs/FileInput";
import DateTimeInput from "../components/Inputs/DateTimeInput";
import EditorInput from "../components/Inputs/EditorInput";

export default class NoticeController extends Controller {
    form;
    cancelBtn;
    modal;
    loader;

    index() {
        this._logger.info("index");
    }

    create() {
        this._logger.info("create");
        this._prepare();

        this.form.addEventListener("submit", this._store.bind(this));
    }

    edit() {
        this._logger.info("edit");
        this._prepare();

        this.form.addEventListener("submit", this._update.bind(this));

        const deleteBtn = this.form.querySelector('[data-action="delete"]');
        deleteBtn?.addEventListener("click", this._destroy.bind(this));
    }

    async _store(e) {
        e.preventDefault();

        const t = e.target;
        const fd = new FormData(t);
        const action = t.action;
        const submitter = e.submitter;

        try {
            submitter.disabled = true;
            this.loader.show();

            const result = await this._ajax.post(action, fd);
            const { success, data } = result;

            if (success && data && data.notice) {
                location.href = `${action}/edit/${data.notice.id}`;
            }
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    async _update(e) {
        e.preventDefault();

        const t = e.target;
        const fd = new FormData(t);
        const action = t.action;
        const submitter = e.submitter;

        try {
            submitter.disabled = true;
            this.loader.show();

            const result = await this._ajax.put(action, fd);
            const { success } = result;

            if (success) {
                location.reload();
            }
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    async _destroy(e) {
        if (!confirm("정말로 삭제하시겠습니까?")) return;

        const action = this.form.action;
        const submitter = e.currentTarget;

        try {
            submitter.disabled = true;
            this.loader.show();

            const result = await this._ajax.delete(action);
            this._logger.success(result);

            if (result.success) {
                this.cancelBtn?.click();
            }
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    _prepare() {
        this.form = document.getElementById("frm");
        this.cancelBtn = document.querySelector('[data-action="cancel"]');
        this.modal = Modal.make("portal").render();
        this.loader = Loader.make("portal").render();

        document.querySelectorAll('[data-view-type="select"]').forEach(el => {
            SelectInput.make(el).render();
        });

        document.querySelectorAll('[data-view-type="editor"]').forEach(el => {
            EditorInput.make(el).render();
        });

        document.querySelectorAll('[data-view-type="file"]').forEach(el => {
            FileInput.make(el).render();
        });

        document.querySelectorAll('[data-view-type="datetime"]').forEach(el => {
            DateTimeInput.make(el).render();
        });
    }
}
