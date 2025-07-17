
import View from '../../core/View';
import Helper from '../../supports/Helper';

export default class ProductZone extends View {

    _defineProps(){
        return {
            template: {}
        }
    }

    _defineState(){
        return {
            ...this._props
        }
    }

   _template() {
        const template = this._state.template;

        if (Helper.isEmptyObject(template)) {
            return `
                <div class="no-form-empty-fallback">
                    <p>선택된 제품이 없습니다. 제품을 선택해주세요.</p>
                </div>
            `;
        }

        const {
            name,
            code,
            description,
            model,
            price,
            category,
            fileattachment:files
        } = template;

        const fileImage = files
            ? files.find((file) => file.file_key === "main_image")
            : null;
        const mainImage = fileImage
            ? fileImage.upload_path
            : "/static/app/img/meta/thumb.jpg";

        const hiddenInputs = `
            <input type="hidden" name="product[name]" value="${Helper.escapeHtml(name)}" />
            <input type="hidden" name="product[code]" value="${Helper.escapeHtml(code)}" />
            <input type="hidden" name="product[model]" value="${Helper.escapeHtml(model)}" />
            <input type="hidden" name="product[price]" value="${price}" />
            <input type="hidden" name="product[description]" value="${Helper.escapeHtml(description || '')}" />
        `;
        

        return `
            <div>
                ${hiddenInputs}
                <div class="no-product-zone">
                    <figure class="no-product-zone-img">
                        <img src="${mainImage}" alt="${name}" />
                    </figure>
                    <div class="no-product-zone-content">
                        <div class="no-product-info">
                            <span class="no-product-info__label">제품명</span>
                            <span class="no-product-info__value">${name}</span>
                        </div>
                        <div class="no-product-info">
                            <span class="no-product-info__label">모델명</span>
                            <span class="no-product-info__value">${model}</span>
                        </div>
                        <div class="no-product-info">
                            <span class="no-product-info__label">코드</span>
                            <span class="no-product-info__value">${code}</span>
                        </div>
                        <div class="no-product-info">
                            <span class="no-product-info__label">가격</span>
                            <span class="no-product-info__value">${price ? Helper.formatCurrency(price) : '-'}</span>
                        </div>
                        <div class="no-product-info">
                            <span class="no-product-info__label">카테고리</span>
                            <span class="no-product-info__value">${category?.label ?? '-'}</span>
                        </div>
                        <div class="no-product-info">
                            <span class="no-product-info__label">설명</span>
                            <span class="no-product-info__value">${description || '-'}</span>
                        </div>
                    <div>
                </div>
            </div>
        `;
    }

}