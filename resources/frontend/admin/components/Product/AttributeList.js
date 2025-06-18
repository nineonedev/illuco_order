import Component from "../../../Modules/Core/Component";
import Select from "../Select";
import Text from "../Text";
import Checkbox from "../Checkbox";
import AttributeItem from "./AttributeItem";

export default class AttributeList extends Component {
    _boot(){
        this._type = 'attribute-list';
        super._boot();
    }

    _defineProps(){
        return {
            template_id: null,
            attributes: [],
            action: null,
        }
    }

    _defineState(){
        return {
            template_id: this._props.template_id,
            attributes: this._props.attributes,
            action: this._props.action,
        }
    }

    _template(){
        this._listId = this._generateNodeId();
        const {template_id, action, attributes} = this._state; 
        
        if (!template_id) {
            throw new Error('Template id required');
        }

        return `
            <div>
                <form data-ref="form" action="${action}" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="template_id" value="${template_id}"/>
                    <div data-ref="type"></div>
                    <div data-ref="label"></div>
                    <div data-ref="name"></div>
                    <div data-ref="required"></div>
                    <button type="submit" class="no-btn-primary">
                        <span>추가</span>
                    </button>
                </form>
                <ul data-ref="list" class="no-product-attribute-list" id="${this._listId}"></ul>
                ${attributes.length === 0 ? `<p>등록된 속성이 없습니다.</p>` : ''}
            </div>
        `;
    }

    renderAttributes(){
        const list = this.refs.list;
        list.innerHTML = ''; // 기존 항목 초기화

        this._state.attributes.forEach((attr, index) => {
            const item =  AttributeItem.make(this._listId, {
                ...attr,
            }).render();
        });
    }

    _bindEvents(){
        this._typeInput = Select.make(this.refs.type, {
            label: "타입 선택",
            name: "type",
            options: [
                {label: '텍스트', value: 'text'},
                {label: '긴텍스트', value: 'longText'},
                {label: '날짜', value: 'date'},
                {label: '숫자', value: 'number'},
                {label: '선택', value: 'select'},
                {label: '다중선택', value: 'multi-select'},
            ]
        }).render();

        this._textInput = Text.make(this.refs.label, {
            label: "이름",
            name: "label",
            required: true,
        }).render();

        this._nameInput = Text.make(this.refs.name, {
            label: "식별자",
            name: "name",
            required: true,
        }).render();

        this._requiredInput = Checkbox.make(this.refs.required, {
            label: "필수 설정",
            name: "required",
            required: false,
            spacing: true, 
        }).render();

        this.on(this.refs.form, 'submit', this._handleSubmit.bind(this));

        this.renderAttributes();
    }

    _handleSubmit(self, e){
        e.preventDefault();

        const t = e.target;
        const fd = new FormData(t);
        fd.append('sort_order', this.refs.list.children.length);

        this.dispatch('attr.store', {
            data: fd,
            action: t.action,
            submitter: e.submitter,
            listEl: this.refs.list,
        });
    }
}