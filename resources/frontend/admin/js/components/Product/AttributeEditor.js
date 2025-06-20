
import Component from "../../core/Component";
import Checkbox from "../Checkbox";
import Select from "../Select";
import Text from "../Text";
import AttributeManager from "./AttributeManager";
import AttributeOptionItem from "./AttributeOptionItem";

export default class AttributeEditor extends Component {
    _type = 'attribute-editor';

    _boot(){
        super._boot();
        this._optionListId = this._generateNodeId();
    }

    _defineProps() {
        return {
            attribute: {},
            status: 'attr',
        };
    }

    _defineState() {
        return {
            ...this._props,
        };
    }

    _template() {
        const {status} = this._state;
        return `
            <div>
                <div>
                    <button type="button" data-status="attr" class="no-prod-attr-tab">속성</button>
                    <button type="button" data-status="option" class="no-prod-attr-tab">옵션</button>
                    <button type="button" data-status="rule" class="no-prod-attr-tab">규칙</button>
                </div>
                <div>
                    ${status === 'attr' ? this._renderAttributes() : ``}
                    ${status === 'option' ? this._renderOptions() : ``}
                    ${status === 'rule' ? this._renderRules() : ``}
                </div>
            </div>
        `;
    }

    _render(){
        super._render(); 

        const {status, attribute } = this._state;
        const {type, name, default_value, label, required, options} = attribute;


        if (status === 'attr') {
            
            this.on(this.refs.form, 'submit', this._handleAttrUpdate.bind(this));
            this.on(this.refs.deleteBtn, 'click', this._handleAttrDelete.bind(this));

            console.log(type, AttributeManager.options);
            
            Select.make(this.refs.type, {
                label: "형태",
                name: "type",
                options: AttributeManager.options,
                value: type
            }).render();
    
            Text.make(this.refs.label, {
                label: "이름",
                name: "label",
                required: true,
                value: label
            }).render();
    
            Text.make(this.refs.name, {
                label: "식별자",
                name: "name",
                required: true,
                value: name
            }).render();

            Text.make(this.refs.name, {
                label: "기본값",
                name: "default_value",
                value: default_value ?? '',
            }).render();
    
            Checkbox.make(this.refs.required, {
                label: "필수 설정",
                name: "required",
                spacing: true,
                value: required,
            }).render();
            return; 
        }

        if (status === 'option') {
            if (!['select', 'multi-select'].includes(type)){
                return; 
            }


            this.on(this.refs.form, 'submit', this._handleOptionAdd.bind(this));

            Text.make(this.refs.label, {
                label: "이름",
                name: "label",
                required: true,
            }).render();
            Text.make(this.refs.value, {
                label: "식별값",
                name: "value",
                required: true,
            }).render();

            if(options && options.length > 0) {
                options.forEach(opt => {
                    const optItem = AttributeOptionItem.make(this._optionListId, {...opt}).render();
                });
            }
            
            return;
        }

        if (status === 'rule') {
            
            return;
        }
    }

    _handleAttrUpdate(self, e){
        e.preventDefault(); 
        const t = e.target; 
        const fd = new FormData(t); 
        
        this.dispatch('attr.update', {
            data: fd, 
            onDisabled: this.setButtonDisabled.bind(this),
        });
    }

    _handleAttrDelete(self, e){
        this.dispatch('attr.destroy', {
            id: this._state.attribute.id, 
            onDisabled: this.setButtonDisabled.bind(this)
        });
    }

    setButtonDisabled(disabled = true){
        if(this.refs.updateBtn) {
            this.refs.updateBtn.disabled = disabled;
        }

        if(this.refs.deleteBtn) {
            this.refs.deleteBtn.disabled = disabled;
        }
    }

    _renderAttributes(){
        const {id} = this._state.attribute;
        return `
            <div class="no-prod-attr-tab-content">
                <form method="post" action="" enctype="multipart/form-data" data-ref="form">
                    <input type="hidden" name="id" value="${id}"/>
                    <div data-ref="type"></div>
                    <div data-ref="label"></div>
                    <div data-ref="name"></div>
                    <div data-ref="default_value"></div>
                    <div data-ref="required"></div>
                    
                    <div class="no-prod-attr-actions">
                        <button type="button" class="no-btn-error" data-ref="deleteBtn">
                            <span>삭제</span>
                        </button>
                        <button type="submit" class="no-btn-primary" data-ref="updateBtn">
                            <span>수정</span>
                        </button>
                    </div>
                </form>
            </div>
        `;
    }

    _handleOptionAdd(self, e){
        e.preventDefault(); 
        
        const t = e.target; 
        const fd = new FormData(t); 
        
        this.dispatch('option.store', {
            data: fd,
            onDisabled: (disabled) => {
                e.submitter.disabled = disabled;
            }
        })
    }

    _renderOptions(){
        const {attribute} = this._state;
        const {id, type, options} = attribute;

        if (!['select', 'multi-select'].includes(type)) {
            return `<div>
                <p>선택 또는 다중 선택만 옵션추가가 가능합니다.</p>
                <p>옵션 추가를 원하실 경우, 속성의 형태를 변경해주세요.</p>
            </div>`; 
        }

        return `
            <div class="no-prod-attr-tab-content">
                <form method="post" data-ref="form">
                    <input type="hidden" name="attribute_id" value="${id}"/>
                    <div data-ref="label"></div>
                    <div data-ref="value"></div>
                    
                    <div class="no-prod-attr-actions">
                        <button type="submit" class="no-btn-primary" data-ref="addBtn">
                            <span>추가</span>
                        </button>
                    </div>
                </form>
                ${options.length ? `<ol class="no-prod-opt-list" id="${this._optionListId}"></ol>` : '<p>등록된 옵션이 없습니다.</p>'}
            </div>
        `;
    }

    resetOptionform(){
        if (this.refs.form && this._state.status === 'option') {
            this.refs.form.reset();
        }
            
    }

    _renderRules(){
        return `
            <div class="no-prod-attr-tab-content">
                <h3>규칙추가</h3>
                <form>
                    
                </form>
            </div>
        `;
    }

    _bindEvents(){
        this.qsAll('button[data-status]').forEach(btn => {
            this.on(btn, 'click', this._handleTab.bind(this));
        }); 


    }

    _handleTab(self, e){
        this.setState({status: e.target.dataset.status});
    }
}
