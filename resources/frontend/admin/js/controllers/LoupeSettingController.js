// resources/assets/js/controllers/LoupeSettingController.js

import Controller from "../core/Controller";
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";
import Ajax from "../core/Ajax";
import SearchButton from "../components/Cart/SearchButton";
import TemplateSelection from "../components/Cart/TemplateSelection";

import FrameColorList from "@/components/Loupe/FrameColorList";
import LoupeSettingList from "@/components/Loupe/LoupeSettingList";
import MultiSelectInput from "../components/Inputs/MultiSelectInput";

export default class LoupeSettingController extends Controller {
  // ========== 상태 ==========
  createForm;              // 상단 "루페세팅 추가" 폼 (#create-setting)
  colorCreateForm;         // 프레임 컬러 추가 폼 (#create-frame-color)
  rows = [];               // (서버 렌더 폴백) 세팅 목록의 <tr>
  modal;
  loader;
  _ajax;

  // 템플릿 선택 컨텍스트
  _activeFormForPick = null;
  _activeButton = null;
  _activeButtonEl = null;

  // ========== 진입 메서드 ==========
  async index() {
    this._logger.info("loupe-setting.index");
    this._prepare();

    this.loader.show();
    // 하위 리스트(동적 렌더) 초기화
    await this._initChildLists();

    this.loader.hide();

    // 이벤트 버스
    this._listen("pick.template", this._pickTemplate.bind(this));
    this._listen("fetch.templates", this._fetchAllTemplates.bind(this));

    // 상단 세팅 생성 폼
    this.createForm = document.getElementById("create-setting");
    if (this.createForm) {
      // 템플릿 검색 버튼 장착
      const topInstance = SearchButton.make("template-hook", {
        label: "제품 검색",
        onClick: () => {
          this._activeFormForPick = this.createForm;
          this._activeButton = topInstance;
          this._activeButtonEl = document.querySelector(
            '#template-hook [data-view-type="search-button"], #template-hook button'
          );
          this._openTemplateSearch();
        },
      });
      topInstance.render();

      // 숫자 입력 보조(검증 & TOTAL PD 보조 표시)
      this._wireCreateFormValidations();

      // 제출
      this.createForm.addEventListener("submit", (e) => this._saveCreate(e));
    }

    // 프레임 컬러 추가 폼
    this.colorCreateForm = document.getElementById("create-frame-color");
    if (this.colorCreateForm) {
      this.colorCreateForm.addEventListener("submit", (e) =>
        this._saveColorCreate(e)
      );
      // 컬러 피커 ↔ 텍스트 동기화(뷰 내 스크립트와 중복되어도 무해)
      this._syncHexPicker(
        document.getElementById("color_hex_new"),
        document.getElementById("color_hex_picker_new")
      );
    }

    // (서버 렌더 폴백) 세팅 목록 행 처리
    if (!document.getElementById("loupe-setting-hook")) {
      this._wireSettingRows();
    }
  }

  // ========== 동적 리스트 초기화 ==========
  async _initChildLists() {
    const response = await this._ajax.get('/admin/loupe-setting');
    const {settings, colors} = response.data
    
    // settings, colors(선택된) | colors 

    // 프레임 컬러 목록
    const fcHook = document.getElementById("frame-color-hook");
    if (fcHook) {
    //   const initColors = window.__FRAME_COLORS__ || [];
      FrameColorList.make("frame-color-hook", {
        colors: colors,
        endpoints: { base: "/admin/loupe-frame-colors" },
      }).render();
    }

    // 루페 세팅 목록 (선택적)
    const lsHook = document.getElementById("loupe-setting-hook");
    if (lsHook) {
    //   const initSettings = window.__LOUPE_SETTINGS__ || [];
    //   const initColors = window.__FRAME_COLORS__ || [];
      LoupeSettingList.make("loupe-setting-hook", {
        settings: settings,
        colors: colors,
        endpoints: { base: "/admin/loupe-settings" },
      }).render();
    }
  }

  // ========== 템플릿 검색 ==========
  async _openTemplateSearch() {
    await this._fetchAllTemplates({});
  }

