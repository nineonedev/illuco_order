import Controller from "../core/Controller";
import CountrySelectInput from "../components/Inputs/CountrySelectInput";
import SelectInput from "../components/Inputs/SelectInput";
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";
import Ajax from "../core/Ajax";

export default class DealerController extends Controller {
    form;
    cancelBtn;
    modal;
    loader;
    tempHook; // ⬅️ 임시 비번 표시 위치
    _tempHideTimer; // ⬅️ 자동 숨김 타이머

    index() {
        this._logger.info("index");
        this._prepare();
        this._handleListDelete();
    }

    create() {
        this._logger.info("create");
        this._prepare();
        this.form.addEventListener("submit", this._store.bind(this));
    }

    edit() {
        this._logger.info("edit");
        this._prepare();

        // 기본 정보 저장
        this.form.addEventListener("submit", this._update.bind(this));

        // 삭제
        const deleteBtn = this.form.querySelector('[data-action="delete"]');
        deleteBtn?.addEventListener("click", this._destroy.bind(this));

        // 비밀번호 변경 / 임시 비밀번호 발급 / 회수
        const pwForm = document.getElementById("password-frm");
        const tempForm = document.getElementById("temp-frm");
        const revokeFrm = document.getElementById("revoke-frm");

        pwForm?.addEventListener("submit", this._updatePassword.bind(this));
        tempForm?.addEventListener(
            "submit",
            this._issueTempPassword.bind(this)
        );
        revokeFrm?.addEventListener(
            "submit",
            this._revokeTempPassword.bind(this)
        );
    }

