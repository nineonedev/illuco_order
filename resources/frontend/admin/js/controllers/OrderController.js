import Controller from "../core/Controller";
import Modal from '../shared/Modal';
import Loader from '../shared/Loader';
import SelectInput from "../components/Inputs/SelectInput";
import DateInput from "../components/Inputs/DateInput";
import Ajax from "../core/Ajax";

export default class OrderController extends Controller {
    loader;
    modal;
    form;
    cancelBtn;


    _prepare(){
        this.modal = Modal.make('portal').render();
        this.loader = Loader.make('portal').render();
    }

    show(){
        this._prepare();
        this._logger.info("show");

        const form = document.getElementById('cancel-frm'); 
        if (!form) {
            console.error('No found form with id: "frm"');   
        }
        
        this.form = form;
        form.addEventListener('submit', this.cancel.bind(this));
    }

    async cancel(e){
        e.preventDefault();

        if (!confirm("정말로 이 주문을 취소하시겠습니까?\n취소 이후에는 되돌릴 수 없습니다.")) return;

        const button = e.submitter; 
        const form = e.target; 
        this._logger.info(button, form); 

        try {
            button.disabled = true; 
            this.loader.show();
            
            const result = await new Ajax(false).put(form.action);
            this._logger.success(result);

            if (result.success) {
                location.reload(); 
            }

        } finally {

            this.loader.hide();
            button.disabled = false; 
        }
    }

    edit() {
        this._prepare();
        this._logger.info("index");
        
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

            document.querySelectorAll('[data-view-type="select"]').forEach(el => {
                const props = el.dataset.viewProps || '{}';
                SelectInput.make(el, JSON.parse(props)).render();
            })
            
            document.querySelectorAll('[data-view-type="date"]').forEach(el => {
                const props = el.dataset.viewProps || '{}';
                DateInput.make(el, JSON.parse(props)).render();
            })
            
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
        
        const fd = new FormData(form);

        if (!fd.get('order_status')) {
            alert('주문 상태를 선택해주세요.');
            return;
        }

        try {
            button.disabled = true; 
            this.loader.show();
            
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