  _buildExceptsParam() {
    // 현재 화면에 이미 선택된 템플릿 제외 (중복 방지)
    const ids = [];

    // 생성 폼
    const createTpl = this.createForm?.querySelector('input[name="template_id"]')?.value;
    if (createTpl) ids.push(createTpl);

    // 서버 렌더 폴백(행 폼)
    (this.rows || []).forEach((tr) => {
      const formId = tr.querySelector("form[id^=f-]")?.id;
      if (!formId) return;
      const linked = document.querySelector(`input[name="template_id"][form="${formId}"]`);
      const btn = tr.querySelector("[data-template-id]");
      const v = (linked?.value || btn?.getAttribute("data-template-id") || "").trim();
      if (v) ids.push(v);
    });

    const unique = Array.from(new Set(ids));
    return unique.length ? `excepts=${encodeURIComponent(unique.join(","))}` : "";
  }

  async _fetchAllTemplates({ button, query = null } = {}) {
    let url = `/admin/product-templates/loupes`; // 카테고리: loupe 전용
    const params = [];
    if (query) params.push(query);

    const excepts = this._buildExceptsParam();
    if (excepts) params.push(excepts);
    if (params.length) url += `?${params.join("&")}`;

    try {
      button?.setState?.({ disabled: true });
      this.loader.show();

      const res = await this._ajax.get(url);
      if (!res?.success) throw new Error("템플릿 로드 실패");

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
    } catch (e) {
      console.error(e);
      alert("제품 목록을 불러오는 중 오류가 발생했습니다.");
    } finally {
      button?.setState?.({ disabled: false });
      this.loader.hide();
    }
  }

  _pickTemplate({ template }) {
    const form = this._activeFormForPick || this.createForm;
    if (!form || !template) return;

    // 숨김 필드 주입
    const idInput = form.querySelector('input[name="template_id"]');
    if (idInput) idInput.value = template.id;

    // 버튼 라벨/속성 갱신
    this._activeButton?.setState?.({ label: template.name });
    this._activeButtonEl?.setAttribute?.("data-template-id", String(template.id));

    // 모달 닫기 & 상태 초기화
    this.modal.setState({ open: false, header: "", content: "" });
    this._activeFormForPick = null;
    this._activeButton = null;
    this._activeButtonEl = null;

    this._toast("제품 템플릿이 선택되었습니다.");
  }

