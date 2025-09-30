// resources/assets/js/controllers/LoupeFrameColorController.js
import Controller from "../core/Controller";
import Ajax from "../core/Ajax";
import Loader from "../shared/Loader";

export default class LoupeFrameColorController extends Controller {
  loader;
  _ajax;

  index() {
    this._logger.info("loupe-frame-colors.index");
    this._prepare();

    // 좌측 카드 바인딩
    document.querySelectorAll(".lc-card").forEach((card) => {
      this._bindCard(card);
    });

    // 우측 추가 폼 바인딩
    const createForm = document.getElementById("create-frame-color");
    if (createForm) this._bindCreateForm(createForm);
  }

  /* =========================
   * Prepare
   * ========================= */
  _prepare() {
    this.loader = Loader.make("portal").render();
    this._ajax = this._ajax || new Ajax(true);
  }

  /* =========================
   * 카드 한 장 바인딩
   * ========================= */
  _bindCard(card) {
    const form = card.querySelector(".lc-card__form");
    const btnDelete = card.querySelector('[data-item-action="delete"]');

    // 이름 입력 → 헤더 타이틀 실시간 반영
    const nameInput = card.querySelector('input[name="name"]');
    const nameLabel = card.querySelector(".lc-name");
    if (nameInput && nameLabel) {
      nameInput.addEventListener("input", () => {
        nameLabel.textContent = nameInput.value || nameLabel.textContent;
      });
    }

    // HEX/피커/칩 동기화
    this._syncColorInputs(card);

    // 저장(UPDATE)
    if (form) {
      form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const submitter = e.submitter || form.querySelector('[type="submit"]');
        const data = new FormData(form);
        const action = form.action;

        try {
          submitter && (submitter.disabled = true);
          this.loader.show();

          // HTML form PUT 호환 위해 _method가 없다면 추가
          if (!data.get("_method")) data.append("_method", "put");

          const res = await new Ajax(false).put(action, data, true);
          if (res?.success) {
            // redirect가 있으면 이동, 없으면 깔끔히 재로딩
            const redirect = res?.data?.redirect;
            redirect ? (location.href = redirect) : location.reload();
          } else {
            alert(res?.message || "수정에 실패했습니다.");
          }
        } catch (err) {
          console.error(err);
          alert("저장 중 오류가 발생했습니다.");
        } finally {
          this.loader.hide();
          submitter && (submitter.disabled = false);
        }
      });
    }

    // 삭제(DELETE)
    if (btnDelete) {
      btnDelete.addEventListener("click", async (e) => {
        e.preventDefault();
        const href = btnDelete.getAttribute("href");
        if (!href) return;
        if (!confirm("정말로 삭제하시겠습니까?")) return;

        try {
          this.loader.show();
          const res = await new Ajax(false).delete(href);
          if (res?.success) {
            const redirect = res?.data?.redirect;
            redirect ? (location.href = redirect) : location.reload();
          } else {
            alert(res?.message || "삭제에 실패했습니다.");
          }
        } catch (err) {
          console.error(err);
          alert("삭제 중 오류가 발생했습니다.");
        } finally {
          this.loader.hide();
        }
      });
    }
  }

  /* =========================
   * HEX ↔ input[type=color] ↔ 카드칩 동기화
   * ========================= */
  _syncColorInputs(scopeEl) {
    // 카드 스코프 내 아이디 규칙: #color_hex_{id}, #color_picker_{id}
    const hexInput = scopeEl.querySelector('[id^="color_hex_"]');
    const picker = scopeEl.querySelector('[id^="color_picker_"]');

    // 카드칩은 루트 .lc-card에 --chip CSS 변수를 씀
    const card = scopeEl.closest(".lc-card");
    const setChip = (val) => {
      if (card) card.style.setProperty("--chip", val);
    };

    // 초기칩
    if (picker && picker.value) setChip(picker.value);

    // 피커 → HEX/칩
    if (picker && hexInput) {
      picker.addEventListener("input", () => {
        hexInput.value = picker.value;
        setChip(picker.value);
      });
    }

    // HEX → 피커/칩 (유효성 검사)
    if (hexInput && picker) {
      hexInput.addEventListener("input", () => {
        const v = (hexInput.value || "").trim();
        if (/^#([0-9a-fA-F]{6})$/.test(v)) {
          picker.value = v;
          setChip(v);
        }
      });
    }
  }

  /* =========================
   * 우측 생성 폼 (STORE)
   * ========================= */
  _bindCreateForm(form) {
    // 우측 프리뷰 칩: #new-color-preview .no-chip__dot
    const previewDot = document.querySelector(
      "#new-color-preview .no-chip__dot"
    );
    const hex = form.querySelector("#color_hex_new");
    const picker = form.querySelector("#color_hex_picker_new");

    // 동기화 (우측 폼만의 미리보기)
    if (hex && picker && previewDot) {
      const setPrev = (v) => (previewDot.style.background = v);
      setPrev(picker.value || "#999999");

      picker.addEventListener("input", () => {
        hex.value = picker.value;
        setPrev(picker.value);
      });
      hex.addEventListener("input", () => {
        const v = (hex.value || "").trim();
        if (/^#([0-9a-fA-F]{6})$/.test(v)) {
          picker.value = v;
          setPrev(v);
        }
      });
    }

    // 제출
    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      const submitter = e.submitter || form.querySelector('[type="submit"]');
      const data = new FormData(form);
      const action = form.action || "/admin/loupe-frame-colors";

      try {
        submitter && (submitter.disabled = true);
        this.loader.show();

        const res = await new Ajax(false).post(action, data, true);
        if (res?.success) {
          const redirect = res?.data?.redirect || location.href;
          location.href = redirect;
        } else {
          alert(res?.message || "등록에 실패했습니다.");
        }
      } catch (err) {
        console.error(err);
        alert("등록 중 오류가 발생했습니다.");
      } finally {
        this.loader.hide();
        submitter && (submitter.disabled = false);
      }
    });
  }
}
