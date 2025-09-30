import View from "../../core/View";
import Button from "../../shared/Button";
import SearchButton from "../Cart/SearchButton";
import Helper from "../../supports/Helper";

export default class CustomerPicker extends View {
  _defineProps() {
    return {
      customer: null,   // { id, name, email, phone, country, address, age, description }
      countries: {},    // { "KR": "Korea", ... }
      showReset: true,
    };
  }

  _defineState() {
    return { ...this._props };
  }

  _template() {
    return `
      <div class="no-page-row">
        <div class="no-page-head">
          <div class="no-page-head__between">
            <h2 class="no-heading-sm">고객</h2>
            <div data-ref="toolbar"></div>
          </div>
        </div>

        <div data-ref="search"></div>

        <div class="no-form-inner no-mt-12">
          <div class="no-form-group" data-ref="info"></div>
        </div>
      </div>
    `;
  }

  _render() {
    super._render();

    // 검색 버튼
    SearchButton.make(this.refs.search, {
      label: this._state.customer?.name ? `${this._state.customer.name} 변경` : "고객 선택",
      onClick: () => this._dispatch("fetch.customers", { view: this }),
    }).render();

    // 초기화 버튼
    if (this._props.showReset) {
      Button.make(this.refs.toolbar, {
        label: "초기화",
        className: "no-btn-success-outline --xs",
        onClick: () => this.setState({ customer: null }),
      }).render();
    }

    this._renderInfo();
  }

  _renderInfo() {
    const mount = this.refs.info;
    if (!mount) return;

    const c = this._state.customer;
    if (!c) {
      mount.innerHTML = `<div class="no-form-empty-fallback">선택된 고객이 없습니다.</div>`;
      return;
    }

    const countryLabel =
      (this._props.countries && this._props.countries[c.country]) || c.country || "-";

    // 요청한 hiddenInputs 포맷 그대로 사용
    const hiddenInputs = `
      <input type="hidden" name="customer_name" value="${Helper.escapeHtml(c.name || "")}" />
      <input type="hidden" name="customer_email" value="${Helper.escapeHtml(c.email || "")}" />
      <input type="hidden" name="customer_phone" value="${Helper.escapeHtml(c.phone || "")}" />
      <input type="hidden" name="customer_country" value="${Helper.escapeHtml(c.country || "")}" />
      <input type="hidden" name="customer_address" value="${Helper.escapeHtml(c.address || "")}" />
      <input type="hidden" name="customer_description" value="${Helper.escapeHtml(c.description || "")}" />
    `;

    mount.innerHTML = `
      <div>
        ${hiddenInputs}
        <div class="no-customer-zone">
          <div class="no-customer-info">
            <div class="no-customer-info__item">
              <span class="no-customer-info__item__label">이름</span>
              <span class="no-customer-info__item__value">${Helper.escapeHtml(c.name || "-")}</span>
            </div>
            <div class="no-customer-info__item">
              <span class="no-customer-info__item__label">국가</span>
              <span class="no-customer-info__item__value">${Helper.escapeHtml(countryLabel)}</span>
            </div>
            <div class="no-customer-info__item">
              <span class="no-customer-info__item__label">연락처</span>
              <span class="no-customer-info__item__value">${Helper.escapeHtml(c.phone || "-")}</span>
            </div>
            <div class="no-customer-info__item">
              <span class="no-customer-info__item__label">이메일</span>
              <span class="no-customer-info__item__value">${Helper.escapeHtml(c.email || "-")}</span>
            </div>
            <div class="no-customer-info__item">
              <span class="no-customer-info__item__label">나이</span>
              <span class="no-customer-info__item__value">${(c.age ?? "-")}</span>
            </div>
            <div class="no-customer-info__item">
              <span class="no-customer-info__item__label">주소</span>
              <span class="no-customer-info__item__value">${Helper.escapeHtml(c.address || "-")}</span>
            </div>
            <div class="no-customer-info__item">
              <span class="no-customer-info__item__label">설명</span>
              <span class="no-customer-info__item__value">${Helper.escapeHtml(c.description || "-")}</span>
            </div>
          </div>
        </div>
      </div>
    `;
  }
}
