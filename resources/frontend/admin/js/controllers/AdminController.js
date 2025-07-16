
import Controller from "../core/Controller";
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";
import SelectInput from "../components/Inputs/SelectInput";
import FileInput from "../components/Inputs/FileInput";
import DateTimeInput from "../components/Inputs/DateTimeInput";
import EditorInput from "../components/Inputs/EditorInput";
import DateInput from "../components/Inputs/DateInput";

import {
    Chart,
    CategoryScale,
    LinearScale,
    BarController,
    BarElement,
    LineController,
    LineElement,
    PointElement,
    Tooltip,
    Legend,
} from "chart.js";

Chart.register(
    CategoryScale,
    LinearScale,
    BarController,
    BarElement,
    LineController,
    LineElement,
    PointElement,
    Tooltip,
    Legend
);


export default class AdminController extends Controller {
    form;
    cancelBtn;
    modal;
    loader;

    totalChart;
    illucoChart;
    dealerChart;

    _prepare() {
        this.form = document.getElementById("frm");
        this.cancelBtn = document.querySelector('[data-action="cancel"]');
        this.modal = Modal.make("portal").render();
        this.loader = Loader.make("portal").render();

        document.querySelectorAll('[data-view-type="select"]').forEach((el) => {
            SelectInput.make(el).render();
        });

        document.querySelectorAll('[data-view-type="editor"]').forEach((el) => {
            EditorInput.make(el).render();
        });

        document.querySelectorAll('[data-view-type="file"]').forEach((el) => {
            FileInput.make(el).render();
        });

        document
            .querySelectorAll('[data-view-type="datetime"]')
            .forEach((el) => {
                DateTimeInput.make(el).render();
            });

        document
            .querySelectorAll('[data-view-type="date"]')
            .forEach((el) => {
                DateInput.make(el).render();
            });
    }

    async dashboard() {
        this._prepare();

        const queryParams = Object.fromEntries(
            new URLSearchParams(window.location.search).entries()
        );

        this.loader.show();

        try {
            const aggregation = await this.fetchAggregation(queryParams);

            if (aggregation) {
                await this.renderSummary(aggregation);
                await this.renderTotalChart(aggregation);
                await this.renderIllucoChart(aggregation);
                await this.renderDealerChart(aggregation);
            }
        } finally {
            this.loader.hide();
        }
    }

    /**
     * aggregation API 호출(fetch)
     */
    async fetchAggregation(query) {
        const queryString = new URLSearchParams(query).toString();
        const response = await fetch(`/admin/dashboard/aggregation?${queryString}`, {
            method: "GET",
            headers: {
                Accept: "application/json",
            }
        });

        const json = await response.json();

        return json?.data?.aggregation ?? null;
    }

    /**
     * 요약 정보 렌더링
     */
    async renderSummary(aggr) {
        const totalEl = document.querySelector("#total-sales-amount");
        if (totalEl) {
            totalEl.textContent =
                `${aggr.total_sales.total_sales.toLocaleString()} USD`;
        }

        const illucoEl = document.querySelector("#illuco-sales-amount");
        if (illucoEl) {
            illucoEl.textContent =
                `${aggr.illuco_sales.total_sales.toLocaleString()} USD`;
        }

        const dealerTotal = aggr.dealer_sales.reduce(
            (sum, d) => sum + (d.total_sales || 0),
            0
        );

        const dealerEl = document.querySelector("#dealer-sales-amount");
        if (dealerEl) {
            dealerEl.textContent =
                `${dealerTotal.toLocaleString()} USD`;
        }
    }


    /**
     * 전체 매출 차트
     */
    async renderTotalChart(aggr) {
        const sales = aggr.total_sales;

        if (!sales) return;

        const labels = sales.months.map(m => `${m.month}월`);
        const data = sales.months.map(m => m.total_sales);

        const el = document.getElementById("sales-chart");
        this.totalChart = this.renderChart(
            el,
            this.totalChart,
            labels,
            data,
            "전체 매출 (USD)",
            "rgba(54, 162, 235, 0.5)"
        );
    }

    /**
     * 일루코 매출 차트
     */
    async renderIllucoChart(aggr) {
        const sales = aggr.illuco_sales;

        if (!sales) return;

        const labels = sales.months.map(m => `${m.month}월`);
        const data = sales.months.map(m => m.total_sales);

        const el = document.getElementById("illuco-chart");
        this.illucoChart = this.renderChart(
            el,
            this.illucoChart,
            labels,
            data,
            "본사(일루코) 매출 (USD)",
            "rgba(255, 99, 132, 0.5)"
        );
    }

