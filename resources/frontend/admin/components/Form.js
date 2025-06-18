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
        this.on(this._el, 'submit', this._handleSubmit.bind(this));
    }

    _handleSubmit(e){
        e.preventDefault();

        if (this._props.onSubmit && typeof this._props.onSubmit === 'function') {
            this._props.onSubmit({
                data: new FormData(this._el),
                action: this._el.action
            }, e);
        }
    }

}
