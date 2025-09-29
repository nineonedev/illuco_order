// resources/assets/js/components/Loupe/FrameColorList.js
import View from '../../core/View';
import Ajax from '../../core/Ajax';

export default class FrameColorList extends View {
  _boot() {
    super._boot();
    this._ajax = this._ajax || new Ajax(true);
    this._csrf = this._getCsrfToken();
  }

  _defineProps() {
    return {
      colors: [],
      endpoints: { base: '/admin/loupe-frame-colors' },
    };
  }

  _defineState() {
    return {
      ...this._props,
    };
  }

  // tbody 안에 렌더하므로 행만 반환
  _template() {
    const rows = (this.state.colors || []).map((c) => this._rowTemplate(c)).join('');
    if (!rows) {
      return `<tbody><tr><td colspan="6" class="center no-text-secondary">등록된 컬러가 없습니다.</td></tr><tbody>`;
    }
    return `<tbody>${rows}</tbody>`;
  }

  _rowTemplate(c) {
    const id = c.id;
    const name = this._e(c.name || '');
    const code = this._e(c.code || '');
    const hex = this._e(c.hex || '#000000');
    const sortOrder = Number.isFinite(+c.sort_order) ? +c.sort_order : 0;
    const checked = c.is_active ? 'checked' : '';

    return `
      <tr data-id="${id}">
        <td style="display:none;">
          <form id="fc-${id}" action="${this._props.endpoints.base}/${id}" method="post" style="display:none;">
            <input type="hidden" name="_method" value="put">
            ${this._csrf ? `<input type="hidden" name="_token" value="${this._csrf}">` : ''}
          </form>
        </td>

        <!-- 이름 -->
        <td>
          <div class="no-form-control --sm" style="margin:0;">
            <label class="no-form-control-inner">
              <input type="text" name="name" class="no-form-control-input" value="${name}" form="fc-${id}">
              <fieldset class="no-form-control-label"><legend class="no-form-control-text">이름</legend></fieldset>
            </label>
          </div>
        </td>

        <!-- 코드 -->
        <td>
          <div class="no-form-control --sm" style="margin:0;">
            <label class="no-form-control-inner">
              <input type="text" name="code" class="no-form-control-input" value="${code}" form="fc-${id}">
              <fieldset class="no-form-control-label"><legend class="no-form-control-text">코드</legend></fieldset>
            </label>
          </div>
        </td>

        <!-- HEX (+ 피커) -->
        <td>
          <div class="no-form-control --sm" style="margin:0;display:flex;gap:.5rem;align-items:center;">
            <label class="no-form-control-inner" style="flex:1;">
              <input type="text" name="hex" class="no-form-control-input" value="${hex}" form="fc-${id}" placeholder="#000000">
              <fieldset class="no-form-control-label"><legend class="no-form-control-text">HEX</legend></fieldset>
            </label>
            <input type="color" data-role="picker" value="${hex || '#000000'}" title="색상 선택" style="width:42px;height:28px;border:none;background:transparent;">
          </div>
        </td>

        <!-- 순서 -->
        <td>
          <div class="no-form-control --sm" style="margin:0;max-width:110px;">
            <label class="no-form-control-inner">
              <input type="number" name="sort_order" class="no-form-control-input" value="${sortOrder}" step="1" inputmode="numeric" form="fc-${id}">
              <fieldset class="no-form-control-label"><legend class="no-form-control-text">순서</legend></fieldset>
            </label>
          </div>
        </td>

        <!-- 사용 -->
        <td class="center">
          <div class="no-form-checkbox --sm" style="justify-content:center;">
            <label class="no-form-checkbox-pointer">
              <input type="checkbox" name="is_active" value="1" class="no-form-checkbox-input" form="fc-${id}" ${checked}>
              <div class="no-form-checkbox-ripple">
                <span class="no-form-checkbox-box"><div class="no-form-checkbox-icon"><i class="fa-solid fa-check"></i></div></span>
              </div>
            </label>
          </div>
        </td>

        <!-- 작업 -->
        <td class="no-table-action">
          <div class="no-page-index-table__action no-prod-attr-list__action">
            <button type="button" data-action="save" data-id="${id}" class="no-btn-primary-outline --xs"><span>저장</span></button>
            <button type="button" data-action="delete" data-id="${id}" class="no-btn-error-outline --xs"><span>삭제</span></button>
          </div>
        </td>
      </tr>
    `;
  }

