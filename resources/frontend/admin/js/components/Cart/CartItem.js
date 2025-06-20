import View from "../../core/View";
import InputFactory from "../Inputs/InputFactroy";

export default class CartItem extends View {
    _boot(){
        this._aggtHookId = this._generateHookId();
        super._boot();
    }

    _defineProps(){
        return {
            id:null,
            cart_id: null,
            product_id: null,
            quantity: null,
            product: null,
        }
    }

    _defineState(){
        return {
            ...this._props
        }
    }
    
    _template() {
        const { product, quantity } = this._state;
        console.log(this._state);
        
        const model = product?.model ?? '-';
        const price = parseInt(product?.price ?? 0).toLocaleString();
        const image = product?.template?.image ?? '/static/default.png';

        // 옵션 문자열 구성
        const optionMap = JSON.parse(product?.option_json ?? '{}');
        const attributes = product?.template?.attributes ?? [];

        const optionText = attributes.map(attr => {
            const value = optionMap[attr.id];
            if (Array.isArray(value)) {
                // 예: 다중 선택형
                const labels = attr.options
                    .filter(opt => value.includes(opt.value))
                    .map(opt => opt.label)
                    .join(', ');
                return `${attr.label} - ${labels}`;
            } else {
                return `${attr.label} - ${value}`;
            }
        }).join(', ');

        return `
            <li class="no-cart-item">
                <div class="no-cart-item-head">
                    <strong class="no-heading-sm">${product.name}</strong>
                    <button type="button" class="no-btn-error-outline --xs">
                        <span>삭제</span>
                    </button>
                </div>

                <div class="no-cart-item-present">
                    <div class="no-cart-item-present-block">
                        <div class="no-cart-item-present__img">
                            <figure>
                                <img src="${image}" alt="제품 이미지" />
                            </figure>
                        </div>

                        <div class="no-cart-item-present__info">
                            <p class="no-text-sm">모델명: ${model}</p>
                            <p class="no-text-sm">옵션: ${optionText}</p>
                        </div>
                    </div>

                    <div class="no-cart-item-present__price">
                        <b>₩${price}</b>
                    </div>

                    <div id="${this._aggtHookId}"></div>
                </div>
            </li>
        `;
    }


    _render(){
        super._render();

        const input = InputFactory.make('counter', {
            name: 'quantity',
        }).make(this._aggtHookId, {
            onChange: this._handlePrice.bind(this)
        }).render();
    }

    _handlePrice({value, view}, evt){
        console.log(value);
    }
}