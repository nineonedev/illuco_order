import View from "../../core/View";
import Helper from "../../supports/Helper";

export default class SummaryTable extends View {
    _defineProps() {
        return {
            labels: ["품목", "단가", "수량", "소계"],
            items: [], // [{ name, price, quantity, subTotal }]
            renderItem: null,
        };
    }

    _defineState(){
        return {
            ...this._props,
        }
    }

    _renderItem(item) {
        return `
            <tr>
                <td>${item.name ?? '-'}</td>
                <td>${Helper.formatCurrency(item.price ?? 0)}</td>
                <td>${item.quantity ?? 1}</td>
                <td>${Helper.formatCurrency(item.subTotal ?? 0)}</td>
            </tr>
        `;
    }

    _template() {
        const {labels, items} = this._state;
        
        // thead
        const headers = labels.map(label => `<th>${label}</th>`).join("");
        

        // tbody rows
        const renderItem = this._state.renderItem || this._renderItem.bind(this);
        const rows = items.map(renderItem).join("");

        // total
        const total = items.reduce((acc, cur) => cur.subTotal + acc, 0);

        return `
            <div class="no-summary-table-inner">
                <table class="no-summary-table">
                    <thead>
                        <tr>
                            ${headers}
                        </tr>
                    </thead>
                    <tbody>
                        ${rows}
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="${labels.length - 1}">총 제품 가격</th>
                            <td class="no-price-total">${Helper.formatCurrency(total)}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        `;
    }
}
