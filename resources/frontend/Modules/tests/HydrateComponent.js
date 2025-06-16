import Component from "../core/Component";

export default class HydrateComponent extends Component {
    constructor(hookId, props) {
        super(hookId, props);
    }

    /** override */
    _render() {
        if (!this._el) {
            const element = this._hostEl.firstElementChild;
            if (!element) {
                throw new Error(`Hydration failed: no content found in #${this._hookId}`);
            }

            this._el = element;
        } else {
            const cloneEl = this._el.cloneNode(true);
            this._el.replaceWith(cloneEl);
            this._el = cloneEl;
        }
        
        this._logger.success(`Hydrate Rendered`);
        this._fireBindingCallbacks();
    }
}
