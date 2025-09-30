import View from "../../core/View";
import Helper from "../../supports/Helper";
import SelectInput from "../Inputs/SelectInput";

export default class CustomerSelection extends View {
  _defineProps() {
    return {
      paginator: {},
      query: {
        search: "",
        country: "",
        dealer_id: "",
        sort: "created_at_desc",
      },
      countries: {},
      dealers: [],
    };
  }

  _defineState() {
    return {
      ...this._props,
    };
  }

  _template() {
    const { paginator, query } = this._state;
    const { data } = paginator;

    return `
      <div>
        <form id="search-form" class="no-page-search-form">
          <div class="no-page-search-form__actions">
            <button type="button" class="no-btn-success --xs" id="btn-reset">초기화</button>
            <button type="submit" class="no-btn-primary --xs" id="btn-submit">검색</button>
          </div>

          <div class="no-page-search-form-row">
            <div class="no-form-search">
              <label for="query" class="no-form-label">검색</label>
              <div class="no-form-search-inner">
                <div class="no-form-search__icon">
                  <i class="fa-light fa-magnifying-glass"></i>
                </div>
                <input
                  type="search"
                  name="search"
                  id="query"
                  class="no-form-search-input"
                  placeholder="이름, 이메일, 연락처"
                  value="${query.search ?? ""}"
                />
              </div>
            </div>

            <div data-ref="country"></div>
            ${this._state.dealers.length > 0 ? `<div data-ref="dealer"></div>` : ``}
            <div data-ref="sort"></div>
          </div>
        </form>

        <div class="no-page-index-table-outer">
          <table class="no-page-index-table">
            <thead>
              <tr>
                <th>이름</th>
                <th>국가</th>
                <th>이메일</th>
                <th>연락처</th>
                <th>관리</th>
              </tr>
            </thead>
            <tbody>${data.map(this._renderItem.bind(this)).join("")}</tbody>
          </table>
        </div>

        ${this._renderPagination(paginator)}
      </div>
    `;
  }

  _render() {
    super._render();

    this.form = this.qs("#search-form");

    console.log(this.form);
    

    // 국가 셀렉트 (버튼 검색 방식: onChange 없음)
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

    // 대리점 셀렉트 (존재할 때만, onChange 없음)
    if (this._state.dealers.length > 0) {
      SelectInput.make(this.refs.dealer, {
        label: "대리점",
        name: "dealer_id",
        value: this._state.query.dealer_id || "",
        options: [
          { label: "전체", value: "" },
          ...this._state.dealers.map((d) => ({
            label: d.user?.name || d.name,
            value: d.id,
          })),
        ],
      }).render();
    }

    // 정렬 셀렉트 (버튼 검색 방식: onChange 없음)
    SelectInput.make(this.refs.sort, {
      label: "정렬",
      name: "sort",
      value: this._state.query.sort || "",
      options: [
        { label: "최신순", value: "created_at_desc" },
        { label: "오래된순", value: "created_at_asc" },
        { label: "이름 오름차순", value: "name_asc" },
        { label: "이름 내림차순", value: "name_desc" },
        { label: "이메일 오름차순", value: "email_asc" },
        { label: "이메일 내림차순", value: "email_desc" },
      ],
    }).render();
  }

