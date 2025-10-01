// resources/assets/js/controllers/LoupeSettingController.js
import Controller from "../core/Controller";
import Ajax from "../core/Ajax";
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";
import SelectInput from "../components/Inputs/SelectInput";
import MultiSelectInput from "../components/Inputs/MultiSelectInput";
import SearchButton from "../components/Cart/SearchButton";
import TemplateSelection from "../components/Cart/TemplateSelection";

export default class LoupeSettingController extends Controller {
    form;
    cancelBtn;

    // 템플릿 선택 컨텍스트
    _activeFormForPick = null;
    _activeButton = null;
    _activeButtonEl = null;

    index() {
        this._logger.info("loupe-settings.index");
        this._prepare();
        this._handleListDelete(); // 선택삭제/개별삭제(목록 페이지용)
    }

    async create() {
        this._logger.info("loupe-settings.create");
        this._prepare();

        // 이벤트 버스
        this._listen("pick.template", this._pickTemplate.bind(this));
        this._listen("fetch.templates", this._fetchAllTemplates.bind(this));

        // #template-hook에 검색 버튼 장착
        const tplHook = document.getElementById("template-hook");
        if (tplHook) {
            const btnInstance = SearchButton.make("template-hook", {
                label: "제품 검색",
                onClick: () => {
                    // 현재 폼/버튼 컨텍스트 저장 → 선택 시 라벨 갱신 및 hidden 주입에 사용
                    this._activeFormForPick = this.form;
                    this._activeButton = btnInstance;
                    this._activeButtonEl = tplHook.querySelector(
                        '[data-view-type="search-button"], button'
                    );
                    this._openTemplateSearch(); // => _fetchAllTemplates 호출
                },
            });
            btnInstance.render();
        }

        // 생성 제출 = store
        this.form?.addEventListener("submit", async (e) => {
            await this.store(e);
        });
    }

    async store(e) {
        e.preventDefault();
        const submitter =
            e.submitter || this.form.querySelector('[type="submit"]');
        const data = new FormData(this.form);
        const action = this.form.action || "/admin/loupe-settings";

        try {
            submitter && (submitter.disabled = true);
            this.loader.show();

            const result = await new Ajax(false).post(action, data, true);
            if (result?.success) {
                const redirect =
                    result?.data?.redirect || "/admin/loupe-settings";
                location.href = redirect;
            } else {
                alert(result?.message || "등록에 실패했습니다.");
            }
        } finally {
            this.loader.hide();
            submitter && (submitter.disabled = false);
        }
    }

    async edit() {
        this._logger.info("loupe-settings.edit");
        this._prepare();

        // 템플릿 검색 이벤트 리스너 (create와 동일)
        this._listen("pick.template", this._pickTemplate.bind(this));
        this._listen("fetch.templates", this._fetchAllTemplates.bind(this));

        // #template-hook에 SearchButton 장착 (현재 템플릿명으로 초기 라벨 표시)
        const tplHook = document.getElementById("template-hook");
        if (tplHook) {
            const initLabel =
                tplHook.getAttribute("data-current-label") || "제품 변경";
            const btnInstance = SearchButton.make("template-hook", {
                label: initLabel,
                onClick: () => {
                    this._activeFormForPick = this.form;
                    this._activeButton = btnInstance;
                    this._activeButtonEl = tplHook.querySelector(
                        '[data-view-type="search-button"], button'
                    );
                    this._openTemplateSearch(); // => _fetchAllTemplates
                },
            });
            btnInstance.render();
        }

        // 저장
        this.form?.addEventListener("submit", async (e) => {
            await this.update(e);
        });

        // 삭제
        const deleteBtn = this.form?.querySelector('[data-action="delete"]');
        deleteBtn?.addEventListener("click", async () => {
            const data = new FormData(this.form);
            data.set("_method", "delete");
            await this.destroy(this.form.action, data);
        });
    }

    async update(e) {
        e.preventDefault();
        const submitter =
            e.submitter || this.form.querySelector('[type="submit"]');
        const data = new FormData(this.form);
        const action = this.form.action; // 예: /admin/loupe-settings/{id}

        try {
            submitter && (submitter.disabled = true);
            this.loader.show();

            const result = await new Ajax(false).put(action, data, true);
            if (result?.success) {
                const redirect = result?.data?.redirect;
                if (redirect) {
                    location.href = redirect;
                } else {
                    location.reload();
                }
            } else {
                alert(result?.message || "수정에 실패했습니다.");
            }
        } finally {
            this.loader.hide();
            submitter && (submitter.disabled = false);
        }
    }

    async destroy(action, data = null) {
        if (!confirm("정말로 삭제하시겠습니까?")) return;

        this.loader.show();
        try {
            const result = await new Ajax(false).delete(action, data);
            if (result?.success) {
                const redirect =
                    result?.data?.redirect || "/admin/loupe-settings";
                location.href = redirect;
            } else {
                alert(result?.message || "삭제에 실패했습니다.");
            }
        } finally {
            this.loader.hide();
        }
    }

