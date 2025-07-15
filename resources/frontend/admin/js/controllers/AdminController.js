import Controller from "../core/Controller";
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";
import SelectInput from "../components/Inputs/SelectInput";
import FileInput from "../components/Inputs/FileInput";
import DateTimeInput from "../components/Inputs/DateTimeInput";
import EditorInput from "../components/Inputs/EditorInput";
import {
    Chart,
    CategoryScale,
    LinearScale,
    BarController,
    BarElement,
    Tooltip,
    Legend,
} from "chart.js";

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
    modal;
    loader;

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
    }

    dashboard() {
        this._logger.info("dashboard");
        this._prepare();

        // 매출 현황 차트 (대리점별 매출)
        const salesChartEl = document.getElementById("sales-chart");
        if (salesChartEl) {
            new Chart(salesChartEl, {
                type: "bar",
                data: {
                    labels: ["대리점A", "대리점B", "대리점C"],
                    datasets: [
                        {
                            label: "매출액 (USD)",
                            data: [25000, 18000, 32000],
                            backgroundColor: [
                                "rgba(54, 162, 235, 0.5)",
                                "rgba(255, 206, 86, 0.5)",
                                "rgba(75, 192, 192, 0.5)",
                            ],
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                },
            });
        }

        // 최근 오더 표
        const orders = [
            {
                orderNo: "ORD20240701",
                orderer: "김철수",
                dealer: "대리점A",
                date: "2024-07-01",
                amount: "$1200",
            },
            {
                orderNo: "ORD20240628",
                orderer: "박영희",
                dealer: "대리점B",
                date: "2024-06-28",
                amount: "$800",
            },
        ];

        const orderBody = document.getElementById("recent-orders-body");
        orders.forEach((order) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${order.orderNo}</td>
                <td>${order.orderer}</td>
                <td>${order.dealer}</td>
                <td>${order.date}</td>
                <td>${order.amount}</td>
            `;
            orderBody?.appendChild(tr);
        });

        // 공지사항 표
        const notices = [
            { title: "서버 점검 안내", author: "관리자", date: "2024-07-01" },
            { title: "신제품 출시", author: "관리자", date: "2024-06-28" },
        ];

        const noticesBody = document.getElementById("notices-body");
        notices.forEach((notice) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${notice.title}</td>
                <td>${notice.author}</td>
                <td>${notice.date}</td>
            `;
            noticesBody?.appendChild(tr);
        });
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
