// resources/assets/js/controllers/DealerPriceController.js

import Controller from "../core/Controller";
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";
import Ajax from "../core/Ajax";
import SearchButton from "../components/Cart/SearchButton";
import TemplateSelection from "../components/Cart/TemplateSelection";

export default class DealerPriceController extends Controller {
    createForm; // 상단 추가 폼 (#create-price)
    rows = []; // 숨김 폼들: form[data-price-form]
    modal;
    loader;

    static TYPE_EDIT = "edit";
    static TYPE_CREATE = "create";

    // ✅ 임시 상태
    _activeFormForPick = null; // 현재 템플릿 선택을 적용할 <form>
    _activeButton = null; // 현재 클릭된 SearchButton 인스턴스
    _activeButtonEl = null; // (폴백) 실제 버튼 엘리먼트
    _activeType = DealerPriceController.TYPE_CREATE;

    edit() {
        this._logger.info("dealer-price.edit");
        this._prepare();

        // 상단 추가 폼
        this.createForm = document.getElementById("create-price");
        if (this.createForm) {
            this.createForm.addEventListener("submit", this._save.bind(this));
        }

        // 상단: 템플릿 검색 버튼 (SearchButton 인스턴스 보관)
        let topInstance;
        topInstance = SearchButton.make("template-hook", {
            label: "제품 검색",
            onClick: () => {
                this._activeType = DealerPriceController.TYPE_CREATE;
                this._activeFormForPick = this.createForm;
                this._activeButton = topInstance;
                this._activeButtonEl = document.querySelector(
                    '#template-hook [data-view-type="search-button"], #template-hook button'
                );
                this.handleSearch();
            },
        });
        this.templateSearchButton = topInstance;
        topInstance.render();

        // 이벤트 리스너
        this._listen("pick.template", this._pickTemplate.bind(this));
        this._listen("fetch.templates", this._fetchAllTemplates.bind(this));

        // 행 단위 숨김 폼 수집 + 각 행의 검색 버튼을 SearchButton으로 초기화
        this.rows = Array.from(document.querySelectorAll("tr[data-row-id]"));

        this.rows.forEach((row) => {
            const priceId = +row.dataset.rowId;

            const updateBtn = row.querySelector("button[data-action=update]");
            const destroyBtn = row.querySelector("button[data-action=destroy]");

            const priceEl = row.querySelector(`input[name=price]`);
            const activeEl = row.querySelector(`input[name=is_active]`);

            updateBtn?.addEventListener("click", async (e) => {
                const payload = new URLSearchParams({
                    id: priceId,
                    price: priceEl ? priceEl.value : null,
                    is_active: activeEl ? (activeEl.checked ? 1 : 0) : 0,
                });

                await this._handleUpdate(e, payload);
            });
            destroyBtn?.addEventListener("click", async (e) => {
                await this._hanldeDestroy(e);
            });
        });
    }

    async _handleUpdate(e, payload = {}) {
        const t = e.currentTarget;
        const priceId = +t.dataset.rowFor;

        const result = await new Ajax(true).put(
            `/admin/dealer-price/${priceId}`,
            payload
        );

        const { success, data, message } = result;
        alert(message);

        if (!success) {
            return;
        }

        location.reload();
    }

    async _hanldeDestroy(e, payload = {}) {
        console.log("[delete]", e);
        const t = e.currentTarget;
        const priceId = t.dataset.rowFor;

        const msg = "정말 삭제하시겠습니까?";

        if (!confirm(msg)) {
            e.preventDefault();
            e.stopPropagation();
            return;
        }

        const result = await new Ajax(true).delete(
            `/admin/dealer-price/${priceId}`
        );

        const { success, data, message } = result;
        alert(message);

        if (!success) {
            return;
        }

        location.reload();
    }

