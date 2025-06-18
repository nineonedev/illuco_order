import Controller from "../../modules/core/Controller";
import Select from "../components/Select";
import CountrySelect from '../components/CountrySelect';

export default class CustomerController extends Controller {
    form;
    cancelBtn;

    index() {
        this._logger.info("index");
    }

    _prepare() {
        CountrySelect.make('country-hook').render();
        Select.make("dealer-hook").render();
    }

    create() {
        this._logger.info("create");
        this._prepare();

        this.form = document.getElementById('frm'); 
        this.form.addEventListener('submit', this._store.bind(this));
        this.cancelBtn = document.querySelector('[data-action="cancel"]');
    }

    edit() {
        this._logger.info("edit");
        this._prepare();
        
        this.form = document.getElementById('frm'); 
        this.form.addEventListener('submit', this._update.bind(this));
        this.cancelBtn = document.querySelector('[data-action="cancel"]');

        this.form.querySelector('[data-action="delete"]').addEventListener('click', () => {
            const data = new FormData(this.form);
            data.set('_method', 'delete');
            this._destroy(this.form.action, data);
        })
    }

    async _store(e) {
        e.preventDefault();

        const t = e.target;
        const data = new FormData(t); 
        const action = t.action; 

        e.submitter.disabled = true;
        const result = await this._ajax.post(action, data);
        e.submitter.disabled = false;

        this._logger.success(result);

        if(result.success){
            this.cancelBtn.click();
        }

    }

    async _update(e) {
        e.preventDefault();

        const t = e.target;
        const data = new FormData(t); 
        const action = t.action; 

        e.submitter.disabled = true;
        const result = await this._ajax.put(action, data);
        e.submitter.disabled = false;

        this._logger.success(result);
        if(result.success){
            location.reload(); 
        }

    }

    async _destroy(action, data) {
        if (!confirm("정말로 삭제하시겠습니까?")) return;
        const result = await this._ajax.delete(action, data);
        this._logger.success(result);

        if (result.success) {
            this.cancelBtn.click();
        }
    }
}
