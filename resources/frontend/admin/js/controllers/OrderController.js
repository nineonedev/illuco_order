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

        
        const items = document.querySelectorAll('[data-view="order-item-form"]');

        if(items) {
            items.forEach(form => {
                form.addEventListener('submit', this._restore.bind(this));
            })

        }

        this.cancelBtn = form.querySelector('[data-action=cancel]');

        if(form){
            const deleteBtn = form.querySelector('[data-action=delete]');

            SelectInput.make('order_status', {
                // onChange: this._handleChange.bind(this)
            }).render();
                
            const restoreForm = document.getElementById('restore-form')
            if (restoreForm){
                restoreForm.addEventListener('submit', this._restoreAll.bind(this));
            }
            
            if(deleteBtn){
                deleteBtn.addEventListener('click', this._destroy.bind(this));
                form.addEventListener('submit', this._update.bind(this));
            }
        }

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