    _pickTemplate({ template }) {
        const form = this._activeFormForPick;
        if (!form || !template) return;

        // 1) product_template_id (숨김 폼 내부에 존재)
        const idInput = this._queryLinkedInput(
            form,
            "product_template_id",
            true
        );
        if (idInput) idInput.value = template.id;

        // 2) price (행에서는 form="f-.."로 연결되어 있으므로 링크 조회)
        const priceInput = this._queryLinkedInput(form, "price");
        if (priceInput) {
            // 비어있으면 템플릿 가격을 기본 세팅
            if (priceInput.value === "" || priceInput.value == null) {
                if (template.price != null && template.price !== "") {
                    priceInput.value = String(template.price);
                }
            }
        }

        // 3) is_active 체크 (기본 활성화)
        const activeInput = this._queryLinkedInput(form, "is_active");
        if (activeInput && activeInput.type === "checkbox") {
            activeInput.checked = true;
        }

        // 4) 버튼 라벨 갱신 (우선순위: 인스턴스 → 엘리먼트)
        if (
            this._activeButton &&
            typeof this._activeButton.setState === "function"
        ) {
            this._activeButton.setState({ label: `${template.name} - ${template.model}` });
        }

        if (this._activeButtonEl) {
            this._activeButtonEl.setAttribute(
                "data-template-id",
                String(template.id)
            );
        }

        // 모달 닫기
        if (this.modal) {
            this.modal.setState({ open: false, header: "", content: "" });
        }

        // 상태 리셋
        this._activeFormForPick = null;
        this._activeButton = null;
        this._activeButtonEl = null;
        this._activeType = DealerPriceController.TYPE_CREATE;

        this._toast("제품 템플릿이 선택되었습니다.");
    }

    // 🔎 검색 모달 오픈
    async handleSearch(activeType, activeForm) {
        if (activeType) this._activeType = activeType;
        if (activeForm) this._activeFormForPick = activeForm;
        await this._fetchAllTemplates({});
    }

    // 기존 함수 교체
    _buildExceptsParam() {
        const ids = [];

        (this.rows || []).forEach((f) => {
            const pid = f?.dataset?.rowId;

            // 1순위: 숨김 폼 내부 hidden 필드
            let tplId = (
                f.querySelector('input[name="product_template_id"]')?.value ||
                ""
            ).trim();

            // 2순위: 행의 검색 버튼 data-template-id
            if (!tplId && pid) {
                const btn = document.querySelector(
                    `button[data-price-form="${pid}"]`
                );
                tplId = (btn?.getAttribute("data-template-id") || "").trim();
            }

            if (tplId) ids.push(tplId);
        });

        const unique = Array.from(new Set(ids)); // 중복 제거
        return unique.length
            ? `excepts=${encodeURIComponent(unique.join(","))}`
            : "";
    }

    // 제품 템플릿 목록 로드 & 모달 표시
    async _fetchAllTemplates({ button, query = null } = {}) {
        let url = `/admin/product-templates`;
        const params = [];

        if (query) params.push(query);

        const excepts = this._buildExceptsParam();
        if (excepts) params.push(excepts);

        if (params.length) {
            url += `?${params.join("&")}`;
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
                document.querySelector(
                    `[data-price-form="${form.dataset.priceForm}"] button[data-view-type="search-button"]`
                ) ||
                document.querySelector("#template-hook")
            )?.focus?.();
            return;
        }

        const isZeroLike = /^0*(?:\.0+)?$/.test(priceStr); // "0", "0.0", "00.00" 등
        if (!priceStr || isNaN(priceNum) || isZeroLike || priceNum <= 0) {
            alert("가격을 0이 아닌 값으로 입력해주세요. (예: 1.00)");
            // 링크된 price input 포커스
            const pi = this._queryLinkedInput(form, "price");
            pi?.focus?.();
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
                location.reload();
            } else {
                alert(res.message || "처리에 실패했습니다.");
            }
        } catch (err) {
            console.error(err);
            // alert("요청 처리 중 오류가 발생했습니다.");
        } finally {
            this.loader.hide();
            submitter && (submitter.disabled = false);
        }
    }

    /**
     * 링크된 입력(name)을 찾아 반환
     * - form 내부 우선 검색
     * - 없으면 [form="form.id"]로 연결된 요소 검색
     * - 그래도 없으면 같은 행(tr[data-price-form="..."]) 범위에서 탐색(폴백)
     */
    _queryLinkedInput(form, name, preferInside = false) {
        let el = null;

        if (preferInside) {
            el = form.querySelector(`[name="${name}"]`);
        }
        if (!el && form.id) {
            el = document.querySelector(`[name="${name}"][form="${form.id}"]`);
        }
        if (!el) {
            const pid = form.dataset.priceForm;
            if (pid) {
                el = document.querySelector(
                    `tr[data-price-form="${pid}"] [name="${name}"]`
                );
            }
        }
        return el;
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
