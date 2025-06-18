import Component from "../../../Modules/Core/Component";
import Text from "../Text";
import Select from "../Select";
import Checkbox from "../Checkbox";

export default class AttributeItem extends Component {
    _boot() {
        this._type = 'attribute-item';
        super._boot();
    }

    _defineProps() {
        return {
            id: null,
            label: '',
            name: '',
            type: '',
            required: false,
            sort_order: 0,
            template_id: null,
        };
    }

    _defineState() {
        return {
            ...this._props
        };
    }

    _template() {
        this._formId = this._generateNodeId();
        const {sort_order, template_id, id} = this._state;

        return `
            <li class="no-product-attribute-item">
                <form data-ref="form" id="${this._formId}" method="post" action="/admin/product-attributes/${id}">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="sort_order" value="${sort_order}">
                    <input type="hidden" name="template_id" value="${template_id}">

                    <div class="no-product-attribute-item-flex">
                        <div data-ref="type"></div>
                        <div data-ref="required" class="no-product-attribute-item-check"></div>
                    </div>
                    <div class="no-product-attribute-item-flex">
                        <div data-ref="label"></div>
                        <div data-ref="name"></div>
                    </div>

                    <div class="no-product-attribute-item-actions">
                        <button type="submit" class="no-btn-primary" data-ref="updateBtn">수정</button>
                        <button type="button" class="no-btn-error" data-ref="destroyBtn">삭제</button>
                    </div>
                </form>
            </li>
        `;
    }

    _bindEvents() {
        const { label, name, type, required } = this._state;
        console.log(1);

        this._labelInput = Text.make(this.refs.label, {
            label: "이름",
            name: "label",
            required: true,
            value: label,
        }).render();

        this._nameInput = Text.make(this.refs.name, {
            label: "식별자",
            name: "name",
            required: true,
            value: name,
        }).render();

        this._typeInput = Select.make(this.refs.type, {
            label: "타입 선택",
            name: "type",
            value: type,
            options: [
                { label: '텍스트', value: 'text' },
                { label: '긴텍스트', value: 'longText' },
                { label: '날짜', value: 'date' },
                { label: '숫자', value: 'number' },
                { label: '선택', value: 'select' },
                { label: '다중선택', value: 'multi-select' },
            ]
        }).render();

        this._requiredInput = Checkbox.make(this.refs.required, {
            label: "필수 설정",
            name: "required",
            required: false,
            value: required,
        }).render();

        this.on(this.refs.form, 'submit', this._handleUpdate.bind(this));
        this.on(this.refs.destroyBtn, 'click', this._handleDestroy.bind(this));
    }

    _handleUpdate(comp, e) {
        e.preventDefault();
        const t = e.target;
        const fd = new FormData(t);

        this.dispatch('attr.update', {
            component: this,
            data: fd,
            action: t.action,
            submitter: e.submitter,
        });
    }

    _handleDestroy() {
        this.dispatch('attr.destroy', {
            component: this,
            props: this._props,
        });
    }
}