    // ---------------------------
    // 저장/수정/삭제 (기존)
    // ---------------------------
    async _store(e) {
        e.preventDefault();
        const t = e.target,
            fd = new FormData(t),
            action = t.action,
            submitter = e.submitter;
        try {
            submitter.disabled = true;
            this.loader.show();
            const result = await this._ajax.post(action, fd, true);
            const { success, data } = result;
            if (success && data && data.dealer) {
                location.href = `${action}/edit/${data.dealer.id}`;
            }
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    async _update(e) {
        e.preventDefault();
        const t = e.target,
            fd = new FormData(t),
            action = t.action,
            submitter = e.submitter;
        try {
            submitter.disabled = true;
            this.loader.show();
            const result = await this._ajax.put(action, fd, true);
            if (result.success) location.reload();
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    async _destroy(e) {
        if (!confirm("정말로 삭제하시겠습니까?")) return;
        const action = this.form.action,
            submitter = e.currentTarget;
        const fd = new FormData(this.form);
        fd.set("_method", "delete");
        try {
            submitter.disabled = true;
            this.loader.show();
            const result = await this._ajax.delete(action, fd);
            if (result.success) this.cancelBtn?.click();
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    // ---------------------------
    // 비밀번호 변경
    // ---------------------------
    async _updatePassword(e) {
        e.preventDefault();
        const t = e.target;
        const fd = new FormData(t);
        const action = t.action;
        const submitter = e.submitter;

        const pw = fd.get("password") || "";
        const pwc =
            fd.get("password_confirmation") || fd.get("password_confirm") || "";
        if (pw.length < 8)
            return alert("비밀번호는 최소 8자 이상이어야 합니다.");
        if (pw !== pwc) return alert("비밀번호 확인이 일치하지 않습니다.");

        // 중복 방지를 위해 set 사용
        fd.set("password_confirmation", pwc);

        try {
            submitter.disabled = true;
            this.loader.show();
            const res = await this._ajax.put(action, fd, true);
            if (res.success) {
                t.reset();
                alert("비밀번호가 변경되었습니다.");
            } else {
                alert(res.message || "비밀번호 변경에 실패했습니다.");
            }
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    // ---------------------------
    // 임시 비밀번호 발급 -> #temp-hook에 렌더
    // ---------------------------
    async _issueTempPassword(e) {
        e.preventDefault();

        const t = e.target;
        const fd = new FormData(t);
        const action = t.action;
        const submitter = e.submitter;

        try {
            submitter.disabled = true;
            this.loader.show();
            const res = await this._ajax.post(action, fd, true);
            if (!res.success) {
                alert(res.message || "임시 비밀번호 발급에 실패했습니다.");
                return;
            }

            const { temp_password, expires_at } = res.data || {};

            if (this.tempHook) {
                // 기존 타이머 정리
                if (this._tempHideTimer) clearTimeout(this._tempHideTimer);

                // 표시 UI
                this.tempHook.innerHTML = `
                    <div class="no-temp-pw" style="margin-top:12px;border:1px dashed #ddd;padding:12px;border-radius:8px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <strong>임시 비밀번호</strong>
                            <button type="button" class="no-btn --xs" data-action="hide">숨기기</button>
                        </div>
                        <div class="no-kbd" style="font-size:20px;margin:8px 0">${
                            temp_password || "-"
                        }</div>
                        <p class="no-help" style="margin:0;">유효기간: ${
                            expires_at || "-"
                        }</p>
                        <div style="display:flex;gap:8px;margin-top:8px;">
                            <button type="button" class="no-btn --sm" data-action="copy">복사</button>
                        </div>
                        <p class="no-help" style="margin-top:8px">※ 이 영역은 보안상 잠시 후 자동으로 숨겨집니다.</p>
                    </div>
                `;

                // 이벤트 바인딩
                const copyBtn = this.tempHook.querySelector(
                    '[data-action="copy"]'
                );
                const hideBtn = this.tempHook.querySelector(
                    '[data-action="hide"]'
                );

                copyBtn?.addEventListener("click", async () => {
                    try {
                        await navigator.clipboard.writeText(
                            temp_password || ""
                        );
                        alert("복사되었습니다.");
                    } catch {
                        alert("복사 실패");
                    }
                });

                hideBtn?.addEventListener("click", () => {
                    this.tempHook.innerHTML = `<p class="no-help">임시 비밀번호가 발급되었습니다. (숨김)</p>`;
                });

                // 자동 숨김 (3분)
                this._tempHideTimer = setTimeout(() => {
                    if (this.tempHook?.querySelector(".no-temp-pw")) {
                        this.tempHook.innerHTML = `<p class="no-help">임시 비밀번호가 발급되었습니다. (자동 숨김)</p>`;
                    }
                }, 3 * 60 * 1000);

                // 화면으로 스크롤
                this.tempHook.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });
            } else {
                // fallback: 모달
                const html = `
                    <div class="no-modal-content">
                        <h3 class="no-heading-sm">임시 비밀번호</h3>
                        <div class="no-kbd" style="font-size:20px;margin:12px 0">${
                            temp_password || "-"
                        }</div>
                        <p class="no-help">유효기간: ${expires_at || "-"}</p>
                        <div class="no-flex no-gap-8" style="margin-top:12px">
                            <button class="no-btn --sm" data-action="copy">복사</button>
                            <button class="no-btn-primary --sm" data-action="close">닫기</button>
                        </div>
                        <p class="no-help" style="margin-top:8px">※ 이 창을 닫으면 다시 확인할 수 없습니다.</p>
                    </div>
                `;
                this.modal.setContent(html).open();
                const portal = this.modal.el;
                portal
                    .querySelector('[data-action="copy"]')
                    ?.addEventListener("click", async () => {
                        try {
                            await navigator.clipboard.writeText(
                                temp_password || ""
                            );
                            alert("복사되었습니다.");
                        } catch {
                            alert("복사 실패");
                        }
                    });
                portal
                    .querySelector('[data-action="close"]')
                    ?.addEventListener("click", () => {
                        this.modal.close().clear();
                    });
            }
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    // ---------------------------
    // 임시 비밀번호 회수(무효화)
    // ---------------------------
    async _revokeTempPassword(e) {
        e.preventDefault();
        const t = e.target;
        const action = t.action;
        const submitter = e.submitter || t.querySelector('[type="submit"]');

        // 서버로 delete (폼에 _method=delete가 이미 있음)
        const fd = new FormData(t);

        try {
            submitter && (submitter.disabled = true);
            this.loader.show();

            const res = await this._ajax.delete(action, fd);
            if (res.success) {
                // 화면에서 표시 제거/메시지
                if (this._tempHideTimer) clearTimeout(this._tempHideTimer);
                if (this.tempHook) {
                    this.tempHook.innerHTML = `<p class="no-help">임시 비밀번호가 회수(무효화)되었습니다.</p>`;
                }
                alert("임시 비밀번호를 해제했습니다.");
            } else {
                alert(res.message || "임시 비밀번호 해제에 실패했습니다.");
            }
        } finally {
            this.loader.hide();
            submitter && (submitter.disabled = false);
        }
    }

    // ---------------------------
    // 리스트 일괄 삭제(기존)
    // ---------------------------
    _handleListDelete() {
        const deleteBtn = document.getElementById("select-delete-btn");
        const chkAll = document.getElementById("chk-all");
        const chkItems = document.querySelectorAll('[name="checked_ids[]"]');
        if (!deleteBtn || !chkAll || chkItems.length === 0) return;

        const singleDeleteButtons = document.querySelectorAll(
            '[data-item-action="delete"]'
        );
        singleDeleteButtons.forEach((link) => {
            link.addEventListener("click", async (e) => {
                e.preventDefault();
                if (!confirm("정말로 삭제하시겠습니까?")) return;
                const action = link.getAttribute("href");
                this.loader.show();
                try {
                    const result = await new Ajax(false).delete(action);
                    if (result.success) location.reload();
                    else alert("삭제에 실패했습니다.");
                } finally {
                    this.loader.hide();
                }
            });
        });

        const updateDeleteButtonState = () => {
            const checkedItems = Array.from(chkItems).filter(
                (chk) => chk.checked
            );
            const count = checkedItems.length;
            deleteBtn.disabled = count === 0;
            deleteBtn.textContent =
                count > 0 ? `총 ${count}개 항목 삭제` : "선택삭제";
        };

        chkItems.forEach((chk) => {
            chk.addEventListener("change", () => {
                const allChecked = Array.from(chkItems).every((x) => x.checked);
                chkAll.checked = allChecked;
                updateDeleteButtonState();
            });
        });

        chkAll.addEventListener("change", () => {
            const checked = chkAll.checked;
            chkItems.forEach((chk) => (chk.checked = checked));
            updateDeleteButtonState();
        });

        deleteBtn.addEventListener("click", async () => {
            const checked = Array.from(chkItems).filter((chk) => chk.checked);
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

            const ids = checked.map((chk) => chk.value);
            this.loader.show();
            try {
                const result = await new Ajax(false).delete(
                    "/admin/dealers/bulk-delete",
                    new URLSearchParams({ ids })
                );
                if (result.success) location.reload();
                else alert("삭제에 실패했습니다.");
            } finally {
                this.loader.hide();
            }
        });

        updateDeleteButtonState();
    }

    _prepare() {
        this.form = document.getElementById("frm");
        this.cancelBtn = document.querySelector('[data-action="cancel"]');
        this.modal = Modal.make("portal").render();
        this.loader = Loader.make("portal").render();

        // ⬇️ 추가: 임시 비번 표시 위치
        this.tempHook = document.getElementById("temp-hook");
        if (this._tempHideTimer) {
            clearTimeout(this._tempHideTimer);
            this._tempHideTimer = null;
        }

        document
            .querySelectorAll('[data-view-type="country-select"]')
            .forEach((el) => {
                CountrySelectInput.make(el).render();
            });

        document.querySelectorAll('[data-view-type="select"]').forEach((el) => {
            SelectInput.make(el).render();
        });
    }
}
