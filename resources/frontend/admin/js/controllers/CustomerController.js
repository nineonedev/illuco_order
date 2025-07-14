import Controller from "../core/Controller";
import CountrySelectInput from '../components/Inputs/CountrySelectInput';
import Modal from '../shared/Modal';
import Loader from '../shared/Loader';
import SelectInput from "../components/Inputs/SelectInput";

export default class CustomerController extends Controller {
    form;
    cancelBtn;
    modal;
    loader;

    index() {
        this._logger.info("index");
        this._prepare(); 
    }

    create() {
        this._logger.info("create");
        this._prepare();

        this.form.addEventListener('submit', this._store.bind(this));
    }

    edit() {
        this._logger.info("edit");
        this._prepare();

        this.form.addEventListener('submit', this._update.bind(this));

        const deleteBtn = this.form.querySelector('[data-action="delete"]');
        deleteBtn?.addEventListener('click', this._destroy.bind(this));
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

            this._logger.success(result);

            if (success && data && data.customer) {
                location.href = `${action}/${data.customer.id}/edit`;
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
            this._logger.success(result);

            if (result.success) {
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

        const fd = new FormData(this.form);
        fd.set('_method', 'delete');

        try {
            submitter.disabled = true;
            this.loader.show();

            const result = await this._ajax.delete(action, fd);
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
        this.form = document.getElementById('frm');
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
}
