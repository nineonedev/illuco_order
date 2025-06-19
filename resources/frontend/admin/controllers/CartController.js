import Ajax from "../../modules/core/Ajax";
import Controller from "../../modules/core/Controller";
import Select from "../components/Select";

export default class CartController extends Controller {
    customerInput;
    templateInput;

    index() {
        this._logger.info("index");

        this.customerInput = Select.make("customer_id", {
            onChange: this._fetchCustomer.bind(this),
        }).render();

        this.templateInput = Select.make("template_id", {
            onChange: this._fetchTemplate.bind(this),
        }).render();

        
    }

    async _fetchCustomer(id){
        if (!id) {
            return; 
        }
        
        const result = await new Ajax(true).get(`/admin/customers/${id}`);
        console.log(result);
    }   

    async _fetchTemplate(e){

    }

    async _store(e) {

    }

    async _update(e) {

    }

    async _destroy(action, data) {

    }
}
