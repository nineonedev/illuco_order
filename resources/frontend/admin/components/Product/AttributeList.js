import Component from "../../../Modules/Core/Component";
import AttributeItem from "./AttributeItem";

export default class AttributeList extends Component {
    _type = 'attribute-list';
    _listId = null;
    

    _defineProps(){
        return {
            attributes: [],
        }
    }

    _defineState(){
        return {
            ...this._props,
        }
    }

    _template(){
        return this._state.attributes.length ? this._renderTemplate() : this._renderFallback();
    }

    _renderTemplate(){
        this._listId = this._generateNodeId();

        return `
            <div class="no-prod-attr-list">
                <div class="no-page-index-table-outer">
                    <table class="no-page-index-table">
                        <thead>
                            <tr>
                                <th>순서</th>
                                <th>형태</th>
                                <th>이름</th>
                                <th>관리</th>
                            </tr>
                        </thead>
                        <tbody id="${this._listId}"></tbody>
                    </table>
                </div>
            </div>
        `;
    }

    _renderFallback(){
        return  `<p>등록된 속성이 없습니다.</p>`;
    }
    
    getCurrentSortOrder(){
        return this._state.attributes && this._state.attributes.length ? this._state.attributes.length + 1 : 0; 
    }

    _afterRender(){
        const attrs = this._state.attributes; 
        

        if (!attrs || attrs.length === 0 ) {
            return; 
        }
        
        attrs.forEach((attr, index) => {
            const item =  AttributeItem.make(this._listId, {
                ...attr,
            }).render();
        });
    }
}