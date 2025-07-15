import ClaimController from '../../controllers/ClaimController';
import View from '../../core/View';
import Helper from '../../supports/Helper';

export default class CustomerZone extends View {

    _defineProps(){
        return {
            customer: {}
        }
    }

    _defineState(){
        return {
            ...this._props
        }
    }

   _template() {
        const customer = this._state.customer;

        if (Helper.isEmptyObject(customer)) {
            return `
                <div class="no-form-empty-fallback">
                    <p>선택된 고객이 없습니다. 고객을 선택해주세요.</p>
                </div>
            `;
        }

        const {
            name,
            country,
            phone,
            email,
            age,
            address,
            description
        } = customer;

        const countryLabel = ClaimController.attributes.countries[country] ?? country; 

        const hiddenInputs = `
            <input type="hidden" name="customer_name" value="${Helper.escapeHtml(name)}" />
            <input type="hidden" name="customer_email" value="${Helper.escapeHtml(email || '')}" />
            <input type="hidden" name="customer_phone" value="${Helper.escapeHtml(phone || '')}" />
            <input type="hidden" name="customer_country" value="${Helper.escapeHtml(country || '')}" />
            <input type="hidden" name="customer_address" value="${Helper.escapeHtml(address || '')}" />
            <input type="hidden" name="customer_description" value="${Helper.escapeHtml(description || '')}" />
        `;

        return `
            <div>
                ${hiddenInputs}
                <div class="no-customer-zone">
                    <div class="no-customer-info">
                        <div class="no-customer-info__item">
                            <span class="no-customer-info__item__label">이름</span>
                            <span class="no-customer-info__item__value">${name}</span>
                        </div>
                        <div class="no-customer-info__item">
                            <span class="no-customer-info__item__label">국가</span>
                            <span class="no-customer-info__item__value">${countryLabel || '-'}</span>
                        </div>
                        <div class="no-customer-info__item">
                            <span class="no-customer-info__item__label">연락처</span>
                            <span class="no-customer-info__item__value">${phone || '-'}</span>
                        </div>
                        <div class="no-customer-info__item">
                            <span class="no-customer-info__item__label">이메일</span>
                            <span class="no-customer-info__item__value">${email || '-'}</span>
                        </div>
                        <div class="no-customer-info__item">
                            <span class="no-customer-info__item__label">나이</span>
                            <span class="no-customer-info__item__value">${age !== null && age !== undefined ? age : '-'}</span>
                        </div>
                        <div class="no-customer-info__item">
                            <span class="no-customer-info__item__label">주소</span>
                            <span class="no-customer-info__item__value">${address || '-'}</span>
                        </div>
                        <div class="no-customer-info__item">
                            <span class="no-customer-info__item__label">설명</span>
                            <span class="no-customer-info__item__value">${description || '-'}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

    }

}