  // ========== 생성 폼: 검증 & 제출 ==========
  _wireCreateFormValidations() {
    if (!this.createForm) return;

    const q = (sel) => this.createForm.querySelector(sel);

    const wdMin = q('input[name="wd_min"]');
    const wdMax = q('input[name="wd_max"]');
    const vdMin = q('input[name="vd_min"]');
    const vdMax = q('input[name="vd_max"]');

    const frMin = q('input[name="fd_right_min"]');
    const frMax = q('input[name="fd_right_max"]');
    const flMin = q('input[name="fd_left_min"]');
    const flMax = q('input[name="fd_left_max"]');

    const total = q('input[name="fd_total_distance"]');

    // 총 PD 보조 계산 & 좌우 편차 경고
    const helperId = "pd-helper";
    let helper = document.getElementById(helperId);
    if (!helper) {
      helper = document.createElement("div");
      helper.id = helperId;
      helper.className = "no-text-xs";
      helper.style.marginTop = "4px";
      total?.closest(".no-form-control")?.appendChild(helper);
    }

    const showPDHelper = () => {
      const R = parseFloat((frMin?.value || "").trim());
      const L = parseFloat((flMin?.value || "").trim());
      if (!isFinite(R) || !isFinite(L)) {
        helper.textContent = "";
        return;
      }
      const sum = (R + L).toFixed(1);
      const diff = Math.abs(R - L);
      helper.textContent = `계산 도움: R(${R}) + L(${L}) = ${sum} mm`;
      if (diff > 2) helper.textContent += "  •  경고: 좌/우 편차가 2mm를 초과합니다.";
      if (total && (total.value == null || total.value === "")) total.value = sum;
    };

    [frMin, flMin, frMax, flMax].forEach((el) => el?.addEventListener("input", showPDHelper));

    // 범위 기본 검증
    const ensureRange = (minEl, maxEl, label) => {
      const a = parseFloat((minEl?.value || "").trim());
      const b = parseFloat((maxEl?.value || "").trim());
      if (isFinite(a) && isFinite(b) && a > b) {
        alert(`${label}의 최소값이 최대값보다 큽니다.`);
        (minEl || maxEl)?.focus?.();
        return false;
      }
      return true;
    };

    // VD 10~25 사이만 허용 (배경 기준)
    const ensureVD = () => {
      const a = parseFloat((vdMin?.value || "").trim());
      const b = parseFloat((vdMax?.value || "").trim());
      if (isFinite(a) && (a <= 10 || a >= 25)) {
        alert("VD 최소는 10~25mm 사이 값만 허용됩니다.");
        vdMin?.focus?.();
        return false;
      }
      if (isFinite(b) && (b <= 10 || b >= 25)) {
        alert("VD 최대는 10~25mm 사이 값만 허용됩니다.");
        vdMax?.focus?.();
        return false;
      }
      return true;
    };

    [wdMin, wdMax].forEach((el) =>
      el?.addEventListener("change", () => ensureRange(wdMin, wdMax, "WD"))
    );
    [vdMin, vdMax].forEach((el) =>
      el?.addEventListener("change", () => ensureVD() && ensureRange(vdMin, vdMax, "VD"))
    );
    [frMin, frMax].forEach((el) =>
      el?.addEventListener("change", () => ensureRange(frMin, frMax, "far PD RIGHT"))
    );
    [flMin, flMax].forEach((el) =>
      el?.addEventListener("change", () => ensureRange(flMin, flMax, "far PD LEFT"))
    );

    // 제출 전 최종 종합 검증
    this._preSubmitValidators = () => {
      if (!this._requireTemplate(this.createForm)) return false;

      const must = [wdMin, wdMax, vdMin, vdMax, frMin, frMax, flMin, flMax, total];
      for (const el of must) {
        if (!el) continue;
        const v = (el.value || "").trim();
        if (v === "" || isNaN(parseFloat(v))) {
          alert("필수 수치 항목이 비어있거나 올바르지 않습니다.");
          el?.focus?.();
          return false;
        }
      }

      if (!ensureRange(wdMin, wdMax, "WD")) return false;
      if (!(ensureVD() && ensureRange(vdMin, vdMax, "VD"))) return false;
      if (!ensureRange(frMin, frMax, "far PD RIGHT")) return false;
      if (!ensureRange(flMin, flMax, "far PD LEFT")) return false;

      const R = parseFloat((frMin?.value || "").trim());
      const L = parseFloat((flMin?.value || "").trim());
      if (isFinite(R) && isFinite(L) && Math.abs(R - L) > 2) {
        if (!confirm("좌/우 편차가 2mm를 초과합니다. 계속하시겠습니까?")) return false;
      }
      return true;
    };
  }

  async _saveCreate(e) {
    e.preventDefault();
    const form = e.currentTarget;
    if (typeof this._preSubmitValidators === "function") {
      if (!this._preSubmitValidators()) return;
    }

    const submitter = e.submitter || form.querySelector('[type="submit"]');
    const fd = new FormData(form);

    try {
      submitter && (submitter.disabled = true);
      this.loader.show();

      const res = await this._ajax.post(form.action, fd, true);
      if (res?.success) {
        this._toast(res.message || "루페 세팅이 추가되었습니다.");
        location.reload();
      } else {
        alert(res?.message || "추가에 실패했습니다.");
      }
    } catch (err) {
      console.error(err);
      alert("요청 처리 중 오류가 발생했습니다.");
    } finally {
      this.loader.hide();
      submitter && (submitter.disabled = false);
    }
  }

  // ========== 프레임 컬러 추가 ==========
  async _saveColorCreate(e) {
    e.preventDefault();
    const form = e.currentTarget;
    const submitter = e.submitter || form.querySelector('[type="submit"]');

    // 최소 유효성
    const nameEl = form.querySelector('input[name="name"]');
    if (!nameEl || !nameEl.value.trim()) {
      alert("컬러 이름을 입력하세요.");
      nameEl?.focus?.();
      return;
    }

    const fd = new FormData(form);
    try {
      submitter && (submitter.disabled = true);
      this.loader.show();

      const res = await this._ajax.post(form.action, fd, true);
      if (res?.success) {
        this._toast(res.message || "프레임 컬러가 추가되었습니다.");
        location.reload();
      } else {
        alert(res?.message || "추가에 실패했습니다.");
      }
    } catch (err) {
      console.error(err);
      alert("요청 처리 중 오류가 발생했습니다.");
    } finally {
      this.loader.hide();
      submitter && (submitter.disabled = false);
    }
  }

