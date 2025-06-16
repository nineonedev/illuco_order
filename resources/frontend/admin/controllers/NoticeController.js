// import Form from '../../modules/components/Form';
import Controller from "../../modules/core/Controller";
import File from "../components/File";
import Form from "../components/Form";
import LongText from "../components/LongText";

export default class NoticeController extends Controller {
    index() {
        this._logger.info("index");
    }

    create() {
        this._logger.info("create");

        Form.make("frm");

        this._listen("store", this._store.bind(this));
    }

    edit() {
        this._logger.info("edit");
    }

    async _store({ data, action }, evt) {
        const resData = await this._ajax.post(action, data);
        this._logger.info(resData);
    }
}
