import View from "../../core/View";
import Helper from "../../supports/Helper";
import InputFactory from "../Inputs/InputFactroy";
import SearchButton from "./SearchButton";
import TemplateForm from "./TemplateForm";

export default class Template extends View {
    _defineProps(){
        return {
            template: {},
            onSubmit: () => {}
        }
    }

    _defineState(){
        return {
            ...this._props
        }
    }
   
    _template(){
       return `
            <div class="no-page-row">
                <div class="no-page-head">
                    <h2 class="no-heading-sm">주문</h2>
                </div>

                <div data-ref="search"></div>
                
                <div class="no-form-inner">
                    <div class="no-form-group" data-ref="group"></div>
                </div>
            </div>
       `;
    }

    _render(){
        super._render();

        SearchButton.make(this.refs.search, {
            label: '제품 검색',
            onClick: this._handleClick.bind(this)
        }).render();

        if (Helper.isEmptyObject(this._state.template)){ 
            this._renderFallback();
            return;
        } 

        this._renderForm();
    }

    _handleClick(buttonView, evt){
        this._dispatch('fetch.templates', {template: this, button: buttonView, evt: evt});
    }

    _renderForm(){
        TemplateForm.make(this.refs.group, {
            template: this._state.template
        }).render();
    }

    _renderFallback(){
        this.refs.group.innerHTML = `
            <div class="no-form-empty-fallback">
                <p>선택된 제품이 없습니다. 제품을 선택해주세요.</p>
            </div>
        `
    }
}