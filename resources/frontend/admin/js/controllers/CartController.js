import Ajax from "../core/Ajax";
import Controller from "../core/Controller";
import TemplateSelection from "../components/Cart/TemplateSelection";
import CustomerSelection from "../components/Cart/CustomerSelection";
import Cart from "../components/Cart/Cart";
import Template from "../components/Cart/Template";
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";

export default class CartController extends Controller {
    form;
    modal;
    cart;

    static attributes = {};

    async index() {
        this._logger.info("index");

        this.modal = Modal.make("portal").render();
        this.loader = Loader.make("portal").render();
        this.cart = Cart.make("cart-hook").render();
        this.form = Template.make("template-hook").render();

        await this._loadAllData();

        this._listen("fetch.customers", this._fetchAllCustomers.bind(this));
        this._listen("fetch.templates", this._fetchAllTemplates.bind(this));

        this._listen("pick.customer", this._pickCustomer.bind(this));
        this._listen("pick.template", this._pickTemplate.bind(this));

        this._listen("add.cart", this._addToCart.bind(this));
        this._listen("update.cartitem", this._updateCartItem.bind(this));
        this._listen("update.cartitems", this._updateManyCartItems.bind(this));
        this._listen("edit.cartitem", this._editCartItem.bind(this));

        this._listen("delete.cartitem", this._deleteCartItem.bind(this));
        this._listen("delete.cartitems", this._deleteManyCartItems.bind(this));

        this._listen("order.create", this._createOrder.bind(this));
    }

    async _loadAllData() {
        this.loader.show();

        try {
            const [attrResult] = await Promise.all([
                new Ajax(true).get("/admin/product-attributes"),
            ]);

            CartController.attributes = attrResult.data;
            this._logger.success("속성 로드 결과", attrResult.data);
        } catch (err) {
            alert(err.message);
        } finally {
            this.loader.hide();
        }
    }

    async _createOrder({ ids, totalAmount, memo, button }) {
        this._logger.info(ids, button);

        const customer = this.cart.getCustomer();

        const orderData = new URLSearchParams({
            ids: ids,
            customer_id: customer?.id,
            total_amount: totalAmount,
            memo: memo,
        });

        try {
            button.setState({ disabled: true });
            this.loader.show();
            const result = await Ajax.make(false).post(
                "/admin/orders",
                orderData
            );
            this._logger.success(result);

            this.cart.setCartItems(result.data.cartitems || []);
        } catch (err) {
            this._logger.error(err);
            alert("주문 처리 중 문제가 발생하였습니다.");
        } finally {
            this.loader.hide();
            button.setState({ disabled: false });
        }
    }

    async _deleteManyCartItems({ ids, views, button }) {
        this._logger.info("ids", ids, views);

        try {
            button.setState({ disabled: true });
            this.loader.show();
            const result = await Ajax.make(true).delete(
                "/admin/cartitems",
                new URLSearchParams({ ids })
            );
            this._logger.success(result);

            for (const id of ids) {
                this.cart.removeCartItem(id);
            }
        } catch (err) {
            this._logger.error(err);
            alert("삭제 처리 중 문제가 발생하였습니다.");
        } finally {
            this.loader.hide();
            button.setState({ disabled: false });
        }
    }

    async _deleteCartItem({ id, view, button }) {
        this._logger.info(id);

        try {
            button.setState({ disabled: true });
            this.loader.show();

            const result = await Ajax.make(true).delete(
                `/admin/cartitems/${id}`
            );
            this._logger.success("장바구니 삭제", result);
            this.cart.removeCartItem(id);
        } catch (err) {
            this._logger.error(err);

            alert("삭제 처리 중 문제가 발생하였습니다.");
        } finally {
            this.loader.hide();
            button.setState({ disabled: false });
        }
    }

    async _editCartItem({ id, view, button }) {
        this._logger.info(id, view, button);

        button.setState({ disabled: true });
        this.loader.show();

        try {
            const result = await new Ajax(true).get(`/admin/cartitems/${id}`);
            this._logger.success(result);

            const cartitem = result.data.cartitem;
            console.log(cartitem);
            
            this._pickTemplate({ template: cartitem.product.template, cartitem: cartitem});

        } catch (err) {
            // 에러 처리
            this._logger.error(err);
            alert("제품 정보를 불러오는 중 문제가 발생했습니다.");
        } finally {
            // 로딩 스피너 숨기기
            this.loader.hide();
            button.setState({ disabled: false });
        }
    }

    async _updateManyCartItems({ data, view }) {
        this._logger.info(Object.fromEntries(data));

        try {
            this.loader.show();
            const result = await new Ajax(true).put(
                `/admin/cartitems`,
                data
            );
            this._logger.success("장바구니 변경", result);

            const cartitems = result.data.cartitems;

            console.log(cartitems);
            
            
            cartitems.forEach(item => {
                this.cart.updateCartItem(item.id, {
                    ...item,
                });
            });
            
        } finally {
            this.loader.hide();
        }
    } 

    async _updateCartItem({ id, data, view, button }) {
        this._logger.info(id, data);

        try {
            if (button) {
                button.setState({ disabled: true });
            }
            this.loader.show();
            const result = await new Ajax(true).put(
                `/admin/cartitems/${id}`,
                data
            );
            this._logger.success("장바구니 변경", result);

            const cartitem = result.data.cartitem;
            this.cart.updateCartItem(cartitem.id, {
                ...view.state,
                ...cartitem,
            });
        } finally {
            this.loader.hide();

            if (button) {
                button.setState({ disabled: false });
            }
        }
    }

    async _addToCart({ data, button }, evt) {
        this._logger.info(Object.fromEntries(data), button);

        const customer = this.cart.getCustomer();

        if (!customer) {
            alert("고객을 선택해주세요.");
            return;
        }

        data.append("customer_id", customer.id);

        try {
            button.setState({ disabled: true });
            this.loader.show();

            const result = await new Ajax(false).post("/admin/cart", data);
            this._logger.success("장바구니 추가", result.data.cartitem);
            this.cart.addCartItem(result.data.cartitem);
            
        } finally {
            this.loader.hide();
            button.setState({ disabled: false });
        }
    }

    async _pickTemplate({ template, cartitem = {} }, evt) {
        this.form.setState({ template, cartitem});
        this.modal.setState({
            open: false,
            content: "",
            header: "",
        });
    }

    async _pickCustomer({ customer }, evt) {
        this.cart.setState({ customer: customer });
        this.modal.setState({
            open: false,
            content: "",
            header: "",
        });
    }

    async _fetchAllCustomers({ button }, evt) {
        try {
            button.setState({ disabled: true });
            const result = await new Ajax(true).get(`/admin/customers`);

            if (!result.success) {
                return;
            }

            const { customers } = result.data;

            this.modal.setState({
                header: button.props.label,
                content: CustomerSelection.make(
                    null,
                    { paginator: customers },
                    false
                ),
                open: true,
            });
        } finally {
            button.setState({ disabled: false });
        }
    }

    async _fetchAllTemplates({ button }, evt) {
        try {
            button.setState({ disabled: true });
            const result = await new Ajax(true).get(`/admin/product-templates`);
            const { success, data } = result;

            if (!success) return;

            const { templates } = data;

            this.modal.setState({
                header: button.props.label,
                content: TemplateSelection.make(
                    null,
                    { paginator: templates },
                    false
                ),
                open: true,
            });
        } finally {
            button.setState({ disabled: false });
        }
    }

    async _store(e) {}

    async _update(e) {}

    async _destroy(action, data) {}
}
