import View from "../../core/View";
import Helper from "../../supports/Helper";

export default class CartItemSet extends View
{
    _defineProps(){
        return {};
    }

    _defineState(){
        return {
            ...this._props,
        }
    }

    _template(){
        const {product, quantity, cartitem} = this._state;

        const files = product?.template?.fileattachment;
        const fileImage = files
            ? files.find((file) => file.file_key === "main_image")
            : null;
        const mainImage = fileImage
            ? fileImage.upload_path
            : "/static/app/img/meta/thumb.jpg";

        const totalQuantity = cartitem.quantity * quantity;

        return `
            <li class="no-cart-subitem">
                <figure class="no-cart-subitem-img">
                <img src="${mainImage}" alt="${product.name}">
                </figure>
                <div class="no-cart-subitem-info">
                    <p class="no-text-sm"><em>${product.name}</em></p>
                    <!-- 
                    <p class="no-text-sm">코드: ${product.code}</p>
                    <p class="no-text-sm">모델명: ${product.model}</p> 
                    -->
                    <p class="no-text-sm">수량: ${totalQuantity}</p>
                    <p class="no-text-sm">단가: ${Helper.formatCurrency(product.price)}</p>
                    <p class="no-text-sm">총액: <em>${Helper.formatCurrency(product.price * totalQuantity)}</em></p>
                </div>
            </li>
        `;
    }
}