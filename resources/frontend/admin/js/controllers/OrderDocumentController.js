import Controller from "../core/Controller";
import Modal from '../shared/Modal';
import Loader from '../shared/Loader';
import Ajax from "../core/Ajax";

export default class OrderController extends Controller {
    loader;
    modal;
    form;
    cancelBtn;

    print(){
        const element = document.getElementById('print-area');

        if (!element) {
            console.error('print-area not found!');
            return;
        }

        const printBtn = document.getElementById('print-btn');

        if(!printBtn) {
            console.error('print-btn not found!');
            return;
        }

        printBtn.addEventListener('click', () => {
            html2pdf(element, {
                margin:       0,
                filename:     element.dataset.pdfName || 'order-document',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, dpi: 300},
                jsPDF:        { unit: 'mm', format: 'a4', orientation: element.dataset.page || 'portrait' }
            });
        });
        
    }

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
