import Component from "../../modules/core/Component";

export default class Form extends Component {
    _boot() {
        this._type = "form";
        super._boot();
    }

    _defineProps() {
        return {
            onSubmit: () => {}
        }
    }

    _bindEvents() {
        this.on(this._el, 'submit', this._props.onSubmit.bind(this));
    }
}
