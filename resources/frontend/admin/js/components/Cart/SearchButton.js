import View from "../../core/View";

export default class SearchButton extends View {
    _defineProps(){
        return {
            label: '검색',
            disabled: false,
            onClick: () => {},
        }
    }

    _defineState(){
        return {
            ...this._props
        }
    }
    
    _template(){
        const {label, disabled, spacing} = this._state;
        return `
            <button type="button" class="no-form-search-btn" ${disabled ? 'disabled' : ''}>
                <span>${label}</span>
                <i class="fa-light fa-magnifying-glass"></i>
            </button>
        `;
    }

    _bindEvents(){
        this.on(this._el, 'click', this._props.onClick);
    }
}