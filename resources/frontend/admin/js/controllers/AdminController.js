import Controller from '../core/Controller';
import {
    Chart,
    CategoryScale,
    LinearScale,
    BarController,
    BarElement,
    Tooltip,
    Legend,
} from 'chart.js';

Chart.register(
    CategoryScale,
    LinearScale,
    BarController,
    BarElement,
    Tooltip,
    Legend
);

export default class AdminController extends Controller {
    form;
    cancelBtn;

    _prepare() {
        this.form = document.getElementById('frm');
        this.cancelBtn = document.querySelector('[data-action="cancel"]');
    }

    dashboard() {
        this._logger.info("dashboard");

        // 매출 현황 차트 (대리점별 매출)
        const salesChartEl = document.getElementById('sales-chart');
        if (salesChartEl) {
            new Chart(salesChartEl, {
                type: 'bar',
                data: {
                    labels: ['대리점A', '대리점B', '대리점C'],
                    datasets: [{
                        label: '매출액 (USD)',
                        data: [25000, 18000, 32000],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(255, 206, 86, 0.5)',
                            'rgba(75, 192, 192, 0.5)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // 최근 오더 표
        const orders = [
            { orderNo: 'ORD20240701', orderer: '김철수', dealer: '대리점A', date: '2024-07-01', amount: '$1200' },
            { orderNo: 'ORD20240628', orderer: '박영희', dealer: '대리점B', date: '2024-06-28', amount: '$800' },
        ];

        const orderBody = document.getElementById('recent-orders-body');
        orders.forEach(order => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${order.orderNo}</td>
                <td>${order.orderer}</td>
                <td>${order.dealer}</td>
                <td>${order.date}</td>
                <td>${order.amount}</td>
            `;
            orderBody.appendChild(tr);
        });

        // 공지사항 표
        const notices = [
            { title: '서버 점검 안내', author: '관리자', date: '2024-07-01' },
            { title: '신제품 출시', author: '관리자', date: '2024-06-28' },
        ];

        const noticesBody = document.getElementById('notices-body');
        notices.forEach(notice => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${notice.title}</td>
                <td>${notice.author}</td>
                <td>${notice.date}</td>
            `;
            noticesBody.appendChild(tr);
        });
    }



    signIn() {
        this._logger.info("signIn");
        this._prepare();

        this.form.addEventListener('submit', async (e) => {
            const result = await this._process(e, (data, action) => this._ajax.post(action, data, true));
            const {success, data} = result; 

            if (success && data.redirect){
                location.href = data.redirect;
            }
        });
    }

    signUp() {
        this._logger.info("signUp");
        this._prepare();

        this.form.addEventListener('submit', async (e) => {
            const result = await this._process(e, (data, action) => this._ajax.post(action, data, true))
            const {success, data} = result; 
            
            if (success) {
                location.href = '/';
            }
        });
    }

    create() {
        this._logger.info("create");
        this._prepare();

        this.form.addEventListener('submit', async (e) => {
            const result = await this._process(e, (data, action) => this._ajax.post(action, data, true))
            if (result?.success && this.cancelBtn) {
                this.cancelBtn.click();
            }

        });
    }

    edit() {
        this._logger.info("edit");
        this._prepare();

        this.form.addEventListener('submit', async (e) => {
            
            const result = await this._process(e, (data, action) => this._ajax.put(action, data, true))
            if (result?.success) {
                location.reload();
            }
            
        });

        const deleteBtn = this.form.querySelector('[data-action="delete"]');
        deleteBtn?.addEventListener('click', () => {
            const data = new FormData(this.form);
            data.set('_method', 'delete');
            this._destroy(this.form.action, data);
        });
    }

    async _destroy(action, data) {
        if (!confirm("정말로 삭제하시겠습니까?")) return;

        const result = await this._ajax.delete(action, data);
        this._logger.success(result);

        if (result.success) {
            this.cancelBtn?.click();
        }
    }
}
