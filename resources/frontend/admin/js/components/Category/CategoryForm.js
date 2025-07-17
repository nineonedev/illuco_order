import View from '../../core/View';
import TextInput from '../Inputs/TextInput';
import NumberInput from '../Inputs/NumberInput';

export default class CategoryForm extends View {
    _template()
    {
        this._inputHookId = this._generateHookId();

        return `
            <div>
                <form 
                    method="post" 
                    action="/admin/product-categories/" 
                    enctype="multipart/form-data" 
                    class="no-card" 
                    data-ref="form"
                >
                    <div class="no-form-group" id="${this._inputHookId}">
                    </div>

                    <div class="no-form-action">
                        <button type="submit" class="no-btn-primary --sm">
                            <span>추가</span>
                        </button>
                    </div>
                </form>
            </div>
        `;
    }
    
    _bindEvents()
    {
        TextInput.make(this._inputHookId, {
            name: 'label',
            label: '이름',
            required: true,
        }).render(); 

        // TextInput.make(this._inputHookId, {
        //     name: 'slug',
        //     label: '식별자',
        //     required: true,
        // }).render(); 

        NumberInput.make(this._inputHookId, {
            name: 'sort_order',
            label: '순서',
            required: true,
        }).render(); 


        this.on(this.refs.form, 'submit', this._handleSubmit.bind(this));
    }

    _handleSubmit(self, e){
        e.preventDefault();

        const t = e.target;
        const fd = new FormData(t);
        this._dispatch('category.create', {data: fd, button: e.submitter, form: t});
    }
}