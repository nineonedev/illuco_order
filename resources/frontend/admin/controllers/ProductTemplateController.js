import Ajax from "../../Modules/Core/Ajax";
import Controller from "../../modules/core/Controller";
import File from '../components/File';
import AttributeList from "../components/Product/AttributeList";
import Select from '../components/Select';

export default class ProductTemplateController extends Controller {
    form;
    cancelBtn;
    attributeList;

    _prepare() {
        this.form = document.getElementById('frm');
        this.cancelBtn = document.querySelector('[data-action="cancel"]');

        document.querySelectorAll('[data-component-type=file]').forEach(el => {
            File.make(el).render();
        });

        document.querySelectorAll('[data-component-type=select]').forEach(el => {
            Select.make(el).render();
        })
    }

    show(){
        this._logger.info("show");
    }


    create() {
        this._logger.info("create");
        this._prepare();

        this.form.addEventListener('submit', async (e) => {
            const result = await this._process(e, (data, action) => this._ajax.post(action, data, true))
            if (result?.success && this.cancelBtn) {
                this.cancelBtn.click();
            }

        });
    }

    async edit() {
        this._logger.info("edit");
        this._prepare();

        this.form.addEventListener('submit', async (e) => {
            
            const result = await this._process(e, (data, action) => this._ajax.put(action, data, true))
            if (result?.success) {
                location.reload();
            }
            
        });

        const deleteBtn = this.form.querySelector('[data-action="delete"]');
        deleteBtn?.addEventListener('click', () => {
            const data = new FormData(this.form);
            data.set('_method', 'delete');
            this._destroy(this.form.action, data);
        });

        this.attributeList = AttributeList.make('attr-hook', {
            attributes: [],
        });
        this.attributeList.render();

        
        this._listen('attr.store', this._storeAttribute.bind(this));
        await this._fetchAttributes();
    }

    async _fetchAttributes() {
        const action = this.form.dataset.showAction;
        const result = await new Ajax(true).get(action);
        const {data} = result;
        this.attributeList.setState({attributes: data.template.attributes});
    }

    async _storeAttribute({submitter, listEl, data, action}, event) {
        this._logger.success(Object.fromEntries(data));

        try {
            submitter.disabled = true; 
            
            const result = await new Ajax(true).post(action, data);
            this._logger.success(result); 

            if (result.success) {
                
                await this._fetchAttributes(); 
            }

        } finally {
            submitter.disabled = false; 
        }
    }

    async _destroy(action, data) {
        if (!confirm("정말로 삭제하시겠습니까?")) return;

        const result = await this._ajax.delete(action, data);
        this._logger.success(result);

        if (result.success) {
            this.cancelBtn?.click();
        }
    }
}
