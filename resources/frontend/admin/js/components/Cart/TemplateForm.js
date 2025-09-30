import CartController from "../../controllers/CartController";
import OrderOriginalController from "../../controllers/OrderOriginalController";
import View from "../../core/View";
import Button from "../../shared/Button";
import Helper from "../../supports/Helper";
import ProductZone from "../Claim/ProductZone";
import InputFactory from "../Inputs/InputFactroy";
import HeadlightForm from "./HeadlightForm";
import LoupeForm from "./LoupeForm";
import SummaryTable from "./SummaryTable";

export default class TemplateForm extends View {
    _boot() {
        this._inputs = [];
        this._tempHookId = this._generateHookId();
        this._attrHookId = this._generateHookId();
        this._aggtHookId = this._generateHookId();
        this._submitHookId = this._generateHookId();
        this._summaryHookId = this._generateHookId();
        this._productForm = null;
        this._summaryTable = null;
        this._counterInput = null;
        this._typeInput = null;

        super._boot();
    }


    _defineProps() {
        return {
            template: {},
            cartitem: {},
            useCart: true, 
            useWrapper: true,
        };
    }

    _defineState() {
        return {
            ...this._props,
        };
    }

    hasTemplate() {
        return (
            this._state.template && !Helper.isEmptyObject(this._state.template)
        );
    }

    _template() {
        if (!this.hasTemplate()) {
            return `
                <div class="no-form-empty-fallback">
                    <p>선택된 제품이 없습니다. 제품을 선택해주세요.</p>
                </div>
            `;
        }

        const { template, quantity, useWrapper } = this._state;
        const { id, price } = template;

        return `
            <div>
                ${useWrapper ? `<form method="post" data-ref="form"  enctype="multipart/form-data">` : ''}
                    <input type="hidden" name="product[template_id]" value="${id}"/>
                    <input type="type" name="product[type]" value="" />
                    <hr class="no-hr --xl">

                    <fieldset class="no-form-section">
                        <legend class="no-form-section__title">제품 정보</legend>
                        <div id="product-zone"></div>
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
                        <div id="${this._summaryHookId}"></div>
                    </fieldset>
                    
                    <div class="no-form-action" id="${this._submitHookId}"></div>
                ${useWrapper ? `</form>` : ''}
            </div>
        `;
    }

    _render() {
        super._render();

        if (!this.hasTemplate()) return;

        this._submitBtn = null;
        this._productForm = null;
        this._summaryTable = null;
        this._counterInput = null;
        this._typeInput = null;

        this._renderTemplate();
        this._renderAggregate();
        this._renderSubProduct();
    }

    _renderTemplate() {
        this._inputs = [];
        const { template, quantity } = this._state;

        this.productZone = ProductZone.make('product-zone', {
            template: template || {},
        }).render();

        if (Helper.isEmptyObject(template)) return;

        const counterInput = InputFactory.make("counter")
            .make(this._tempHookId, {
                label: "발주 수량",
                name: "quantity",
                value: quantity ?? 1,
                onChange: this._handlePrice.bind(this),
            })
            .render();

        this._counterInput = counterInput;

        this._typeInput = InputFactory.make("text")
            .make(this._tempHookId, {
                type: "hidden",
                value: "",
                name: "product[type]",
            })
            .render();

        this._inputs.push(
            counterInput
        );
    }

    _getInfo(){
        const contollerClass = this._state.useCart ? CartController : OrderOriginalController; 
        const info = contollerClass.findSubProductByModel(this._state.template.model);
        return info;
    }

    _renderSubProduct() {
        const cartItem = this._state.cartitem;

        const info = this._getInfo();

        this._logger.success(info);
        if (!info) return;

        const attributes = info.attributes || null; 
        let productData = null;
        let sets = [];
        
        if (!Helper.isEmptyObject(cartItem)) {
            productData = cartItem.product[cartItem.product.type] || {};
            sets = cartItem.sets;
        }

        const data = {
            template: this._state.template,
            sets: sets,
            onUpdateSets: this.updateSets.bind(this),
            onChangeQuantity: this._handleQuantity.bind(this),
            attributes: attributes,
        }

        if (productData) {
            Object.assign(data, {product: productData});
        }
        
        switch (info.category) {
            case 'loupe': 
                this._productForm = LoupeForm.make(this._attrHookId, {...data, onFetchLens: this._fetchLens.bind(this)}).render();
                    break; 
            case 'headlight':
                this._productForm = HeadlightForm.make(this._attrHookId, data).render();
        }

        this._typeInput.setState({value: info.category});
    }

    _fetchLens(){
        const controllerClass = this._state.useCart ? CartController : OrderOriginalController;
        return controllerClass.findSetGroupItemByModel(LoupeForm.PRECISON_LENS);
    }

    _handleQuantity(count, disabled = false) {
        this._counterInput.setState({value: count, disabled});
        this._handlePrice({value: count});
    }

    _renderAggregate() {
        const { template, quantity, cartitem } = this._state;

        const items = [
            {
                name: template.name,
                price: template.price,
                quantity: quantity ?? 1,
                subTotal: template.price * (quantity ?? 1),
            },
        ];

        if (!Helper.isEmptyObject(cartitem)) {
            cartitem.sets.forEach(set => {
                items.push(set);
            });
        }

        this._summaryTable = SummaryTable.make(this._summaryHookId, {
            labels: ["품목", "단가", "수량", "소계"],
            items: items,
        }).render();

        this._submitBtn = Button.make(this._submitHookId, {
            className: "no-btn-primary --sm",
            label: this._state.useCart ? '장바구니에 추가' : '제품 추가',
        }).render();
    }

    updateSets(sets = []) {
        const item = {
            name: this._state.template.name,
            price: this._state.template.price,
            quantity: this._counterInput.getValue(),
            subTotal:
                this._state.template.price * this._counterInput.getValue(),
        };

        this._summaryTable.setState({ items: [item, ...sets] });
    }
    
    _handlePrice({ value, view }, e) {
        const qty = Number.parseInt(value);

        const item = {
            name: this._state.template.name,
            price: this._state.template.price,
            quantity: qty,
            subTotal: this._state.template.price * qty,
        };

        const sets = this._productForm ? this._productForm.state.sets : [];

        this._summaryTable.setState({
            items: [item, ...sets],
        });
    }

    _bindEvents() {
        if (!this.hasTemplate()) return;

        const useWrapper = this._state.useWrapper;

        const form = useWrapper ? this.refs.form : this._el.closest('form');

        this.on(form, "submit", (view, e) => {
            e.preventDefault();

            const fd = new FormData(e.target);

            if (this._productForm) {
                this._productForm.validateAllFields();

                if (this._productForm.hasErrors()) {
                    console.log("fail to validation...");
                    return;
                }
            }

            console.log("success to validation...");

            this._dispatch(useWrapper ? 'add.cart' : 'add.original', {
                data: fd,
                view: this,
                button: this._submitBtn,
            });
        });
    }
}
