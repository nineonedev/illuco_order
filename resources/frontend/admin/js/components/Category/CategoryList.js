import Sortable from 'sortablejs';
import View from '../../core/View';
import CategoryItem from './CategoryItem';

export default class CategoryList extends View {

    _defineProps()
    {
        return {
            items: [],
        };
    }

    _defineState()
    {
        return {
            ...super._props
        }
    }

    _template()
    {
        console.log(this);
        
        return `
            <ol class="no-category-items"></ol>
        `;
    }

    getCurrentIndex()
    {
        return this._el.children.length || 0;
    }

    addCategory(category)
    {
        this.setState({items: [...this._state.items, category]});
    }


    removeCategory(id) 
    {
        const items = this._state.filter(item => +item.id !== +id); 
        this.setState({items: items});
    }

    _render()
    {
        super._render(); 
        
        this._children = [];

        for (const item of this._state.items) {
            const categoryItem = CategoryItem.make(this._id, {...item}).render();
            this.addChild(categoryItem); 
        }

        Sortable.create(this._el, {
            handle: '[data-action="drag"]',
            onEnd: (evt) => {
                const item  = this._state.items.find(item => item.sort_order === evt.oldIndex);
                if (item === null || item === undefined) return;

                this._dispatch('category.update', {id:item.id, data: new URLSearchParams({...item, sort_order: evt.newIndex})});
            }
        });
    }

}