  // ========== 목록 행(서버 렌더 폴백) ==========
  _wireSettingRows() {
    this.rows = Array.from(
      document.querySelectorAll("table.loupe-setting__table tbody tr")
    ).filter((tr) => tr.querySelector('form[id^="f-"]'));

    this.rows.forEach((tr) => {
      const updateBtn = tr.querySelector('button[type="submit"]');
      const deleteForm = tr.querySelector('form[action*="loupe-settings.destroy"]');

      // 저장: 숨김 폼 submit 그대로 사용 (버튼 form 속성 이용)
      updateBtn?.addEventListener("click", (event) => {
        const formId = updateBtn.getAttribute("form");
        const form = formId ? document.getElementById(formId) : null;
        if (!form) return;

        const get = (n) => document.querySelector(`[name="${n}"][form="${formId}"]`);
        const ok =
          this._ensureMinMax(get("wd_min"), get("wd_max"), "WD") &&
          this._ensureVDInline(get("vd_min"), get("vd_max")) &&
          this._ensureMinMax(get("fd_right_min"), get("fd_right_max"), "far PD RIGHT") &&
          this._ensureMinMax(get("fd_left_min"), get("fd_left_max"), "far PD LEFT");

        if (!ok) {
          event?.preventDefault?.();
          event?.stopPropagation?.();
          return false;
        }
        return true;
      });

      // 삭제
      deleteForm?.addEventListener("submit", (e) => {
        if (!confirm("정말 삭제하시겠습니까?")) {
          e.preventDefault();
          e.stopPropagation();
          return false;
        }
      });
    });
  }

  _ensureMinMax(minEl, maxEl, label) {
    if (!minEl || !maxEl) return true;
    const a = parseFloat((minEl.value || "").trim());
    const b = parseFloat((maxEl.value || "").trim());
    if (isFinite(a) && isFinite(b) && a > b) {
      alert(`${label}의 최소값이 최대값보다 큽니다.`);
      minEl.focus();
      return false;
    }
    return true;
  }

  _ensureVDInline(vdMin, vdMax) {
    const a = parseFloat((vdMin?.value || "").trim());
    const b = parseFloat((vdMax?.value || "").trim());
    if (isFinite(a) && (a <= 10 || a >= 25)) {
      alert("VD 최소는 10~25mm 사이 값만 허용됩니다.");
      vdMin?.focus?.();
      return false;
    }
    if (isFinite(b) && (b <= 10 || b >= 25)) {
      alert("VD 최대는 10~25mm 사이 값만 허용됩니다.");
      vdMax?.focus?.();
      return false;
    }
    if (isFinite(a) && isFinite(b) && a > b) {
      alert("VD 최소값이 최대값보다 큽니다.");
      vdMin?.focus?.();
      return false;
    }
    return true;
  }

  // ========== 유틸 ==========
  _requireTemplate(form) {
    const tpl = form.querySelector('input[name="template_id"]');
    if (!tpl || !tpl.value.trim()) {
      alert("제품 템플릿을 선택해주세요.");
      form.querySelector("#template-hook button")?.focus?.();
      return false;
    }
    return true;
  }

  _syncHexPicker(hexInput, pickerInput) {
    if (!hexInput || !pickerInput) return;
    pickerInput.addEventListener("input", () => (hexInput.value = pickerInput.value));
    hexInput.addEventListener("input", () => {
      const v = hexInput.value?.trim();
      if (/^#([0-9a-fA-F]{6})$/.test(v)) pickerInput.value = v;
    });
  }

  _toast(message) {
    if (window?.noToast) return window.noToast.success(message);
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
    this._ajax = this._ajax || new Ajax(true);

    document.querySelectorAll('[data-view-type="multi-select"]').forEach((el) => {
        MultiSelectInput.make(el).render();
    });
  }
}
