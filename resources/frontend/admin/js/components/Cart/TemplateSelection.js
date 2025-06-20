import View from "../../core/View";

export default class TemplateSelection extends View {
    _defineProps(){
        return {
            paginator: {} // { data: [{ id, name, code, category, ... }] }
        };
    }

    _defineState(){
        return {
            ...this._props
        };
    }

    _template(){
        const { paginator } = this._state;
        const { data } = paginator;

        return `
            <table class="no-page-index-table">
                <thead>
                    <tr>
                        <th>이미지</th>
                        <th>제품명</th>
                        <th>모델명</th>
                        <th>코드</th>
                        <th>단가(USD)</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.map(this._renderItem).join('')}
                </tbody>
            </table>
        `;
    }

    _renderItem({ id, fileattachment, name, model, code, price } = {}) {
        const mainImage = fileattachment.find(file => file.file_key === 'main_image');
        
        return `
            <tr>
                <td>
                    ${mainImage ? `
                        <div class="no-page-index-table__thumb">
                            <img width="80" src="${mainImage.upload_path}" alt="${model || name}"/>
                        </div>
                    ` : `-` }
                </td>
                <td><span>${name}</span></td>
                <td><span>${model || '-'}</span></td>
                <td><span>${code || '-'}</span></td>
                <td><span>${price || '-'}</span></td>
                <td>
                    <div class="no-prod-attr-list__action">
                        <button 
                            type="button" 
                            class="no-btn-primary-outline" 
                            data-row-id="${id}">
                            <span>선택</span>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    _bindEvents(){
        const buttons = this.qsAll('button');

        buttons.forEach(btn => {
            this.on(btn, 'click', this._handleClick.bind(this));
        });
    }

    _handleClick(form, evt){
        const id = evt.currentTarget.getAttribute('data-row-id');
        const template = this._state.paginator.data.find(item => item.id === +id);
        this._dispatch('pick.template', { template });
    }
}
