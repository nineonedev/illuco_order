import Component from "../../../Modules/Core/Component";
import Select from "../Select";
import Text from "../Text";
import Checkbox from "../Checkbox";

export default class AttributeOptionItem extends Component {
    _type = 'attribute-option-item'; 

    _defineProps(){
        return {
            id: null,
            label: null,
            value: null,
            attribute_id: null,
        }
    }

    _defineState(){
        return {
            ...this._props
        }
    }

    _template(){
        const {id, attribute_id, } = this.state;

        return `
            <li class="no-prod-opt-item">
                <form method="post" data-ref="form">
                    <input type="hidden" name="attribute_id" value="${attribute_id}" />
                    <input type="hidden" name="id" value="${id}" />
                    
                    <div data-ref="label"></div>
                    <div data-ref="value"></div>

                    <div class="no-prod-attr-actions">
                        <button type="button" class="no-btn-error" data-ref="deleteBtn">
                            <span>삭제</span>
                        </button>
                        <button type="submit" class="no-btn-primary">
                            <span>수정</span>
                        </button>
                    </div>
                </form>
            </li>
        `;
    }

    _render(){
        const {label, value} = this.state;

        super._render();
        
        Text.make(this.refs.label, {
            label: "이름",
            name: "label",
            value: label,
            required: true,
        }).render();
        Text.make(this.refs.value, {
            label: "식별값",
            name: "value",
            value: value,
            required: true,
        }).render();
    }

    _bindEvents(){
        this.on(this._refs.form, 'submit', this._handleSubmit.bind(this));
        this.on(this._refs.deleteBtn, 'click', this._handleDelete.bind(this));
    }

    _handleSubmit(self, e){
        e.preventDefault(); 
        const t = e.target;
        const fd = new FormData(t); 

        this.dispatch('opt.update', {
            data: t,
        })
    }

    _handleDelete(self, e) {

    }
}