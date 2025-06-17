import Controller from "../../modules/core/Controller";
import LanguageSelect from "../components/LanguageSelect";
import DateTime from "../components/DateTime";
import Select from "../components/Select";
import Form from "../components/Form";

export default class CustomerController extends Controller {
    index() {
        this._logger.info("index");
    }

    create() {
        this._logger.info("create");

        const languageSel = LanguageSelect.make("country-select-hook", {
            name: "country",
            label: "국가 선택",
            country: "kr",
        });
        languageSel.render();

        const agent = Select.make("agents");
        agent.render();

        const datetime = DateTime.make("datetime");
        datetime.render();

        const form = Form.make("frm", { onSubmit: this._store.bind(this) });
    }

    edit() {
        this._logger.info("edit");

        const form = Form.make("frm", { onSubmit: this._update.bind(this) });
        form.onMounted((form) => {
            const deleteBtn = form.qs('[data-action="delete"]');
            form.on(deleteBtn, "click", () =>
                form.dispatch("destroy", {
                    data: new FormData(form._el),
                    action: form._el.action,
                })
            );
        });
        form.render();

        const languageSel = LanguageSelect.make("country-select-hook", {
            name: "country",
            label: "국가 선택",
            country: "kr",
        });
        languageSel.render();

        const agent = Select.make("agents");
        agent.render();

        const datetime = DateTime.make("datetime");
        datetime.render();

        this._listen("destroy", this._destroy.bind(this));
    }

    async _store({ data, action }, evt) {
        evt.submitter.disabled = true;
        const result = await this._ajax.post(action, data);
        evt.submitter.disabled = false;

        this._loggee.success(result);
    }

    async _update(data, evt) {
        evt.submitter.disabled = true;
        const result = await this._ajax.put(action, data);
        evt.submitter.disabled = false;

        this._loggee.success(result);
    }

    async _destroy({ data, action }, evt) {
        if (!confirm("정말로 삭제하시겠습니까?")) return;

        const result = await this._ajax.delete(action, data);
        this._loggee.success(result);
    }
}
