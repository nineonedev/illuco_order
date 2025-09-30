import View from "../../core/View";
import Helper from "../../supports/Helper";
import SelectInput from "../Inputs/SelectInput";

/**
 * DealerSelection
 * - 컨트롤러: modal.setState({ content: DealerSelection.make(null, {...}, false) })
 * - 검색/정렬/페이징 시 'fetch.dealers'로 query 전달
 * - 행 선택 시 'pick.dealer'로 { dealer } 전달
 */
export default class DealerSelection extends View {
  _defineProps() {
    return {
      paginator: {}, // { data: [...], current_page, per_page, ... }
      query: {
        name: "",
        country: "",
        code: "",
        phone: "",
        email: "",
        sort: "created_at_desc",
      },
      countries: {}, // { KR: "Korea", ... }
    };
  }

  _defineState() {
    return { ...this._props };
  }

  _template() {
    const { paginator, query } = this._state;
    const data = paginator?.data ?? [];

    return `
      <div>
        <form id="dealer-search-form" class="no-page-search-form">
          <div class="no-page-search-form__actions">
            <button type="button" class="no-btn-success --xs" id="dealer-btn-reset">초기화</button>
            <button type="submit" class="no-btn-primary --xs" id="dealer-btn-submit">검색</button>
          </div>

          <div class="no-page-search-form-row">
            <div class="no-form-search">
              <label for="dealer_name" class="no-form-label">대리점명</label>
              <div class="no-form-search-inner">
                <div class="no-form-search__icon"><i class="fa-light fa-magnifying-glass"></i></div>
                <input
                  type="search"
                  name="name"
                  id="dealer_name"
                  class="no-form-search-input"
                  placeholder="대리점명 검색"
                  value="${Helper.escapeHtml(query.name ?? "")}"
                />
              </div>
            </div>

            <div data-ref="country"></div>

            <div class="no-form-search">
              <label for="dealer_code" class="no-form-label">코드</label>
              <div class="no-form-search-inner">
                <div class="no-form-search__icon"><i class="fa-light fa-magnifying-glass"></i></div>
                <input
                  type="search"
                  name="code"
                  id="dealer_code"
                  class="no-form-search-input"
                  placeholder="코드 검색"
                  value="${Helper.escapeHtml(query.code ?? "")}"
                />
              </div>
            </div>

            <div class="no-form-search">
              <label for="dealer_phone" class="no-form-label">연락처</label>
              <div class="no-form-search-inner">
                <div class="no-form-search__icon"><i class="fa-light fa-magnifying-glass"></i></div>
                <input
                  type="search"
                  name="phone"
                  id="dealer_phone"
                  class="no-form-search-input"
                  placeholder="연락처 검색"
                  value="${Helper.escapeHtml(query.phone ?? "")}"
                />
              </div>
            </div>

            <div class="no-form-search">
              <label for="dealer_email" class="no-form-label">이메일</label>
              <div class="no-form-search-inner">
                <div class="no-form-search__icon"><i class="fa-light fa-magnifying-glass"></i></div>
                <input
                  type="search"
                  name="email"
                  id="dealer_email"
                  class="no-form-search-input"
                  placeholder="이메일 검색"
                  value="${Helper.escapeHtml(query.email ?? "")}"
                />
              </div>
            </div>

            <div data-ref="sort"></div>
          </div>
        </form>

        <div class="no-page-index-table-outer">
          <table class="no-page-index-table">
            <thead>
              <tr>
                <th>대리점명</th>
                <th>국가</th>
                <th>코드</th>
                <th>연락처</th>
                <th>이메일</th>
                <th>선택</th>
              </tr>
            </thead>
            <tbody>
              ${data.map(this._renderItem.bind(this)).join("")}
            </tbody>
          </table>
        </div>

        ${this._renderPagination(paginator)}
      </div>
    `;
  }