  _render() {
    super._render();
    this._bindRows();
  }

  _bindRows() {
    const tbody = this.$el;
    if (!tbody) return;

    // HEX <-> color picker 동기화
    tbody.querySelectorAll('tr').forEach((tr) => {
      const picker = tr.querySelector('input[data-role="picker"]');
      const hexInput = tr.querySelector('input[name="hex"]');
      if (picker && hexInput) {
        picker.addEventListener('input', () => {
          hexInput.value = picker.value.toUpperCase();
        });
        hexInput.addEventListener('input', () => {
          const v = (hexInput.value || '').trim();
          if (/^#([0-9A-Fa-f]{6})$/.test(v)) picker.value = v;
        });
      }
    });

    // 저장/삭제 이벤트
    tbody.querySelectorAll('button[data-action="save"]').forEach((btn) => {
      btn.addEventListener('click', (e) => this._onSave(e));
    });
    tbody.querySelectorAll('button[data-action="delete"]').forEach((btn) => {
      btn.addEventListener('click', (e) => this._onDelete(e));
    });
  }

  async _onSave(e) {
    const id = e.currentTarget.dataset.id;
    const tr = this.$el.querySelector(`tr[data-id="${id}"]`);
    if (!tr) return;

    const name = tr.querySelector('input[name="name"]')?.value?.trim() || '';
    const code = tr.querySelector('input[name="code"]')?.value?.trim() || '';
    const hex  = tr.querySelector('input[name="hex"]')?.value?.trim()  || '';
    const sortEl = tr.querySelector('input[name="sort_order"]');
    let sortOrder = parseInt((sortEl?.value ?? '').toString(), 10);
    if (isNaN(sortOrder)) sortOrder = 0;
    const isActive = tr.querySelector('input[name="is_active"]')?.checked ? 1 : 0;

    // 간단 검증
    if (!name) { alert('이름을 입력하세요.'); return; }
    if (hex && !/^#([0-9A-Fa-f]{6})$/.test(hex)) { alert('HEX는 #RRGGBB 형식이어야 합니다.'); return; }
    if (code && !/^[a-z0-9][a-z0-9._-]*$/.test(code)) { alert('코드는 소문자/숫자/.-_ 만 허용합니다.'); return; }

    const payload = new URLSearchParams();
    payload.set('name', name);
    if (code !== '') payload.set('code', code);
    if (hex !== '')  payload.set('hex', hex.toUpperCase());
    payload.set('sort_order', String(sortOrder));
    payload.set('is_active', String(isActive));

    try {
      e.currentTarget.disabled = true;
      const res = await this._ajax.put(`${this._props.endpoints.base}/${id}`, payload);
      const { success, message } = res || {};
      alert(message || (success ? '저장되었습니다.' : '저장에 실패했습니다.'));
      if (success) {
        const idx = this.state.colors.findIndex((c) => String(c.id) === String(id));
        if (idx >= 0) {
          this.state.colors[idx] = {
            ...this.state.colors[idx],
            name,
            code,
            hex: hex.toUpperCase(),
            sort_order: sortOrder,
            is_active: isActive,
          };
        }
      }
    } catch (err) {
      console.error(err);
      alert('요청 중 오류가 발생했습니다.');
    } finally {
      e.currentTarget.disabled = false;
    }
  }

  async _onDelete(e) {
    const id = e.currentTarget.dataset.id;
    if (!confirm('정말 삭제하시겠습니까?')) return;

    try {
      e.currentTarget.disabled = true;
      const res = await this._ajax.delete(`${this._props.endpoints.base}/${id}`);
      const { success, message } = res || {};
      alert(message || (success ? '삭제되었습니다.' : '삭제에 실패했습니다.'));
      if (success) {
        this.state.colors = this.state.colors.filter((c) => String(c.id) !== String(id));
        this._update(); // re-render
      }
    } catch (err) {
      console.error(err);
      alert('요청 중 오류가 발생했습니다.');
    } finally {
      e.currentTarget.disabled = false;
    }
  }

  // ---------------------------
  // utils
  // ---------------------------
  _getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta?.content) return meta.content;
    const anyToken = document.querySelector('input[name="_token"]');
    return anyToken?.value || '';
  }

  _e(s) {
    return (s ?? '')
      .toString()
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }
}
