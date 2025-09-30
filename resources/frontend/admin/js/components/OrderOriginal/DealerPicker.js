import View from "../../core/View";
import Button from "../../shared/Button";
import SearchButton from "../Cart/SearchButton"; // Cart/Template에서 쓰던 컴포넌트 재사용
import Helper from "../../supports/Helper";

export default class DealerPicker extends View {
  _defineProps() {
    return {
      dealer: null,   // { id, name, code, country, address, phone, email, memo? }
      countries: {},  // { "KR": "Korea", ... }
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
            <h2 class="no-heading-sm">대리점</h2>
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
      label: this._state.dealer?.name ? `${this._state.dealer.name} 변경` : "대리점 선택",
      onClick: () => this._dispatch("fetch.dealers", { view: this }),
    }).render();

    // 초기화 버튼
    if (this._props.showReset) {
      Button.make(this.refs.toolbar, {
        label: "초기화",
        className: "no-btn-success-outline --xs",
        onClick: () => this.setState({ dealer: null }),
      }).render();
    }

    this._renderInfo();
  }

  _renderInfo() {
    const mount = this.refs.info;
    if (!mount) return;

    const d = this._state.dealer;
    if (!d) {
      mount.innerHTML = `<div class="no-form-empty-fallback">선택된 대리점이 없습니다.</div>`;
      return;
    }

    const countryLabel =
      (this._props.countries && this._props.countries[d.country]) || d.country || "-";

    const hidden = `
      <input type="hidden" name="dealer_name" value="${Helper.escapeHtml(d.name || "")}" />
      <input type="hidden" name="dealer_code" value="${Helper.escapeHtml(d.code || "")}" />
      <input type="hidden" name="dealer_country" value="${Helper.escapeHtml(d.country || "")}" />
      <input type="hidden" name="dealer_address" value="${Helper.escapeHtml(d.address || "")}" />
      <input type="hidden" name="dealer_phone" value="${Helper.escapeHtml(d.phone || "")}" />
      <input type="hidden" name="dealer_email" value="${Helper.escapeHtml(d.email || "")}" />
      <input type="hidden" name="dealer_memo" value="${Helper.escapeHtml(d.memo || "")}" />
    `;

    mount.innerHTML = `
      <div>
        ${hidden}
        <div class="no-dealer-zone">
          <div class="no-dealer-info">
            <div class="no-dealer-info__item">
              <span class="no-dealer-info__item__label">이름</span>
              <span class="no-dealer-info__item__value">${Helper.escapeHtml(d.name || "-")}</span>
            </div>
            <div class="no-dealer-info__item">
              <span class="no-dealer-info__item__label">코드</span>
              <span class="no-dealer-info__item__value">${Helper.escapeHtml(d.code || "-")}</span>
            </div>
            <div class="no-dealer-info__item">
              <span class="no-dealer-info__item__label">국가</span>
              <span class="no-dealer-info__item__value">${Helper.escapeHtml(countryLabel)}</span>
            </div>
            <div class="no-dealer-info__item">
              <span class="no-dealer-info__item__label">연락처</span>
              <span class="no-dealer-info__item__value">${Helper.escapeHtml(d.phone || "-")}</span>
            </div>
            <div class="no-dealer-info__item">
              <span class="no-dealer-info__item__label">이메일</span>
              <span class="no-dealer-info__item__value">${Helper.escapeHtml(d.email || "-")}</span>
            </div>
            <div class="no-dealer-info__item">
              <span class="no-dealer-info__item__label">주소</span>
              <span class="no-dealer-info__item__value">${Helper.escapeHtml(d.address || "-")}</span>
            </div>
            <div class="no-dealer-info__item">
              <span class="no-dealer-info__item__label">메모</span>
              <span class="no-dealer-info__item__value">${Helper.escapeHtml(d.memo || "-")}</span>
            </div>
          </div>
        </div>
      </div>
    `;
  }
}
