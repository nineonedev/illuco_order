import Controller from "../core/Controller";
import Modal from '../shared/Modal';
import Loader from '../shared/Loader';
import SelectInput from "../components/Inputs/SelectInput";
import Ajax from "../core/Ajax";

export default class OrderController extends Controller {
    loader;
    modal;
    form;
    cancelBtn;

    edit() {
        this._logger.info("index");
        this.modal = Modal.make('portal').render();
        this.loader = Loader.make('portal').render();
        
        const form = document.getElementById('frm'); 
        this.form = form;

        const deleteBtn = form.querySelector('[data-action=delete]');
        this.cancelBtn = form.querySelector('[data-action=cancel]');
        
        document.querySelectorAll('[data-view="order-item-form"]').forEach(form => {
            form.addEventListener('submit', this._restore.bind(this));
        })

        const restoreForm = document.getElementById('restore-form')
        restoreForm.addEventListener('submit', this._restoreAll.bind(this));
        
        deleteBtn.addEventListener('click', this._destroy.bind(this));
        form.addEventListener('submit', this._update.bind(this));

        SelectInput.make('order_status', {
            // onChange: this._handleChange.bind(this)
        }).render();
    }

    

    async _restoreAll(e){
        e.preventDefault();
        const button = e.submitter;
        const form = e.target;
        this._logger.info(button, form);

        try {
            button.disabled = true; 
            this.loader.show();

            const fd = new FormData(form); 
            const result = await new Ajax(false).post(form.action, fd);
            this._logger.success(result);

        } finally {
            this.loader.hide();
            button.disabled = false; 
        }
    }

    async _restore(e) {
        e.preventDefault();
        const button = e.submitter;
        const form = e.target;
        this._logger.info(button, form);

        try {
            button.disabled = true; 
            this.loader.show();

            const fd = new FormData(form); 
            const result = await new Ajax(false).post(form.action, fd);
            this._logger.success(result);

        } finally {
            this.loader.hide();
            button.disabled = false; 
        }
    }

    async _update(e) {
        e.preventDefault();
        const button = e.submitter; 
        const form = e.target; 
        this._logger.info(button, form); 
        
        try {
            button.disabled = true; 
            this.loader.show();
            
            const fd = new FormData(form);
            const result = await new Ajax(false).put(form.action, fd);
            this._logger.success(result);

        } finally {

            this.loader.hide();
            button.disabled = false; 
        }
    }

    async _destroy(e) {
        
        if (!confirm('정말로 삭제하시겠습니까?')) {
            return;
        }

        const button = e.currentTarget;
        const form = this.form;
        this._logger.info(button, form);

        try {
            button.disabled = true; 
            this.loader.show();

            const fd = new FormData(form); 
            const result = await new Ajax(false).delete(form.action, fd);
            this._logger.success(result);

            if (result.data.redirect) {
                location.href = result.data.redirect;
            } else {
                this.cancelBtn.click();
            }

        } finally {
            this.loader.hide();
            button.disabled = false; 
        }
    }
}