    /**
     * 대리점 매출 차트
     */
    renderDealerChart(aggr) {
        const sales = aggr.dealer_sales;

        if (!sales) return;

        // 1~12월 라벨
        const labels = Array.from({ length: 12 }, (_, i) => `${i + 1}월`);

        // 대리점별 dataset
        const datasets = sales.map((dealer, i) => {
            // 월별 매출
            const data = Array(12).fill(0);
            dealer.months.forEach(m => {
                data[m.month - 1] = m.total_sales;
            });

            // 랜덤 컬러 생성 (혹은 고정 컬러 배열 사용 가능)
            const color = this.getColor(i);

            return {
                label: dealer.dealer_name,
                data,
                borderColor: color,
                backgroundColor: color,
                tension: 0.3,
                fill: false,
            };
        });

        const el = document.getElementById("dealer-chart");

        if (!el) {
            console.warn("Chart rendering skipped: #dealer-chart element not found.");
            return;
        }

        if (this.dealerChart) {
            this.dealerChart.destroy();
        }

        this.dealerChart = new Chart(el, {
            type: "line",
            data: {
                labels,
                datasets,
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: "매출 (USD)"
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: "월"
                        }
                    }
                }
            }
        });
    }

    getColor(index) {
        const colors = [];

        for (let i = 0; i < 100; i++) {
            const hue = Math.round((i * 360) / 100);
            colors.push(`hsla(${hue}, 70%, 60%, 0.5)`);
        }

        return colors[index % colors.length];
    }



    renderChart(ctx, chartInstance, labels, data, labelText, color, horizontal = false) {
        if (!ctx) {
            console.warn(`Chart rendering skipped: element not found.`);
            return null;
        }
        
        if (chartInstance) {
            chartInstance.data.labels = labels;
            chartInstance.data.datasets[0].data = data;
            chartInstance.update();
            return chartInstance;
        } else {
            return new Chart(ctx, {
                type: "bar",
                data: {
                    labels,
                    datasets: [{
                        label: labelText,
                        data,
                        backgroundColor: color,
                        borderWidth: 1,
                    }]
                },
                options: {
                    indexAxis: horizontal ? 'y' : 'x',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true },
                        x: { beginAtZero: true }
                    }
                }
            });
        }
    }



    signIn() {
        this._logger.info("signIn");
        this._prepare();

        this.form?.addEventListener("submit", this._signInHandler.bind(this));
    }

    async _signInHandler(e) {
        e.preventDefault();

        const t = e.target;
        const fd = new FormData(t);
        const action = t.action;
        const submitter = e.submitter;

        try {
            submitter.disabled = true;
            this.loader.show();

            const result = await this._ajax.post(action, fd, true);
            const { success, data } = result;

            if (success && data?.redirect) {
                location.href = data.redirect;
            }
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    signUp() {
        this._logger.info("signUp");
        this._prepare();

        this.form?.addEventListener("submit", this._signUpHandler.bind(this));
    }

    async _signUpHandler(e) {
        e.preventDefault();

        const t = e.target;
        const fd = new FormData(t);
        const action = t.action;
        const submitter = e.submitter;

        try {
            submitter.disabled = true;
            this.loader.show();

            const result = await this._ajax.post(action, fd, true);
            const { success } = result;

            if (success) {
                location.href = "/";
            }
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    create() {
        this._logger.info("create");
        this._prepare();

        this.form?.addEventListener("submit", this._createHandler.bind(this));
    }

    async _createHandler(e) {
        e.preventDefault();

        const t = e.target;
        const fd = new FormData(t);
        const action = t.action;
        const submitter = e.submitter;

        try {
            submitter.disabled = true;
            this.loader.show();

            const result = await this._ajax.post(action, fd, true);
            if (result?.success && this.cancelBtn) {
                this.cancelBtn.click();
            }
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    edit() {
        this._logger.info("edit");
        this._prepare();

        this.form?.addEventListener("submit", this._editHandler.bind(this));

        const deleteBtn = this.form?.querySelector('[data-action="delete"]');
        deleteBtn?.addEventListener("click", this._destroy.bind(this));
    }

    async _editHandler(e) {
        e.preventDefault();

        const t = e.target;
        const fd = new FormData(t);
        const action = t.action;
        const submitter = e.submitter;

        try {
            submitter.disabled = true;
            this.loader.show();

            const result = await this._ajax.put(action, fd, true);
            if (result?.success) {
                location.reload();
            }
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    async _destroy(e) {
        if (!confirm("정말로 삭제하시겠습니까?")) return;

        const submitter = e.currentTarget;
        const action = this.form?.action;
        const data = new FormData(this.form);
        data.set("_method", "delete");

        try {
            submitter.disabled = true;
            this.loader.show();

            const result = await this._ajax.delete(action, data);
            this._logger.success(result);

            if (result?.success) {
                this.cancelBtn?.click();
            }
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }
}
