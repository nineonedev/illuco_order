import View from '../../core/View';

export default class CategoryItem extends View {
    _defineProps()
    {
        return {
            id: null,
            slug: '',
            label: '',
        }
    }

    _defineState()
    {
        return {
            ...this._props,
        }
    }

    _template()
    {
        const {label, slug} = this._state;
        return `
            <li class="no-category-item" data-node="root">
                <div class="no-category-item-block">
                    <div class="no-category-item-head">
                        <div class="no-category-item-head-move">
                            <button type="button" data-action="drag" class="no-btn-icon --narrow --xs">
                                <i class="fa-regular fa-grip-dots-vertical"></i>
                            </button>
                        </div>
                        <div class="no-category-item-head-text">
                            <span>${label}</span>
                        </div>
                    </div>
                    <div class="no-category-item-action">
                        <button type="button" data-ref="edit" class="no-btn-icon --xs">
                            <i class="fa-light fa-pen-to-square"></i>
                        </button>
                        <button type="button" data-ref="delete" class="no-btn-icon --xs">
                            <i class="fa-light fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </li>
        `;
    }

    _bindEvents()
    {
        this.on(this.refs.edit, 'click', this._handleEdit.bind(this));
        this.on(this.refs.delete, 'click', this._handleDelete.bind(this));
    }

    _handleEdit(){
        
    }

    _handleDelete()
    {
        this._dispatch('category.delete', {id: this._state.id});
    }
}