import View from "../../core/View";
import Button from "../../shared/Button";
import Helper from "../../supports/Helper";
import InputFactory from "../Inputs/InputFactroy";

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
            template: {}
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
        const totalPrice = Helper.formatCurrency(price * (quantity ?? 1)); 

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
                        <div class="no-cart-aggregation">
                            <dl>
                                <dt>단가</dt>
                                <dd><span data-ref="price">${formattedPrice}</span></dd>
                            </dl>
                            <dl>
                                <dt>수량</dt>
                                <dd><span data-ref="quantity">${quantity ?? 1}</span></dd>
                            </dl>
                            <dl>
                                <dt>
                                    <span>총 제품 가격</span>
                                </dt>
                                <dd><b data-ref="total">${totalPrice}</b></dd>
                            </dl>
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
        const {template} = this._state;
        
        if (Helper.isEmptyObject(template)) return; 
        const {attributes} = template; 

        if (attributes && attributes.length > 0) {

            for (const attr of attributes) {
                const input = InputFactory
                    .make(attr.type)
                    .make(this._attrHookId, {
                        ...attr, 
                        name: `attributes[${attr.id}]`
                    }).render();

                this._inputs.push(input);
            }
        }
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

            this._dispatch('add.cart', {
                data: fd, 
                view: this, 
                button: this._submitBtn
            });
        });
    }
}
