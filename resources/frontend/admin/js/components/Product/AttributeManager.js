import Component from "../../core/Component";
import AttributeGenerator from "./AttributeGenerator";
import AttributeList from "./AttributeList";

export default class AttributeManager extends Component {

    static options = [
        {label: '텍스트', value: 'text'},
        {label: '긴텍스트', value: 'longText'},
        {label: '날짜', value: 'date'},
        {label: '숫자', value: 'number'},
        {label: '선택', value: 'select'},
        {label: '다중선택', value: 'multi-select'},
    ];

    _type = 'attribute-manager';

    _boot() {
        super._boot(); 
        this._generator = null;
        this._list = null;
    }


    _defineProps(){
        return {
            attributes: [],
            template_id: null,
            action: null,
        }
    }

    _defineState(){
        return {
            ...this._props
        }
    }

    _render(){
        super._render();

        this._generator = AttributeGenerator
            .make(this._id, {
                options: this.getOptions(), 
                action: this._state.action,
                template_id: this._state.template_id,
            })
            .render();

        this._list = AttributeList
            .make(this._id, {
                attributes: this._state.attributes,
            })
            .render(this._state.attributes.length); 
    }

    resetForm(){
        this._generator.reset();
    }

    renderList(newState = {}, shouldRender = true){
        this._list.setState(newState, shouldRender);
    }
    
    static findOption(value){
        return AttributeManager.options.find(opt => opt.value === value);
    }

    getOptions(){
        return AttributeManager.options;
    }

    getCurrentSortOrder(){
        return this._list.getCurrentSortOrder() || 0;
    }
}