import Controller from "../core/Controller";
import Modal from '../shared/Modal';
import Loader from '../shared/Loader';
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

        form.addEventListener('submit', this._update.bind(this)); 
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
}
