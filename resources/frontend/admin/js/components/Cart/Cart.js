
import View from '../../core/View';
import Button from '../../shared/Button';
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
                    <div class="no-page-head__between">
                        <h2 class="no-heading-sm">장바구니 (<em data-ref="count">0</em>)</h2>
                        <div data-ref="fresh"></div>
                    </div>
                </div>

                <div data-ref="search"></div>


                ${this._renderCustomerZone()}

                <div id="${this._cartItemsHook}"></div>
            </div>
        `;
    }

    hasCustomer(){
        return this._state.customer && !Helper.isEmptyObject(this._state.customer);
    }

    getCustomer(){
        if (!this.hasCustomer()) {
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

        Button.make(this.refs.fresh, {
            label: '초기화', 
            className: 'no-btn-success-outline --xs',
            onClick: this._handleFresh.bind(this),
        }).render();
        
        this._cartItemList = CartItemList.make(this._cartItemsHook, {
            cartitems: this._getCartItemsFromCustomer(),
        }).render();

        this._updateCartCount();
    }

    _getCartItemsFromCustomer(){
        return this._state.customer?.cart?.cartitems || [];
    }

    _handleFresh(){
        this.setState({customer: null});
    }

    _updateCartCount(){
        this.refs.count.textContent = this._cartItemList.cartItems().length;
    }

    setCartItems(cartitems = []){
        this._cartItemList.setState({cartitems: cartitems});
        this._updateCartCount();
    }

    updateOrderStatus(){
        this._cartItemList.updateOrderStatus();
    }

    addCartItem(cartitem){
        this._cartItemList.addCartItem(cartitem);
        this._updateCartCount();
    }

    removeCartItem(id){
        this._cartItemList.removeCartItem(id);
        this._updateCartCount();
    }

    updateCartItem(id, cartitem = {}){
        this._cartItemList.updateCartItem(id, cartitem);
    }

    _handleClick(buttonView, evt){
        this._dispatch('fetch.customers', {cart: this, button: buttonView, evt: evt})
    }

    _renderCustomerZone(){
        if (!this.hasCustomer()) {
            return `
                <p class="no-form-empty-fallback">선택된 고객이 없습니다.</p>
            `;
        }
        
        const {name, phone_number, country, email} = this._state.customer;

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