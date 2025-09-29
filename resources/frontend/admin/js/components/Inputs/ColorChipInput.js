// resources/assets/js/components/Form/MultiSelectInput.js
import View from "../../core/View";

export default class ColorChipInput extends View {
  _defineProps() {
    return {
      name: "multiselect",
      label: "다중 선택",
      value: [],                    // ['kr','us']
      options: [],                  // [{ value:'kr', label:'대한민국', customProperties:{icon:'🇰🇷'} }, ...]
      fallback: false,
      disabled: false,
      readOnly: false,
      required: false,
      invalid: false,
      invalidMessage: "",
      spacing: true,
      onChange: () => {},
      // 선택: Choices 옵션 일부 전달하고 싶을 때
      choicesOptions: {},
    };
  }

  _defineState() { return { ...this._props }; }

  _template() {
    const { label, name, options, fallback, disabled, required } = this._props;
    const nodeId = this._generateElementId();

    return `
      <div class="no-form-control --md">
        <label for="${nodeId}" class="no-form-control-inner">
          <span class="no-form-label">${label}</span>
          <select
            id="${nodeId}"
            name="${name}[]"
            data-ref="select"
            class="no-form-control-input"
            ${disabled ? "disabled" : ""}
            ${required ? "required" : ""}
            multiple
          >
            ${fallback ? `<option value="">-- 선택 --</option>` : ""}
            ${this._renderOptions(options)}
          </select>
        </label>
        <span class="no-form-control-space"></span>
        ${this._props.invalid && this._props.invalidMessage
          ? `<p class="no-form-invalid">${this._e(this._props.invalidMessage)}</p>`
          : ""}
      </div>
    `;
  }

  _renderOptions(options) {
    const selectedValues = Array.isArray(this._state.value) ? this._state.value : [];
    return (options || [])
      .map((opt) => {
        const value = typeof opt === "string" ? opt : opt.value;
        const label = typeof opt === "string" ? opt : opt.label;
        const selected = selectedValues.includes(value) ? "selected" : "";
        // Choices 템플릿에서 쓸 임의 속성 전달 (선택)
        const custom = (opt && opt.customProperties) ? 
          ` data-custom-properties='${this._e(JSON.stringify(opt.customProperties))}'` : "";
        return `<option value="${this._e(value)}" ${selected}${custom}>${this._e(label)}</option>`;
      })
      .join("");
  }

  _bindEvents() {
    const selectEl = this.refs.select;

    // 기본 옵션 + 커스텀 템플릿
    const instance = new Choices(selectEl, {
      removeItemButton: false,         // 커스텀 item 템플릿에서 자체 제거 버튼 사용
      shouldSort: false,
      itemSelectText: "선택",
      allowHTML: true,                 // 템플릿에 HTML 허용
      // (선택) 클래스 네이밍을 내부 프레임워크 스타일로 맞춤
      classNames: {
        containerOuter: 'no-choices',
        containerInner: 'no-choices__inner',
        input: 'no-choices__input',
        list: 'no-choices__list',
        listDropdown: 'no-choices__list--dropdown',
        item: 'no-choices__item',
        button: 'no-choices__button',
        activeState: 'is-active',
        selectedState: 'is-selected',
        highlightedState: 'is-highlighted',
        openState: 'is-open',
        disabledState: 'is-disabled',
        flippedState: 'is-flipped',
        loadingState: 'is-loading',
        focusState: 'is-focused',
        hiddenState: 'is-hidden',
      },
      // 🔧 여기서 렌더 HTML 커스텀
      callbackOnCreateTemplates: (template) => {
        // 주의) 함수 시그니처: (classNames, data) 순서
        return {
          // 선택된 항목(태그) 렌더
          item: (classNames, data) => {
            // option의 data-custom-properties를 Choices가 data.customProperties로 전달
            const cp = data.customProperties || {};
            const icon = cp.icon ? `<span class="no-chip__icon">${this._e(cp.icon)}</span>` : '';
            return template(`
              <span
                class="no-chip ${data.highlighted ? 'is-highlighted' : ''} ${data.disabled ? 'is-disabled' : ''}"
                data-item
                data-id="${data.id}"
                data-value="${this._e(data.value)}"
                ${data.active ? 'aria-selected="true"' : ''}
                ${data.disabled ? 'aria-disabled="true"' : ''}
                >
                ${icon}
                <span class="no-chip__label">${this._e(data.label)}</span>
                <button type="button"
                  class="no-chip__remove"
                  data-button
                  aria-label="Remove item: ${this._e(data.label)}">×</button>
              </span>
            `);
          },
          // 드롭다운 옵션 렌더
          choice: (classNames, data) => {
            const cp = data.customProperties || {};
            const icon = cp.icon ? `<span class="no-option__icon">${this._e(cp.icon)}</span>` : '';
            return template(`
              <div
                class="no-option ${data.disabled ? 'is-disabled' : ''}"
                data-choice
                ${data.disabled ? 'data-choice-disabled aria-disabled="true"' : 'data-choice-selectable'}
                data-id="${data.id}"
                data-value="${this._e(data.value)}"
                ${data.groupId > 0 ? `data-group-id="${data.groupId}"` : ''}
                >
                ${icon}
                <span class="no-option__label">${this._e(data.label)}</span>
              </div>
            `);
          },
          // (선택) 입력 필드/드롭다운 wrapper도 바꾸고 싶으면 아래처럼 추가 가능
          // containerOuter: (classNames, dir, isSelect, isDisabled) => template(`...`),
          // dropdown: (classNames) => template(`...`),
        };
      },
      ...this._props.choicesOptions, // 외부에서 추가 옵션 주입 가능
    });

    // 초기 선택값 반영(이미 option selected로 렌더했지만 보정)
    instance.setChoiceByValue(this._state.value);

    // change 이벤트 브릿지
    if (typeof this._props.onChange === "function") {
      selectEl.addEventListener("change", (e) => {
        const selectedOptions = Array.from(selectEl.selectedOptions).map((opt) => opt.value);
        this._props.onChange({ value: selectedOptions, view: this }, e);
      });
    }

    // 인스턴스 보관(필요시 destroy)
    this._choices = instance;
  }

  // (선택) 뷰가 파괴될 때 Choices 정리
  destroy() {
    try { this._choices?.destroy?.(); } catch(e) {}
    super.destroy?.();
  }

  _e(s) {
    return (s ?? '').toString()
      .replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;')
      .replace(/'/g,'&#39;');
  }
}
