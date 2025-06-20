import View from "../../core/View";

export default class CustomerSelection extends View {
    _defineProps(){
        return {
            paginator: {}
        }
    }

    _defineState(){
        return {
            ...this._props
        }
    }
    
    _template(){
        const {paginator } = this._state;
        const {data} = paginator;

        return `
            <table class="no-page-index-table">
                <thead>
                    <tr>
                        <th>이름</th>
                        <th>국가</th>
                        <th>이메일</th>
                        <th>연락처</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>${data.map(this._renderItem).join('')}</tbody>
            </table>
        `;
    }

    _renderItem({id, name, country, email, phone_number} = {}) {
        return `
            <tr>
                <td>
                    <span>${name}</span>
                </td>
                <td>
                    <span>${country}</span>
                </td>
                <td>
                    <span>${email}</span>
                </td>
                <td>
                    <span>${phone_number}</span>
                </td>
                <td>
                    <div class="no-prod-attr-list__action">
                        <button type="button" class="no-btn-primary-outline" data-row-id="${id}">
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
        })
    }

    _handleClick(form, evt){
        const id = evt.currentTarget.getAttribute('data-row-id');
        const customer = this._state.paginator.data.find(item => item.id === +id);
        this._dispatch('pick.customer', {customer: customer});
    }
}