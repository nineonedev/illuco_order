import Component from "../../../Modules/Core/Component";
import Select from "../Select";
import Text from "../Text";
import Checkbox from "../Checkbox";

export default class AttributeGenerator extends Component {
    _type = 'attribute-generator'; 

    _defineProps(){
        return {
            options: [],
            action: null,
            template_id: null,
            buttonLabel: '추가',
        }
    }

    _template(){
        const {action, template_id, buttonLabel} = this._props;

        return `
            <div class="no-prod-attribute-generator">
                <form data-ref="form" method="post" action="${action}" enctype="multipart/form-data">
                    <input type="hidden" name="template_id" value="${template_id}" />
                    <div data-ref="type"></div>
                    <div data-ref="label"></div>
                    <div data-ref="name"></div>
                    <div data-ref="required"></div>
                    <button type="submit" class="no-btn-primary">
                        <span>${buttonLabel}</span>
                    </button>
                </form>
            </div>
        `;
    }

    reset(){
        this.refs.form.reset(); 
    }

    _afterRender(){
        
        Select.make(this.refs.type, {
            label: "타입 선택",
            name: "type",
            options: this._props.options,
        }).render();

        Text.make(this.refs.label, {
            label: "이름",
            name: "label",
            required: true,
        }).render();

        Text.make(this.refs.name, {
            label: "식별자",
            name: "name",
            required: true,
        }).render();

        Checkbox.make(this.refs.required, {
            label: "필수 설정",
            name: "required",
            required: false,
            spacing: true, 
        }).render();
    }

    _bindEvents(){
        this.on(this.refs.form, 'submit', this._handleSubmit.bind(this));
    }

    _handleSubmit(self, e){
        e.preventDefault();
        
        const t = e.target;
        const fd = new FormData(t);

        this.dispatch('attr.store', {
            data: fd,
            submitter: e.submitter,
            action: t.action
        });
    }
}