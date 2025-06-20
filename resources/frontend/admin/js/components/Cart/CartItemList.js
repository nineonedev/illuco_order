import View from "../../core/View";
import CartItem from "./CartItem";

export default class CartItemList extends View {
   
    _defineProps(){
        return {
            cartitems: [],
        }
    }

    _defineState(){
        return {
            ...this._props
        }
    }

    hasItems(){
        return this._state.cartitems && this._state.cartitems.length > 0;
    }
    
    _template(){
        if (!this.hasItems()) {
            return `<div class="no-cart-empty">
                    <i class="fa-regular fa-cart-flatbed-empty"></i>
                    <p>No Selected Products</p>
                </div>`;
        }

        return `
            <ol class="no-cart-items"></ol>
       `;
    }


    _render(){
        super._render();

        if (!this.hasItems()) return; 
        
        this._children = [];

        for (const item of this._state.cartitems) {
            const cartItem = CartItem.make(this._id, {...item}).render();
            this.addChild(cartItem);     
        }
    }

}