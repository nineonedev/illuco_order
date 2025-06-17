
import Controller from "../../modules/core/Controller";
import File from "../components/File";
import Form from "../components/Form";
import LongText from "../components/LongText";

export default class NoticeController extends Controller {
    index() {
        this._logger.info("index");
    }

    create() {
        this._logger.info("create");

        const form = Form.make("frm", {onSubmit: this._store.bind(this)});

        LongText.make("content", { name: "content" }).render();

        document.querySelectorAll('[data-component-type="file"]').forEach(el => {
            File.make(el).render();
        })
    }

    edit() {
        this._logger.info("edit");

        const form = Form.make("frm", {onSubmit: this._update.bind(this)});
        form.onMounted((form) => {
            const deleteBtn = form.qs('[data-action="delete"]');
            form.on(deleteBtn, 'click', () => form.dispatch('destroy', {data: new FormData(form._el), action: form._el.action}));
        });
        form.render();
        
        LongText.make("content", { name: "content" }).render();

        document.querySelectorAll('[data-component-type="file"]').forEach(el => {
            File.make(el).render();
        })


        
        this._listen('destroy', this._destroy.bind(this));
    }

    async _store({ data, action }, evt) {
        evt.submitter.disabled = true; 
        const result = await this._ajax.post(action, data);
        evt.submitter.disabled = false; 
        
        this._loggee.success(result); 
    }


    async _update(data, evt) {
        evt.submitter.disabled = true; 
        const result = await this._ajax.put(action, data);
        evt.submitter.disabled = false; 
        
        this._loggee.success(result); 
    }

    async _destroy({data, action}, evt) {
        if (!confirm('정말로 삭제하시겠습니까?')) return;
        
        const result = await this._ajax.delete(action, data);
        this._loggee.success(result);
    }
}
