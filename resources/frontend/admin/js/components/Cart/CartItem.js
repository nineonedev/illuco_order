import View from "../../core/View";
import Button from "../../shared/Button";
import Helper from "../../supports/Helper";
import InputFactory from "../Inputs/InputFactroy";

export default class CartItem extends View {
    _boot(){
        this._aggtHookId = this._generateHookId();
        this._checkHookId = this._generateHookId();
        this._deleteBtnHookId = this._generateHookId();
        
        this._checkbox = null;
        this._counter = null;
        this._deleteBtn = null;
        this._checkedIds = [];

        super._boot();
    }

    _defineProps(){
        return {
            id:null,
            cart_id: null,
            product_id: null,
            quantity: null,
            product: null,
            onCheck: () => {},
            onDelete: () => {},
            onUpdate: () => {},
            itemChecked: false,
        }
    }

    _defineState(){
        return {
            ...this._props
        }
    }
    
    _template() {
        const { product, quantity } = this._state;

        const files = product?.template?.fileattachment;
        const fileImage = files ? files.find(file => file.file_key === 'main_image') : null;
        const mainImage = fileImage ? fileImage.upload_path : '/static/app/img/meta/thumb.jpg'; 
        
        const price = product.price ?? 0;
        const formattedPrice = Helper.formatCurrency(price * quantity);

        return `
            <li class="no-cart-item">
                <div class="no-cart-item-head">
                    <div id="${this._checkHookId}"></div>
                    <div id="${this._deleteBtnHookId}"></div>
                </div>

                <div class="no-cart-item-present">
                    <div class="no-cart-item-present-block">
                        <div class="no-cart-item-present__img">
                            <figure>
                                <img src="${mainImage}" alt="${product.name}" />
                            </figure>
                        </div>
                        <div class="no-cart-item-present-detail">
                            <div class="no-cart-item-present__info">
                                <p class="no-text-sm">코드: ${product.code ?? '-'}</p>
                                <p class="no-text-sm">모델명: ${product.model ?? '-'}</p>
                            </div>
                            <div class="no-cart-item-present__price">
                                <p>가격: <b data-ref="price">${formattedPrice}</b></p>
                            </div>
                        </div>
                    </div>
                    <div id="${this._aggtHookId}"></div>
                </div>
            </li>
        `;
    }


    _render(){
        super._render();

        const { product, quantity } = this._state;

        const attributes = product?.template?.attributes ?? [];
        const optionMap = JSON.parse(product?.option_json ?? '{}');
        const optionText = attributes.map(attr => {
            const value = optionMap[attr.id];

            if (Array.isArray(value)) {
                // 예: 다중 선택형
                const labels = attr.options
                    .filter(opt => value.includes(opt.value))
                    .map(opt => opt.label)
                    .join(', ');
                    
                return `${attr.label}: ${labels ?? '-'}`;
            } else {
                return `${attr.label}: ${value ?? '-'}`;
            }
        }).join(', ') ?? '-';
        

        this._checkbox = InputFactory
            .make('checkbox')
            .make(this._checkHookId, {
                name: 'id',
                label: product.name,
                size: 'md',
                helperText: optionText,
                spacing: false,
                value: this._state.id,
                checked: this._state.itemChecked, 
                onChange: this._handleChange.bind(this),
            }).render();

        this._deleteBtn = Button.make(this._deleteBtnHookId, {
            className: 'no-btn-move --md',
            ariaLabel: '아이템 제거',
            children: `<i class="fa-regular fa-xmark"></i>`,
            type: 'button',
            onClick: this._handleDelete.bind(this)
        }).render();

        this._counter = InputFactory
            .make('counter')
            .make(this._aggtHookId, {
            name: 'quantity',
            value: quantity,
            onChange: this._handlePrice.bind(this)
        }).render();
    }

    _handleDelete(){
        this._props.onDelete({id: this._props.id, view: this, button: this._deleteBtn});
    }
    
    _handlePrice({value, view}, evt){
        const id = this._state.id;

        const fd = new URLSearchParams({
            quantity: value,
            id: id
        });

        this._props.onUpdate({id: id, data: fd, view: this, button: this._counter});
    }

    _handleChange({value, view}){
        this.check(value, false);
        this._props.onCheck({value, view: this});
    }

    check(checked = true, shouldRender = true){
        this.setState({itemChecked: checked}, shouldRender);
    }
}