import View from "../../core/View";
import Button from "../../shared/Button";
import Helper from "../../supports/Helper";
import CheckboxInput from "../Inputs/CheckboxInput";
import LongTextInput from "../Inputs/LongTextInput";
import CartItem from "./CartItem";

export default class CartItemList extends View {
    _boot() {
        this._listHookId = this._generateHookId();
        this._aggtHookId = this._generateHookId();
        super._boot();
    }

    _defineProps() {
        return {
            cartitems: [],
            total_maount: 0,
            memo: '',
        };
    }

    _defineState() {
        return {
            ...this._props
        };
    }


    _defineComputed(){
        return {
            total: () => this._computed.mainTotal() + this._computed.setTotal(),
            mainTotal: () => this._children
                .filter(c => c.state.is_main_item)
                .reduce((acc, cur) => acc + (cur.state.product.price * cur.state.quantity), 0),

            setTotal: () => this._children
                .filter(c => c.state.is_main_item)
                .reduce((acc, cur) => {
                    if (!cur.state.sets) {
                        return acc; 
                    }

                    const qty = cur.state.quantity; 
                    return acc + (cur.state.sets.reduce((accSet, curSet) => {
                        return accSet + (curSet.product.price * curSet.quantity);
                    }, 0) * qty);
                }, 0),
        }
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

                <hr class="no-hr --xl">

                <div data-ref="memo"></div>

                <hr class="no-hr --xl">

                <fieldset class="no-form-section">
                    <legend class="no-form-section__title">집계 정보</legend>
                    <div class="no-cart-aggregation">
                        <dl>
                            <dt>메인 제품 합계</dt>
                            <dd><span data-ref="mainTotal">$0</span></dd>
                        </dl>
                        <dl>
                            <dt>세트 제품 합계</dt>
                            <dd><span data-ref="setTotal">$0</span></dd>
                        </dl>
                        <dl class="--total">
                            <dt>총 주문 예상 금액</dt>
                            <dd><b data-ref="total">$0</b></dd>
                        </dl>
                    </div>
                </fieldset>


                <hr class="no-hr --xl">
                
                <div id="${this._aggtHookId}" class="no-cart-to-order"></div>
            </div>
        `;
    }

    _render() {
        this._children = [];
        this._deleteBtn = null;
        this._checkbox = null;
        this._orderButton = null;
        this._memo = null;

        super._render();
        if (!this.hasItems()) return;

        for (const item of this._state.cartitems) {
            this.renderCartItem(item);
        }

        this._memo = LongTextInput.make(this.refs.memo, {
            label: '메모', 
            value: this._state.memo,
            rows: 8,
        }).render();

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
        return this._children.filter(child => child.state.selected).map(child => child.state.id);
    }

    // 전체 선택/해제
    _checkAllItems({ value }) {
        const fd = new FormData();

        this._children.forEach((child, index) => {
            const data = {...child.getData(), selected: value};
            for (const [key, val] of Object.entries(data)) {
                fd.append(`items[${index}][${key}]`, val);
            }
        });

        this._dispatch('update.cartitems', { data: fd, view: this});
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
        this.updateOrderStatus();
    }

    _handleDelete({ id, view, button }) {
        this._dispatch('delete.cartitem', { id, view, button });
        this.updateOrderStatus();
    }

    _handleEdit({id, view, button}){
        this._dispatch('edit.cartitem', { id, view, button });
        this.updateOrderStatus();
    }

    addCartItem(cartitem) {
        this.setState({cartitems: [...this._children.map(c => c.state), cartitem]});
    }

    renderCartItem(cartitem){
        const cartItem = CartItem.make(this._listHookId, {
            ...cartitem,
            onCheck: this._handleCheck.bind(this),
            onDelete: this._handleDelete.bind(this),
            onEdit: this._handleEdit.bind(this),
            onUpdate: this._handleUpdate.bind(this)
        }).render();

        this.addChild(cartItem);
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
            cartitems: this._children.map(c => c.state),
        });
    }

    updateCartItem(id, cartitem = {}) {
        const item = this._children.find(child => +child.state.id === +id);

        if (!item) {
            this._logger.error(`updateCartItem: item not found for id ${id}`);
            return;
        }

        item.setState({ ...cartitem });
        this._updateOrderStatus();
    }

    // 주문 처리
    _handleOrder() {
        const count = this.getCheckedIds().length;

        if (count === 0) {
            alert('선택된 제품이 없습니다. 제품을 선택해주세요.');
            this._orderButton.setState({ disabled: true });
            return;
        }


        this._dispatch('order.create', {
            ids: this.getCheckedIds(),
            button: this._orderButton,
            memo: this._memo.state.value,
            totalAmount: this._computed.total()
        });
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
        this._checkedCartItemIds = this._children.filter(child => child.state.selected).map(child => child.state.id);
        this._checkbox.setState({ checked: this._checkedCartItemIds.length === this._children.length });
    }

    // 선택 상태 업데이트
    _updateOrderStatus() {
        this._updateCheckedItemIds();
        this._updateOrderButton();
        this._updateAggregation();
    }

    _updateAggregation(){
        const mainTotal = this._computed.mainTotal();
        const setTotal = this._computed.setTotal();
        const total = this._computed.total();

        this.setState({ total }, false);

        this.refs.mainTotal.textContent = Helper.formatCurrency(mainTotal);
        this.refs.setTotal.textContent = Helper.formatCurrency(setTotal);
        this.refs.total.textContent = Helper.formatCurrency(total);
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
