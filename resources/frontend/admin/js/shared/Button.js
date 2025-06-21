import View from "../core/View";

export default class Button extends View {
    _defineProps(){
        return {
            label: '버튼',
            type: 'submit',
            className: '',
            disabled: false,
            children: "",
            onClick: () => {},
        }
    }

    _defineState(){
        return {
            ...this._props
        }
    }
    
    _template(){
        const {label, disabled, type, className, children} = this._state;

        let content; 

        if (children && label) {
            const template = document.createElement('template'); 
            template.innerHTML = children; 
            const element = template.content.firstChild;
            element.innerHTML = `<span>${label}</span>`;
            content = element.innerHTML;

        } else {
            content = children ? children : `<span>${label}</span>`;
        }

        return `
            <button type="${type}" class="${className}" ${disabled ? 'disabled' : ''}>
                ${content}
            </button>
        `;
    }

    _bindEvents(){
        this.on(this._el, 'click', this._props.onClick);
    }
}