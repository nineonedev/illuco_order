import Component from "../../Modules/Core/Component";
import File from "./File";
import LongText from "./LongText";

export default class Form extends Component {
    _boot() {
        this._type = "form";
        super._boot();
    }

    _defineProps() {
        return {
            action: "",
            method: "post",
        };
    }

    _template() {
        return this._hydrated ? this._snapshot : this._hostEl.innerHTML;
    }

    _bindEvents() {
        this.on(this._hostEl, "submit", this._handleSubmit.bind(this));

        LongText.make("content", { name: "content" });
        File.make("file-hook");
    }

    _handleSubmit(e) {
        e.preventDefault();
        const t = e.target;
        this._dispatch("store", {
            data: new FormData(t),
            target: t,
            action: t.action,
        });
    }
}
