import Controller from "../core/Controller";
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";
import SelectInput from "../components/Inputs/SelectInput";
import CountrySelectInput from "../components/Inputs/CountrySelectInput";

export default class RoleController extends Controller {
    form;
    cancelBtn;
    modal;
    loader;

    index() {
        this._logger.info("index");
        this._prepare(); 
    }

    _prepare() {
        this.form = document.getElementById("frm");
        this.cancelBtn = document.querySelector('[data-action="cancel"]');
        this.modal = Modal.make('portal').render();
        this.loader = Loader.make('portal').render();

        document.querySelectorAll('[data-view-type="country-select"]').forEach(el => {
            CountrySelectInput.make(el).render();
        });

        document.querySelectorAll('[data-view-type="select"]').forEach(el => {
            SelectInput.make(el).render();
        });
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

            const result = await this._ajax.post(action, fd, true);
            const { success, data } = result;

            if (success && data && data.role) {
                // create 후 edit 페이지로 이동
                location.href = `${action}/edit/${data.role.id}`;
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

            const result = await this._ajax.put(action, fd, true);
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
}
