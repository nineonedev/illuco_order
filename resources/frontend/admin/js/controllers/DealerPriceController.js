// resources/assets/js/controllers/DealerPriceController.js

import Controller from "../core/Controller";
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";
import Ajax from "../core/Ajax";
import SearchButton from "../components/Cart/SearchButton";
import TemplateSelection from "../components/Cart/TemplateSelection";

export default class DealerPriceController extends Controller {
    createForm; // 상단 추가 폼 (#create-price)
    rowForms = []; // 각 tr 폼
    modal;
    loader;

    // ✅ 행 선택용 임시 상태 (row별 템플릿 선택에 사용)
    _activeFormForPick = null;
    _activeRowButton = null;

    edit() {
        this._logger.info("dealer-price.edit");
        this._prepare();

        // 상단: 템플릿 검색 버튼
        this.templateSearchButton = SearchButton.make("template-hook", {
            label: "제품 검색",
            onClick: this.handleSearch.bind(this),
        });
        this.templateSearchButton.render();

        // ✅ 행: 각 tr 안의 search-button 초기화/바인딩
        const rowSearchBtns = document.querySelectorAll(
            'table.no-page-index-table tbody tr form button[data-view-type="search-button"]'
        );
        rowSearchBtns.forEach((btn) => {
            // 버튼 라벨 세팅 (data-view-props에 label이 들어옴)
            try {
                const props = JSON.parse(
                    btn.getAttribute("data-view-props") || "{}"
                );
                btn.innerHTML = `<span>${props.label || "제품 검색"}</span>`;
            } catch {
                btn.innerHTML = `<span>제품 검색</span>`;
            }
            // 스타일 보강(선택)
            btn.classList.add("no-btn-primary-outline");

            // 클릭 → 해당 행을 active로 설정하고 검색 모달 오픈
            btn.addEventListener("click", () => {
                this._activeFormForPick = btn.closest("form");
                this._activeRowButton = btn;
                this.handleSearch(); // 모달 오픈
            });
        });

        // 이벤트 리스너
        this._listen("pick.template", this._pickTemplate.bind(this));
        this._listen("fetch.templates", this._fetchAllTemplates.bind(this));

        // 상단 추가 폼
        this.createForm = document.getElementById("create-price");
        if (this.createForm) {
            this.createForm.addEventListener("submit", this._save.bind(this));
        }

        // 행 단위 폼
        this.rowForms = Array.from(
            document.querySelectorAll("table.no-page-index-table tbody tr form")
        );
        this.rowForms.forEach((f) =>
            f.addEventListener("submit", this._save.bind(this))
        );

        // 삭제 버튼(폼액션이 별도로 설정됨)
        document
            .querySelectorAll(
                'button[data-item-action="delete"], button[data-item-action="destroy"]'
            )
            .forEach((btn) => {
                btn.addEventListener("click", (e) => {
                    const msg =
                        btn.getAttribute("data-confirm") ||
                        "정말 삭제하시겠습니까?";
                    if (!confirm(msg)) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                });
            });

        // 단축키: Ctrl/Cmd + S → 포커스된 입력이 속한 폼 제출
        window.addEventListener("keydown", (e) => {
            const isMac = navigator.platform.toUpperCase().includes("MAC");
            if (
                (isMac ? e.metaKey : e.ctrlKey) &&
                e.key.toLowerCase() === "s"
            ) {
                e.preventDefault();
                const active = document.activeElement;
                const form =
                    active?.closest("form") ||
                    this.createForm ||
                    this.rowForms[0];
                if (form) form.requestSubmit?.();
            }
        });
    }

    _pickTemplate({ template }) {
        // ✅ 행 선택이 활성화되어 있으면 해당 행에 반영
        if (this._activeFormForPick) {
            const form = this._activeFormForPick;

            // hidden: product_template_id
            const idInput = form.querySelector(
                'input[name="product_template_id"]'
            );
            if (idInput) idInput.value = template.id;

            // 버튼 라벨 갱신
            if (this._activeRowButton) {
                this._activeRowButton.innerHTML = `<span>${template.name}</span>`;
            }

            // 셀 값(코드/모델/기본단가) 갱신 - 테이블 구조에 맞춰 갱신
            const row = form.closest("tr");
            if (row) {
                const codeCell = row.querySelector("td:nth-child(2)");
                const modelCell = row.querySelector("td:nth-child(3)");
                const basePriceCell = row.querySelector("td:nth-child(4)");
                if (codeCell) codeCell.textContent = template.code ?? "-";
                if (modelCell) modelCell.textContent = template.model ?? "-";
                if (basePriceCell) {
                    const bp =
                        typeof template.price !== "undefined" &&
                        template.price !== null
                            ? Number(template.price).toFixed(2)
                            : "-";
                    basePriceCell.textContent = bp;
                }
            }

            // 모달 닫기
            if (this.modal) {
                this.modal.setState({ open: false, header: "", content: "" });
            }

            // 상태 리셋
            this._activeFormForPick = null;
            this._activeRowButton = null;

            this._toast("제품 템플릿이 선택되었습니다.");
            return;
        }

        // ↘️ 그렇지 않으면 상단 폼에 반영 (기존 동작)
        this.templateSearchButton.setState({ label: template.name });

        const form = this.createForm || document.getElementById("create-price");
        if (!form || !template) return;

        const idInput = form.querySelector('input[name="product_template_id"]');
        if (idInput) idInput.value = template.id;

        const priceInput = form.querySelector('input[name="price"]');
        if (
            priceInput &&
            (priceInput.value === "" || priceInput.value == null)
        ) {
            if (typeof template.price !== "undefined") {
                priceInput.value = template.price;
            }
        }

        const imgEl = form.querySelector('[data-role="thumb"]');
        if (imgEl && Array.isArray(template.fileattachment)) {
            const main = template.fileattachment.find(
                (f) => f.file_key === "main_image"
            );
            if (main?.upload_path) {
                imgEl.src = main.upload_path;
                imgEl.classList.remove("hidden");
            }
        }

        if (this.modal) {
            this.modal.setState({ open: false, header: "", content: "" });
        }

        this._toast("제품 템플릿이 선택되었습니다.");
    }

