// resources/assets/js/controllers/DealerMemoController.js

import Controller from "../core/Controller";
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";
import Ajax from "../core/Ajax";

export default class DealerMemoController extends Controller {
    form;
    cancelBtn;
    modal;
    loader;

    edit() {
        this._logger.info("dealer-memo.edit");
        this._prepare();

        if (!this.form) {
            this._logger.warn("#frm not found");
            return;
        }

        // 저장
        this.form.addEventListener("submit", this._save.bind(this));

        // 단축키: Ctrl/Cmd + S
        window.addEventListener("keydown", (e) => {
            const isMac = navigator.platform.toUpperCase().indexOf("MAC") >= 0;
            if (
                (isMac ? e.metaKey : e.ctrlKey) &&
                e.key.toLowerCase() === "s"
            ) {
                e.preventDefault();
                this.form.requestSubmit?.() || this.form.submit();
            }
        });
    }

    async _save(e) {
        e.preventDefault();
        const t = e.target;
        const fd = new FormData(t);
        const action = t.action;
        const submitter = e.submitter || t.querySelector('[type="submit"]');

        // 체크박스 명시적 전송(미체크 시 FormData에 안 들어가므로 0으로 고정)
        this._ensureBoolean(fd, "is_pinned_general");
        this._ensureBoolean(fd, "is_pinned_prod");

        try {
            submitter && (submitter.disabled = true);
            this.loader.show();

            const res = await this._ajax.post(action, fd, true);
            if (res.success) {
                // 성공 안내
                this._toast("정상적으로 저장되었습니다.");
                // 필요 시 최신 값으로 폼 동기화 (서버 응답에 값이 있으면 반영)
                const data = res.data || {};
                this._hydrateAfterSave(data);
            } else {
                alert(res.message || "저장에 실패했습니다.");
            }
        } finally {
            this.loader.hide();
            submitter && (submitter.disabled = false);
        }
    }

    _hydrateAfterSave(data) {
        // 서버에서 내려준 값을 폼에 반영(선택)
        // textarea
        const memoGeneral = this.form.querySelector("#memo_general");
        const memoProd = this.form.querySelector("#memo_production");
        if (memoGeneral && typeof data.memo_general === "string") {
            memoGeneral.value = data.memo_general;
        }
        if (memoProd && typeof data.memo_production === "string") {
            memoProd.value = data.memo_production;
        }

        // 체크박스
        const pinGeneral = this.form.querySelector("#pin_general");
        const pinProd = this.form.querySelector("#pin_prod");
        if (pinGeneral && typeof data.is_pinned_general === "boolean") {
            pinGeneral.checked = data.is_pinned_general;
        }
        if (pinProd && typeof data.is_pinned_prod === "boolean") {
            pinProd.checked = data.is_pinned_prod;
        }

        // updated_at 메시지 (있다면 표시)
        const stamp = this.form.querySelector('[data-role="updated-at"]');
        if (stamp && data.updated_at) {
            stamp.textContent = `마지막 저장: ${data.updated_at}`;
        }
    }

    _ensureBoolean(fd, name) {
        if (!fd.has(name)) {
            fd.set(name, "0");
        }
    }

    _toast(message) {
        // 프로젝트 공통 토스트가 없다면 alert 대체
        if (window?.noToast) {
            window.noToast.success(message);
        } else {
            // 심플 토스트
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
    }

    _prepare() {
        this.form = document.getElementById("frm");
        this.cancelBtn = document.querySelector('[data-action="cancel"]');
        this.modal = Modal.make("portal").render();
        this.loader = Loader.make("portal").render();
    }
}
