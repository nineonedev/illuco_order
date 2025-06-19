import Component from "../../../Modules/Core/Component";
import AttributeManager from "./AttributeManager";

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
        const {sort_order, template_id, id, type, label} = this._state;
        const option = AttributeManager.findOption(type);

        return `
            <tr class="no-prod-attr-item">
                <td>
                    <button type="button" class="no-prod-attr-handle">
                        <i class="fa-regular fa-grip-dots-vertical"></i>
                    </button> 
                </td>
                <td>
                    <span>${option.label}</span>
                </td>
                <td>
                    <span>${label}</span>
                </td>
                <td>
                    <div class="no-prod-attr-list__action">
                        <button type="button" data-ref="openBtn" class="no-btn-primary-outline">열기</button>
                        <button type="button" data-ref="deleteBtn" class="no-btn-error-outline">삭제</button>
                    </div>
                </td>
            </tr>
        `;
    }

    _bindEvents(){
        this.on(this.refs.openBtn, 'click', this._handleOpen.bind(this));
        this.on(this.refs.deleteBtn, 'click', this._handleDelete.bind(this));
    }

    _handleOpen(self, e){   
        this._logger.info('open');
        this.dispatch('attr.open', {id: this._state.id, onDisabled: this.setButtonDisabled.bind(this)});
    }   
    
    _handleDelete(self, e){
        this._logger.info('delete');
        this.dispatch('attr.destroy', {id: this._state.id, onDisabled: this.setButtonDisabled.bind(this)});
    }

    setButtonDisabled(disabled = true){
        this.refs.openBtn.disabled = disabled; 
        this.refs.deleteBtn.disabled = disabled; 
    }
}