    // 🔎 상단 검색 버튼 클릭 → 제품 템플릿 모달 오픈
    async handleSearch() {
        await this._fetchAllTemplates({}); // 초기 로드 (필터 없음)
    }

    // 제품 템플릿 목록 로드 & 모달 표시
    async _fetchAllTemplates({ button, query = null } = {}) {
        let url = `/admin/product-templates`;
        if (query) {
            url += `?${query}`;
        }

        try {
            if (button?.setState) button.setState({ disabled: true });
            this.loader.show();

            const result = await new Ajax(true).get(url);
            const { success, data } = result;
            if (!success) return;

            const { templates, categories, query: q } = data;

            if (this.modal.state.open) {
                this.modal.setState({ open: false, content: "", header: "" });
            }

            this.modal.setState({
                header: "제품 검색",
                content: TemplateSelection.make(
                    null,
                    { paginator: templates, categories, query: q },
                    false
                ),
                open: true,
            });
        } catch (err) {
            console.error(err);
            alert("제품 목록을 불러오는 중 오류가 발생했습니다.");
        } finally {
            if (button?.setState) button.setState({ disabled: false });
            this.loader.hide();
        }
    }

    async _save(e) {
        e.preventDefault();
        const form = e.target;
        const submitter = e.submitter || form.querySelector('[type="submit"]');

        const action = form.action;
        const method = (form.method || "post").toUpperCase();

        const fd = new FormData(form);

        // 유효성 검사 (템플릿/가격)
        const tplId = (fd.get("product_template_id") || "").toString().trim();
        const priceStr = (fd.get("price") || "").toString().trim();
        const priceNum = parseFloat(priceStr);

        if (!tplId) {
            alert("제품 템플릿을 선택해주세요.");
            (
                form.querySelector('button[data-view-type="search-button"]') ||
                form.querySelector('button[aria-controls="template-hook"]') ||
                form.querySelector("#template-hook")
            )?.focus?.();
            return;
        }

        const isZeroLike = /^0*(?:\.0+)?$/.test(priceStr); // "0", "0.0", "00.00" 등
        if (!priceStr || isNaN(priceNum) || isZeroLike || priceNum <= 0) {
            alert("가격을 0이 아닌 값으로 입력해주세요. (예: 1.00)");
            form.querySelector('input[name="price"]')?.focus();
            return;
        }

        // 체크박스 값 보정 (미체크 시 0)
        this._ensureBoolean(fd, "is_active");

        try {
            submitter && (submitter.disabled = true);
            this.loader.show();

            const res = await (method === "POST"
                ? this._ajax.post(action, fd, true)
                : this._ajax.request(method, action, fd, true));

            if (res.success) {
                this._toast(res.message || "정상적으로 처리되었습니다.");

                // 서버에서 최신 값이 내려오면 행 UI 일부 동기화
                if (res.data) this._hydrateRowAfterSave(form, res.data);

                // 삭제 성공 시 행 제거
                if (
                    submitter?.dataset?.itemAction === "delete" ||
                    submitter?.dataset?.itemAction === "destroy"
                ) {
                    const tr = form.closest("tr");
                    if (tr) tr.remove();
                }
            } else {
                alert(res.message || "처리에 실패했습니다.");
            }
        } catch (err) {
            console.error(err);
            alert("요청 처리 중 오류가 발생했습니다.");
        } finally {
            this.loader.hide();
            submitter && (submitter.disabled = false);
        }
    }

    _hydrateRowAfterSave(form, data) {
        // updated_at 반영
        const updatedCell = form
            .closest("tr")
            ?.querySelector("td:nth-last-child(2), [data-role='updated-at']");
        if (updatedCell && data.updated_at) {
            updatedCell.textContent =
                typeof data.updated_at === "string"
                    ? data.updated_at
                    : String(data.updated_at);
        }

        // 체크박스 반영
        const active = form.querySelector('input[name="is_active"]');
        if (active && typeof data.is_active !== "undefined") {
            active.checked = !!data.is_active;
        }

        // 가격 반영 (서버가 보낸 값 사용)
        const priceInput = form.querySelector('input[name="price"]');
        if (priceInput && typeof data.price !== "undefined") {
            priceInput.value = data.price;
        }
    }

    _ensureBoolean(fd, name) {
        const hasField = Array.from(fd.keys()).some((k) => k === name);
        if (!hasField) {
            fd.set(name, "0");
        }
    }

    _toast(message) {
        if (window?.noToast) {
            window.noToast.success(message);
            return;
        }
        const el = document.createElement("div");
        el.textContent = message;
        el.style.position = "fixed";
        el.style.left = "50%";
        el.style.top = "20px";
        el.style.transform = "translateX(-50%)";
        el.style.background = "rgba(25,25,25,.9)";
        el.style.color = "#fff";
        el.style.padding = "8px 12px";
        el.style.borderRadius = "6px";
        el.style.zIndex = "9999";
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 1800);
    }

    _prepare() {
        this.modal = Modal.make("portal").render();
        this.loader = Loader.make("portal").render();
        this._ajax = this._ajax || new Ajax();
    }
}
