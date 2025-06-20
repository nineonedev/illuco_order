import View from '../../core/View';
import Helper from '../../supports/Helper';
import CartItem from './CartItem';
import CartItemList from './CartItemList';
import SearchButton from './SearchButton';

export default class Cart extends View {
    _boot(){
        this._cartItemsHook = this._generateHookId();
        this._cartItemList = null
        super._boot();
    }

    _defineProps(){
        return {
            customer: {}
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
                    <h1 class="no-heading-sm">장바구니 (<em data-ref="count">0</em>)</h1>
                </div>

                <div data-ref="search"></div>

                ${this._renderCustomerZone()}

                <div id="${this._cartItemsHook}"></div>
            </div>
        `;
    }

    getCustomer(){
        if (Helper.isEmptyObject(this._state.customer)) {
            return null; 
        }
        
        return this._state.customer;
    }

    _render(){
        this._logger.success(this._state.customer);

        super._render();

        SearchButton.make(this.refs.search, {
            label: '고객 검색',
            onClick: this._handleClick.bind(this)
        }).render();


        this._cartItemList = CartItemList.make(this._cartItemsHook, {
            cartitems: this._state.customer.cart?.cartitems,
        }).render();
    }

    _handleClick(buttonView, evt){
        this._dispatch('fetch.customers', {cart: this, button: buttonView, evt: evt})
    }

    _renderCustomerZone(){
        const {customer} = this._state;
        
        if (Helper.isEmptyObject(customer)) {
            return `
                <p class="no-form-empty-fallback">선택된 고객이 없습니다.</p>
            `;
        }

        const {name, phone_number, country, email} = customer;

        return `
            <div>
                <h2>선택된 고객: ${name}</h2>
                <div>
                    <p>국가: ${country}</p>
                    <p>연락처: ${phone_number}</p>
                    <p>이메일: ${email}</p>
                </div>
            </div>
        `;
    }
}