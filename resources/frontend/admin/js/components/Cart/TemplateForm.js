import View from "../../core/View";
import Button from "../../shared/Button";
import Helper from "../../supports/Helper";
import InputFactory from "../Inputs/InputFactroy";
import LoupeForm from "./LoupeForm";

export default class TemplateForm extends View {
    _boot(){
        this._inputs = [];
        this._tempHookId = this._generateHookId();
        this._attrHookId = this._generateHookId();
        this._aggtHookId = this._generateHookId();
        this._submitHookId = this._generateHookId();
        super._boot();

    }

    _defineProps() {
        return {
            template: {},
            values: [],
        };
    }

    _defineState(){
        return {
            ...this._props
        }
    }

    hasTemplate(){
        return this._state.template && !Helper.isEmptyObject(this._state.template);
    }

    _template() {
        if (!this.hasTemplate()) {
            return `
                <div class="no-form-empty-fallback">
                    <p>선택된 제품이 없습니다. 제품을 선택해주세요.</p>
                </div>
            `;
        }

        const {template, quantity} = this._state;
        const {id, price} = template
        const formattedPrice = Helper.formatCurrency(price);
        const totalPrice = price * (quantity ?? 1)
        const formattedTotalPrice = Helper.formatCurrency(totalPrice);

        const orderItemPrice = totalPrice;
        const totalOrderItemPrice = Helper.formatCurrency(orderItemPrice);

        return `
            <div>
                <form method="post" data-ref="form"  enctype="multipart/form-data">
                    <input type="hidden" name="product[template_id]" value="${id}"/>
                    <hr class="no-hr --xl">

                    <fieldset class="no-form-section">
                        <legend class="no-form-section__title">제품 정보</legend>
                        <div class="no-form-group" id="${this._tempHookId}"></div>
                    </fieldset>

                    <hr class="no-hr --xl">
                    
                    <fieldset class="no-form-section">
                        <legend class="no-form-section__title">속성 정보</legend>
                        <div class="no-form-group" id="${this._attrHookId}"></div>
                    </fieldset>
                    
                    <hr class="no-hr --xl">

                    <fieldset class="no-form-section">
                        <legend class="no-form-section__title">집계 정보</legend>
                        <div class="no-summary-table-inner">
                            <table class="no-summary-table">
                                <thead>
                                    <tr>
                                        <th>품목</th>
                                        <th>단가</th>
                                        <th>수량</th>
                                        <th>소계</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>${template.name}</td>
                                        <td>${formattedPrice}</td>
                                        <td>${quantity ?? 1}</td>
                                        <td>${formattedTotalPrice}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">총 제품 가격</th>
                                        <td class="no-price-total">${totalOrderItemPrice}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </fieldset>
                    
                    <div class="no-form-action" id="${this._submitHookId}"></div>
                </form>
            </div>
        `;
    }

    _render(){
        super._render();
        
        if (!this.hasTemplate()) return; 

        this._submitBtn = null;
        this._productForm = null;
        
        this._renderTemplate();
        this._renderAttributes();
        this._renderAggregate();
    }

    _renderTemplate(){
        this._inputs = [];
        const {template, quantity} = this._state;

        if (Helper.isEmptyObject(template)) return; 

        const { 
            code, 
            description,
            fileattachment,
            price,
            model,
            name
        } = template;

        const nameInput = InputFactory.make('text').make(this._tempHookId,{
            label: '이름',
            name: 'product[name]',
            value: name,
            readOnly: true,
        }).render();

        const textInput = InputFactory.make('text').make(this._tempHookId,{
            label: '코드',
            name: 'product[code]',
            value: code,
            readOnly: true,
        }).render();

        const modelInput = InputFactory.make('text').make(this._tempHookId,{
            label: '모델명',
            name: 'product[model]',
            value: model,
            readOnly: true,
        }).render();
        
        const priceInput = InputFactory.make('number').make(this._tempHookId,{
            label: '단가(USD)',
            name: 'product[price]',
            value: price,
            readOnly: true,
        }).render();

        const descriptionInput = InputFactory.make('longText').make(this._tempHookId,{
            label: '설명',
            name: 'product[description]',
            rows: '4',
            value: description,
            readOnly: true,
        }).render();
        
        const counterInput = InputFactory.make('counter').make(this._tempHookId, {
            label: '발주 수량',
            name: 'quantity',
            value: quantity ?? 1,
            onChange: this._handlePrice.bind(this)
        }).render();

        this._inputs.push(
            nameInput, 
            textInput, 
            modelInput, 
            priceInput, 
            descriptionInput, 
            counterInput
        );
        
    }

    _renderAttributes(){
        // const {category} = this._state.template;


        // switch(category.slug) {
        //     case 'loupe' :
        //         this._renderLoupe();
        //         break;
        // }
        this._productForm = this._renderLoupe();
    }

    _renderLoupe(){
        this._logger.success('loupe');

        LoupeForm.make(this._attrHookId, {
            type: 'ready-made',
        }).render();

        // const hook = document.getElementById(this._attrHookId);
    }

    async _fetchLoupeInfo()
    {
        const model = this._state.template.model;
    }

    _renderAggregate(){
       
        this._submitBtn = Button.make(this._submitHookId, {
            className: 'no-btn-primary --sm',
            label: '장바구니에 추가',
        }).render();
    }

    _handlePrice({value, view}, e){
        const total = this._state.template.price * value;
        this.refs.quantity.textContent = Number.parseInt(value);
        this.refs.total.textContent = Helper.formatCurrency(total);
    }

    _bindEvents(){
        if (!this.hasTemplate()) return;

       this.on(this.refs.form, 'submit', (view, e) => {
            e.preventDefault(); 

            const fd = new FormData(e.target); 
            

            if (this._productForm && !this._productForm.isValid()) {
                console.log('fail to validation...');
                return; 
            } else {
                console.log('success to validation...');
                return; 
            }

            this._dispatch('add.cart', {
                data: fd, 
                view: this, 
                button: this._submitBtn
            });
        });
    }
}
