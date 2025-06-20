import Ajax from "../core/Ajax";
import Controller from "../core/Controller";
import Modal from "../components/Supports/Modal";
import TemplateSelection from "../components/Cart/TemplateSelection";
import CustomerSelection from "../components/Cart/CustomerSelection";
import Cart from "../components/Cart/Cart";
import Template from "../components/Cart/Template";

export default class CartController extends Controller {
    form;
    modal;
    cart;

    index() {
        this._logger.info("index");
    
        this.modal = Modal.make('portal').render();
        this.cart = Cart.make('cart-hook').render();
        this.form = Template.make('template-hook').render();

        this._listen('fetch.customers', this._fetchAllCustomers.bind(this));
        this._listen('fetch.templates', this._fetchAllTemplates.bind(this));
        this._listen('pick.customer', this._pickCustomer.bind(this));
        this._listen('pick.template', this._pickTemplate.bind(this));
        this._listen('add.cart', this._addToCart.bind(this))
    }

    async _addToCart({data, submitter}, evt){

        const customer = this.cart.getCustomer(); 

        if (!customer) {
            alert('고객을 선택해주세요.'); 
            return;
        }

        data.append('customer_id', customer.id);
            
        try {
            submitter.disabled = true; 
            const result = await new Ajax(false).post('/admin/cart', data);
            if (result.success) {
                console.log(result);

            }
        } finally {
            submitter.disabled = false; 
        }

    }

    async _pickTemplate({template}, evt){
        this.form.setState({template: template});
        this.modal.setState({
            open: false, 
            content: '',
            header: ''
        });
    }

    async _pickCustomer({customer}, evt){
        this.cart.setState({customer: customer});
        this.modal.setState({
            open: false, 
            content: '',
            header: ''
        });
    }

     async _fetchAllCustomers({button}, evt){
        
        try {
            button.setState({disabled: true});
            const result = await new Ajax(true).get(`/admin/customers`);

            if (!result.success) {
                return;
            }

            const {customers} = result.data;

            this.modal.setState({
                header: button.props.label,
                content: CustomerSelection.make(null, {paginator: customers}, false),
                open: true,
            })
            
        } finally {
            button.setState({disabled: false});
        }

    }   

    async _fetchAllTemplates({button}, evt){
        try {
            button.setState({disabled: true});
            const result = await new Ajax(true).get(`/admin/product-templates`);
            const {success, data} = result;

            if (!success) return;

            const {templates} = data; 
            
            this.modal.setState({
                header: button.props.label,
                content: TemplateSelection.make(null, {paginator: templates}, false),
                open: true,
            });

        } finally {
            button.setState({disabled: false});
        }

    }


    async _store(e) {

    }

    async _update(e) {

    }

    async _destroy(action, data) {

    }
}
