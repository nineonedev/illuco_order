import Component from "../core/Component"

export default class Form extends Component {
    _boot(){
        // this._debug = false;
    }

    _bindEvents(){
        this._on(this._el, 'submit', this._handleSubmit.bind(this)); 
    }

    _handleSubmit(e){
        e.preventDefault(); 

        const t = e.target;
        this._dispatch('store', {
            data: new FormData(t), 
            target: t, 
            action: t.action
        });
    }
}