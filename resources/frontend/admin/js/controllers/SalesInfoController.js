import Controller from "../core/Controller";
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";
import SelectInput from "../components/Inputs/SelectInput";
import FileInput from "../components/Inputs/FileInput";
import DateTimeInput from "../components/Inputs/DateTimeInput";
import EditorInput from "../components/Inputs/EditorInput";
import DateInput from "../components/Inputs/DateInput";
import Ajax from "../core/Ajax";

export default class SalesInfoController extends Controller {
    form;
    cancelBtn;
    modal;
    loader;

    index() {
        this._logger.info("index");
        this._prepare(); 
        this.form.addEventListener("submit", this._save.bind(this));
    }

    async _save(e) {
        e.preventDefault();

        const t = e.target;
        const fd = new FormData(t);
        const action = t.action;
        const submitter = e.submitter;

        try {
            submitter.disabled = true;
            this.loader.show();

            const result = await this._ajax.post(action, fd);
            const { success } = result;

            if (success) {
                location.reload();
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

        // document.querySelectorAll('[data-view-type="select"]').forEach(el => {
        //     SelectInput.make(el).render();
        // });

        // document.querySelectorAll('[data-view-type="editor"]').forEach(el => {
        //     EditorInput.make(el).render();
        // });

        // document.querySelectorAll('[data-view-type="file"]').forEach(el => {
        //     FileInput.make(el).render();
        // });

        // document.querySelectorAll('[data-view-type="datetime"]').forEach(el => {
        //     DateTimeInput.make(el).render();
        // });

        // document.querySelectorAll('[data-view-type="date"]').forEach(el => {
        //     const props = el.dataset.viewProps || '{}';
        //     DateInput.make(el, JSON.parse(props)).render();
        // })
    }
}