  _render() {
    super._render();
    this.form = this.qs("#dealer-search-form");

    // 국가 (버튼 검색 방식이므로 onChange 없음)
    SelectInput.make(this.refs.country, {
      label: "국가",
      name: "country",
      value: this._state.query.country || "",
      options: [
        { label: "전체", value: "" },
        ...Object.entries(this._state.countries).map(([value, label]) => ({
          label,
          value,
        })),
      ],
    }).render();

    // 정렬 (버튼 검색 방식)
    SelectInput.make(this.refs.sort, {
      label: "정렬",
      name: "sort",
      value: this._state.query.sort || "created_at_desc",
      options: [
        { label: "전체", value: "" },
        { label: "등록일 ↑", value: "created_at_asc" },
        { label: "등록일 ↓", value: "created_at_desc" },
        { label: "대리점명 ↑", value: "name_asc" },
        { label: "대리점명 ↓", value: "name_desc" },
        { label: "코드 ↑", value: "code_asc" },
        { label: "코드 ↓", value: "code_desc" },
      ],
    }).render();
  }

  _bindEvents() {
    // 선택 버튼
    const buttons = this.qsAll("button[data-dealer-row-id]");
    buttons.forEach((btn) => {
      this.on(btn, "click", this._handlePick.bind(this));
    });

    // 폼 submit으로 검색 (입력 즉시 필터링 X)
    if (this.form) {
      this.on(this.form, "submit", (e) => {
        e.preventDefault();
        this._handleSearch(this.form, e);
      });
    }

    // 초기화
    const resetButton = this.qs("#dealer-btn-reset");
    if (resetButton) {
      this.on(resetButton, "click", this._handleReset.bind(this));
    }

    // 페이지 이동
    const moveButtons = this.qsAll(".no-btn-move");
    moveButtons.forEach((btn) => {
      this.on(btn, "click", this._handleMove.bind(this));
    });

    // perpage 변경
    const perPageSelect = this.qs(".no-pagination__select");
    if (perPageSelect) {
      this.on(perPageSelect, "change", this._handlePerPage.bind(this));
    }

    // “검색” 버튼 클릭 → submit 위임(안전망)
    const submitBtn = this.qs("#dealer-btn-submit");
    if (submitBtn) {
      this.on(submitBtn, "click", (e) => {
        e.preventDefault();
        this._handleSearch(this.form, e);
      });
    }
  }

  /* ====== 테이블 렌더링 ====== */
  _renderItem(item = {}) {
    // 유연 파싱(User + dealer relation)
    const user = item;
    const dealer = user.dealer || {};
    const name = (user.name || dealer.name || "-");
    const code = (dealer.code || "");
    const email = (user.email || dealer.email || "-");
    const phone = (user.phone || dealer.phone || "-");
    const country = (dealer.country || "");
    const countryLabel = this._state.countries?.[country] ?? country ?? "-";

    const payload = {
      id: dealer.id || user.id || "",
      name,
      code,
      country,
      address: dealer.address || "",
      phone,
      email,
    };

    return `
      <tr>
        <td><span>${Helper.escapeHtml(name)}</span></td>
        <td><span>${Helper.escapeHtml(countryLabel)}</span></td>
        <td><span>${Helper.escapeHtml(code || "-")}</span></td>
        <td><span>${Helper.escapeHtml(phone || "-")}</span></td>
        <td><span>${Helper.escapeHtml(email || "-")}</span></td>
        <td>
          <div class="no-prod-attr-list__action">
            <button
              type="button"
              class="no-btn-primary-outline"
              data-dealer-row-id="${Helper.escapeHtml(String(payload.id))}"
              data-name="${Helper.escapeHtml(name)}"
              data-code="${Helper.escapeHtml(code)}"
              data-country="${Helper.escapeHtml(country)}"
              data-address="${Helper.escapeHtml(payload.address)}"
              data-phone="${Helper.escapeHtml(phone)}"
              data-email="${Helper.escapeHtml(email)}"
            >
              <span>선택</span>
            </button>
          </div>
        </td>
      </tr>
    `;
  }

