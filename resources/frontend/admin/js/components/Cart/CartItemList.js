import View from "../../core/View";
import Button from "../../shared/Button";
import CheckboxInput from "../Inputs/CheckboxInput";
import CartItem from "./CartItem";

export default class CartItemList extends View {
    _boot() {
        this._listHookId = this._generateHookId();
        this._aggtHookId = this._generateHookId();
        this._orderButton = null;
        super._boot();
    }

    _defineProps() {
        return {
            cartitems: [],
            onUpdateCart: () => {}
        };
    }

    _defineState() {
        return {
            ...this._props
        };
    }



    cartItems() {
        return this._state.cartitems || [];
    }

    hasItems() {
        return this._state.cartitems && this._state.cartitems.length > 0;
    }

    _template() {
        if (!this.hasItems()) {
            return `<div class="no-cart-empty">
                    <i class="fa-regular fa-cart-flatbed-empty"></i>
                    <p>No Selected Products</p>
                </div>`;
        }

        return `
            <div>
                <div class="no-cart-items-selection">
                    <div data-ref="check"></div>
                    <div data-ref="deleteBtn"></div>
                </div>
                <ol id="${this._listHookId}" class="no-cart-items"></ol>
                <div id="${this._aggtHookId}" class="no-cart-to-order"></div>
            </div>
        `;
    }

    _render() {
        super._render();

        if (!this.hasItems()) return;

        this._children = [];
        this._deleteBtn = null;
        this._checkbox = null;

        for (const item of this._state.cartitems) {
            this.addCartItem(item);
        }

        // 전체 선택 체크박스
        this._checkbox = CheckboxInput.make(this.refs.check, {
            label: '전체선택',
            checked: this.getCheckedIds().length === this._children.length,
            size: 'md',
            spacing: false,
            onChange: this._checkAllItems.bind(this)
        }).render();

        // 선택 삭제 버튼
        this._deleteBtn = Button.make(this.refs.deleteBtn, {
            label: '선택삭제',
            type: 'button',
            className: 'no-btn-error-outline --xxs',
            onClick: this._handleDeleteManyItems.bind(this)
        }).render();

        this._updateOrderStatus();
    }

    // 선택된 항목의 ID 목록 반환
    getCheckedIds() {
        return this._children.filter(child => child.state.itemChecked).map(child => child.state.id);
    }

    // 전체 선택/해제
    _checkAllItems({ value }) {
        this._children.forEach(child => child.check(value));
        this._updateOrderStatus();
    }

    // 선택 삭제 처리
    _handleDeleteManyItems() {
        const ids = this.getCheckedIds();

        if (ids.length === 0) {
            alert('선택된 제품이 없습니다. 제품을 선택해주세요.');
            return;
        }

        this._dispatch('delete.cartitems', { ids, view: this, button: this._deleteBtn});
    }

    // 개별 삭제
    _handleUpdate({ id, data, view, button}) {
        this._dispatch('update.cartitem', { id, data, view, button });
    }

    _handleDelete({ id, view, button }) {
        this._dispatch('delete.cartitem', { id, view, button });
    }

    addCartItem(cartitem) {
        const cartItem = CartItem.make(this._listHookId, {
            ...cartitem,
            onCheck: this._handleCheck.bind(this),
            onDelete: this._handleDelete.bind(this),
            onUpdate: this._handleUpdate.bind(this)
        }).render();

        this.addChild(cartItem);
        this._props.onUpdateCart();
    }

    removeCartItem(id) {
        const itemIdx = this._children.findIndex(item => +item.props.id === +id);

        if (itemIdx === -1) {
            this._logger.error('No found cartitem with id : ' + id);
            return;
        }

        // UI에서만 제거하고, 상태는 관리
        this._children[itemIdx].destroy();
        this._children.splice(itemIdx, 1);

        this.setState({
            cartitems: this._state.cartitems.filter(item => +item.id !== +id)
        }, false);
        
        // 업데이트된 상태 반영
        this._props.onUpdateCart();

        if (this._children.length === 0) {
            this.setState({ cartitems: [] });
        } else {
            this._updateOrderStatus(); // 갯수 바뀌었으면 업데이트 필요
        }
    }

    updateCartItem(id, cartitem = {}) {
        const item = this._children.find(child => +child.state.id === +id);

        if (!item) {
            this._logger.error(`updateCartItem: item not found for id ${id}`);
            return;
        }

        item.setState({ ...cartitem });
    }

    // 주문 처리
    _handleOrder() {
        const count = this.getCheckedIds().length;

        if (count === 0) {
            alert('선택된 제품이 없습니다. 제품을 선택해주세요.');
            this._orderButton.setState({ disabled: true });
            return;
        }

        console.log('Ordering...', this.getCheckedIds());
    }

    _handleCheck({ value, view }) {
        const id = view.state.id;

        if (value) {
            this._checkedCartItemIds.push(id);
        } else {
            this._checkedCartItemIds = this._checkedCartItemIds.filter(itemId => +itemId !== +id);
        }

        this._updateOrderStatus();
    }

    _updateCheckedItemIds() {
        this._checkedCartItemIds = this._children.filter(child => child.state.itemChecked).map(child => child.state.id);
        this._checkbox.setState({ checked: this._checkedCartItemIds.length === this._children.length });
    }

    // 선택 상태 업데이트
    _updateOrderStatus() {
        this._updateCheckedItemIds();
        this._updateOrderButton();
    }

    updateOrderStatus() {
        this._updateOrderStatus();
    }

    // 주문 버튼 업데이트
    _updateOrderButton() {
        const checkedCount = this.getCheckedIds().length;
        const label = checkedCount > 0
            ? `총 ${checkedCount}개 제품 주문하기`
            : `제품을 선택해주세요.`;

        if (!this._orderButton) {
            this._orderButton = Button.make(this._aggtHookId, {
                type: 'button',
                disabled: checkedCount === 0,
                label: label,
                className: 'no-btn-primary --lg',
                onClick: this._handleOrder.bind(this),
            }).render();

            return;
        }

        this._orderButton.setState({ disabled: checkedCount === 0, label: label });
    }
}