    // =========================
    // 템플릿 검색/선택
    // =========================
    _openTemplateSearch() {
        this._fetchAllTemplates({});
    }

    _buildExceptsParam() {
        // 현재 폼 hidden에 값 있으면 제외
        const current = (
            this.form?.querySelector('input[name="template_id"]')?.value || ""
        ).trim();
        return current ? `excepts=${encodeURIComponent(current)}` : "";
    }

    async _fetchAllTemplates({ button, query = null } = {}) {
        let url = `/admin/product-templates/loupes`;
        const params = [];
        if (query) params.push(query);
        const excepts = this._buildExceptsParam();
        if (excepts) params.push(excepts);
        if (params.length) url += `?${params.join("&")}`;

        try {
            button?.setState?.({ disabled: true });
            this.loader.show();

            const res = await new Ajax(true).get(url);
            if (!res?.success) return;

            const { templates, categories, query: q } = res.data || {};
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
            this.loader.hide();
            button?.setState?.({ disabled: false });
        }
    }

    _pickTemplate({ template }) {
        const form = this._activeFormForPick || this.form;
        if (!form || !template) return;

        // hidden 주입
        const idInput = form.querySelector('input[name="template_id"]');
        if (idInput) idInput.value = template.id;

        // 버튼 라벨 갱신
        this._activeButton?.setState?.({ label: `${template.name} - ${template.model}` });

        // 모달 닫고 컨텍스트 초기화
        this.modal.setState({ open: false, header: "", content: "" });
        this._activeFormForPick = null;
        this._activeButton = null;
        this._activeButtonEl = null;
    }

    // =========================
    // 공통 준비 & UI 훅
    // =========================
    _prepare() {
        this.form = document.getElementById("frm");
        this.cancelBtn = document.querySelector('[data-action="cancel"]');

        this.modal = Modal.make("portal").render();
        this.loader = Loader.make("portal").render();
        this._ajax = this._ajax || new Ajax(true);

        // select / multiselect 초기화
        document.querySelectorAll('[data-view-type="select"]').forEach((el) => {
            SelectInput.make(el).render();
        });
        document
            .querySelectorAll('[data-view-type="multi-select"]')
            .forEach((el) => {
                MultiSelectInput.make(el).render();
            });
    }

    _handleListDelete() {
        
        const deleteBtn = document.getElementById("select-delete-btn");
        const chkAll = document.getElementById("chk-all");
        const chkItems = document.querySelectorAll('[name="checked_ids[]"]');

        // 개별 삭제 버튼(행 액션)
        const singleDeleteButtons = document.querySelectorAll(
            '[data-method="delete"]'
        );
        singleDeleteButtons.forEach(button => {
            button.addEventListener('click', async e => {
                e.preventDefault(); 

                if (!confirm("정말로 삭제하시겠습니까?")) return;

                const action = button.getAttribute('data-action');
                this.loader.show();

                try {
                    const result = await new Ajax(false).delete(action);

                    if (result.success) {
                        location.reload();
                    } else {
                        alert("삭제에 실패했습니다.");
                    }
                } finally {
                    this.loader.hide();
                }
            });
        });

        if (!deleteBtn || !chkAll || chkItems.length === 0) return;

        const updateDeleteButtonState = () => {
            const count = Array.from(chkItems).filter((c) => c.checked).length;
            deleteBtn.disabled = count === 0;
            deleteBtn.textContent =
                count > 0 ? `총 ${count}개 항목 삭제` : "선택삭제";
        };

        chkItems.forEach((c) =>
            c.addEventListener("change", () => {
                chkAll.checked = Array.from(chkItems).every((x) => x.checked);
                updateDeleteButtonState();
            })
        );

        chkAll.addEventListener("change", () => {
            const checked = chkAll.checked;
            chkItems.forEach((c) => (c.checked = checked));
            updateDeleteButtonState();
        });

        deleteBtn.addEventListener("click", async () => {
            const checked = Array.from(chkItems).filter((c) => c.checked);
            if (checked.length === 0) {
                alert("삭제할 항목을 선택해주세요.");
                return;
            }
            if (
                !confirm(
                    `정말로 ${checked.length}개의 항목을 삭제하시겠습니까?`
                )
            )
                return;

            // bulk 엔드포인트가 없으면 순차 삭제
            this.loader.show();
            try {
                for (const c of checked) {
                    const id = c.value;
                    await new Ajax(false).delete(`/admin/loupe-settings/${id}`);
                }
                location.reload();
            } finally {
                this.loader.hide();
            }
        });

        updateDeleteButtonState();
    }
}