  /* ====== 페이징 ====== */
  _renderPagination(p = {}) {
    const {
      from = 0,
      to = 0,
      total = 0,
      has_previous_page = false,
      has_next_page = false,
      current_page = 1,
      per_page = 15,
    } = p;

    return `
      <div class="no-pagination">
        <p class="no-pagination__text">Rows per page:</p>
        <div class="no-pagination__input">
          <select name="perpage" class="no-pagination__select">
            ${[15, 25, 50, 75, 100]
              .map(
                (v) => `<option value="${v}" ${v == per_page ? "selected" : ""}>${v}</option>`
              )
              .join("")}
          </select>
        </div>
        <div class="no-pagination__text">${from}-${to} of ${total}</div>
        <div class="no-pagination__btn">
          <button type="button" class="no-btn-move ${!has_previous_page ? "--disabled" : ""}" data-move="prev">
            <i class="fa-duotone fa-light fa-chevron-left"></i>
          </button>
          <button type="button" class="no-btn-move ${!has_next_page ? "--disabled" : ""}" data-move="next">
            <i class="fa-duotone fa-light fa-chevron-right"></i>
          </button>
        </div>
      </div>
    `;
  }

  /* ====== 이벤트 핸들러 ====== */
  _handlePick(btn, evt) {
    const id = btn.getAttribute("data-dealer-row-id");
    const dealer = {
      id,
      name: btn.getAttribute("data-name") || "",
      code: btn.getAttribute("data-code") || "",
      country: btn.getAttribute("data-country") || "",
      address: btn.getAttribute("data-address") || "",
      phone: btn.getAttribute("data-phone") || "",
      email: btn.getAttribute("data-email") || "",
    };
    this._dispatch("pick.dealer", { dealer });
  }

  _handleSearch(form, evt) {
    if (evt) evt.preventDefault();

    const formData = new FormData(this.form);
    const params = new URLSearchParams();

    for (const [key, value] of formData.entries()) {
      if (value) params.append(key, value);
    }
    // 페이지는 검색 시 1페이지로
    params.set("page", "1");

    this._dispatch("fetch.dealers", { query: params.toString() });
  }

  _handleReset() {
    const params = new URLSearchParams({
      page: 1,
      perpage: this._state?.paginator?.per_page || 15,
      name: "",
      country: "",
      code: "",
      phone: "",
      email: "",
      sort: "created_at_desc",
    });
    this._dispatch("fetch.dealers", { query: params.toString() });
  }

  _handleMove(btn, evt) {
    evt.preventDefault();
    if (btn.classList.contains("--disabled")) return;

    const move = btn.getAttribute("data-move"); // 'prev' | 'next'
    let page = Number(this._state?.paginator?.current_page || 1);

    if (move === "prev") page = Math.max(1, page - 1);
    if (move === "next") page += 1;

    const params = new URLSearchParams();
    const q = this._state.query || {};

    // 기존 쿼리 유지
    if (q.name) params.set("name", q.name);
    if (q.country) params.set("country", q.country);
    if (q.code) params.set("code", q.code);
    if (q.phone) params.set("phone", q.phone);
    if (q.email) params.set("email", q.email);
    if (q.sort) params.set("sort", q.sort);

    const perSelect = this.qs(".no-pagination__select");
    const perpage =
      (perSelect && perSelect.value) ||
      this._state.paginator?.per_page ||
      15;
    params.set("perpage", String(perpage));
    params.set("page", String(page));

    this._dispatch("fetch.dealers", { query: params.toString() });
  }

  _handlePerPage(select, evt) {
    const perpage = evt.currentTarget.value;

    const params = new URLSearchParams();
    const q = this._state.query || {};

    if (q.name) params.set("name", q.name);
    if (q.country) params.set("country", q.country);
    if (q.code) params.set("code", q.code);
    if (q.phone) params.set("phone", q.phone);
    if (q.email) params.set("email", q.email);
    if (q.sort) params.set("sort", q.sort);

    params.set("perpage", perpage);
    params.set("page", "1");

    this._dispatch("fetch.dealers", { query: params.toString() });
  }
}
