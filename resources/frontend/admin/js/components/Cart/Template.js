import View from "../../core/View";
import Button from "../../shared/Button";
import Helper from "../../supports/Helper";
import InputFactory from "../Inputs/InputFactroy";
import SearchButton from "./SearchButton";
import TemplateForm from "./TemplateForm";

export default class Template extends View {
    _defineProps(){
        return {
            template: {},
            cartitem: {},
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
                    <div class="no-page-head__between">
                        <h2 class="no-heading-sm">주문</h2>
                        <div data-ref="fresh"></div>
                    </div>
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

        Button.make(this.refs.fresh, {
            label: '초기화', 
            className: 'no-btn-success-outline --xs',
            onClick: this._handleFresh.bind(this),
        }).render();

        this._renderForm();
    }

    _handleFresh(){
        this.setState({template: null});
    }

    _handleClick(buttonView, evt){
        this._dispatch('fetch.templates', {view: this, button: buttonView, evt: evt});
    }

    _renderForm(){
        
        TemplateForm.make(this.refs.group, {
            template: this._state.template,
            cartitem: this._state.cartitem,
        }).render();
    }
}