  _renderPagination(paginator) {
    const {
      from = 0,
      to = 0,
      total = 0,
      has_previous_page = false,
      has_next_page = false,
      current_page = 1,
      per_page = 15,
    } = paginator;

    return `
      <div class="no-pagination">
        <p class="no-pagination__text">Rows per page:</p>
        <div class="no-pagination__input">
          <select name="perpage" class="no-pagination__select">
            ${[15, 25, 50, 75, 100]
              .map(
                (v) => `
              <option value="${v}" ${v == per_page ? "selected" : ""}>${v}</option>
            `
              )
              .join("")}
          </select>
        </div>
        <div class="no-pagination__text">
          ${from}-${to} of ${total}
        </div>
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

  _renderItem({ id, name, country, email, phone } = {}) {
    const countryLabel = this._state.countries[country];

    return `
      <tr>
        <td><span>${name}</span></td>
        <td><span>${countryLabel ?? country}</span></td>
        <td><span>${email}</span></td>
        <td><span>${phone || "-"}</span></td>
        <td>
          <div class="no-prod-attr-list__action">
            <button
              type="button"
              class="no-btn-primary-outline"
              data-row-id="${id}">
              <span>선택</span>
            </button>
          </div>
        </td>
      </tr>
    `;
  }

  _bindEvents() {
    // 선택 버튼
    const buttons = this.qsAll("button[data-row-id]");
    buttons.forEach((btn) => {
        this.on(btn, "click", this._handleClick.bind(this));
    });
    
    if (this.form) {
        this.on(this.form, "submit", (e) => {
            e.preventDefault();
            this._handleSearch(this.form, e);
        });
    }

    const moveButtons = this.qsAll(".no-btn-move");
    moveButtons.forEach((btn) => {
        this.on(btn, "click", this._handleMove.bind(this));
    });

    const perPageSelect = this.qs(".no-pagination__select");
    if (perPageSelect) {
        this.on(perPageSelect, "change", this._handlePerPage.bind(this));
    }

    const resetButton = this.qs("#btn-reset");
    if (resetButton) {
        this.on(resetButton, "click", this._handleReset.bind(this));
    }

    const submitBtn = this.qs("#btn-submit");
    if (submitBtn) {
        this.on(submitBtn, "click", this._handleSearch.bind(this));
    }
  }

  _handleMove(button, evt) {
    evt.preventDefault();
    if (button.classList.contains("--disabled")) return;

    const move = button.getAttribute("data-move");
    let page = parseInt(this._state.paginator.current_page || 1, 10);

    if (move === "prev") page = Math.max(1, page - 1);
    if (move === "next") page += 1;

    const params = new URLSearchParams();

    // 기존 쿼리 유지(서버에서 돌려준 query 기준)
    if (this._state.query.search) params.append("search", this._state.query.search);
    if (this._state.query.country) params.append("country", this._state.query.country);
    if (this._state.query.dealer_id) params.append("dealer_id", this._state.query.dealer_id);
    if (this._state.query.sort) params.append("sort", this._state.query.sort);

    // perpage 유지
    const perSelect = this.qs(".no-pagination__select");
    const perpage = (perSelect && perSelect.value) || this._state.paginator.per_page || 15;
    params.append("perpage", String(perpage));

    // 이동할 페이지
    params.append("page", String(page));

    this._dispatch("fetch.customers", {
      query: params.toString(),
    });
  }

  _handlePerPage(select, evt) {
    const perpage = select.value;

    const params = new URLSearchParams();

    if (this._state.query.search) params.append("search", this._state.query.search);
    if (this._state.query.country) params.append("country", this._state.query.country);
    if (this._state.query.dealer_id) params.append("dealer_id", this._state.query.dealer_id);
    if (this._state.query.sort) params.append("sort", this._state.query.sort);

    params.append("perpage", perpage);
    params.append("page", "1");

    this._dispatch("fetch.customers", {
      query: params.toString(),
    });
  }

  _handleSearch(form, evt) {
    if (evt) evt.preventDefault();

    const formData = new FormData(this.form);
    const params = new URLSearchParams();

    for (const [key, value] of formData.entries()) {
      if (value) {
        params.append(key, value);
      }
    }

    // 검색 시 1페이지부터
    params.set("page", "1");

    this._dispatch("fetch.customers", {
      query: params.toString(),
    });
  }

  _handleReset() {
    const params = new URLSearchParams({
      perpage: this._state.paginator.per_page || 15,
      page: 1,
      search: "",
      country: "",
      dealer_id: "",
      sort: "created_at_desc",
    });

    this._dispatch("fetch.customers", {
      query: params.toString(),
    });
  }

  _handleClick(form, evt) {
    const id = evt.currentTarget.getAttribute("data-row-id");
    const customer = this._state.paginator.data.find((item) => item.id === +id);
    this._dispatch("pick.customer", { customer });
  }
}
