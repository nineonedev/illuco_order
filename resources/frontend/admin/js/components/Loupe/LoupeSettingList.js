// resources/assets/js/components/Loupe/LoupeSettingList.js
import View from '../../core/View';
import Ajax from '../../core/Ajax';
import MultiSelectInput from '../Inputs/MultiSelectInput';

export default class LoupeSettingList extends View {
  _boot() {
    super._boot();
    this._ajax = this._ajax || new Ajax(true);
    this._csrf = this._getCsrfToken();
    this._msi = {}; // rowId -> MultiSelectInput instance
  }

  _defineProps() {
    return {
      settings: [],   // [{ id, wd_min, ..., frame_color(json|array), template:{...} }]
      colors: [],     // [{ id, name, hex }]
      endpoints: { base: '/admin/loupe-settings' },
    };
  }

  _defineState() {
    return {
      settings: this._props.settings || [],
      colors: this._props.colors || [],
      loading: false,
    };
  }

  // tbody 내부에 '행'만 렌더
  _template() {
    const rows = (this.state.settings || []).map((s) => this._rowTemplate(s)).join('');
    if (!rows) {
      return `<tr><td colspan="8" class="center no-text-secondary">등록된 세팅이 없습니다.</td></tr>`;
    }
    return rows;
  }

  _rowTemplate(s) {
    const id = s.id;
    const tpl = s.template || {};
    const img = (tpl.fileattachment && tpl.fileattachment[0]?.upload_path) || '';
    const name = this._e(tpl.name || '-');

    // frame_color → 배열화
    let checkedIds = [];
    if (Array.isArray(s.frame_color)) checkedIds = s.frame_color.map((v) => parseInt(v, 10));
    else if (typeof s.frame_color === 'string' && s.frame_color.trim()) {
      try { checkedIds = JSON.parse(s.frame_color); } catch (_) { checkedIds = []; }
      checkedIds = Array.isArray(checkedIds) ? checkedIds.map((v) => parseInt(v, 10)) : [];
    }

    const num = (v) => (v === null || v === undefined ? '' : this._e(String(v)));

    return `
      <tr data-id="${id}">
        <td>
          <div class="no-img-label">
            <div class="no-img-label__image">
              ${img ? `<img src="${this._e(img)}" alt="">` : `<div class="no-img-label__image --placeholder"></div>`}
            </div>
            <span class="no-img-label__label">${name}</span>
          </div>
        </td>

        <!-- WD -->
        <td>
          <div class="loupe-setting__range">
            <input class="no-form-base-input --xs" data-role="wd_min" type="number" step="0.1" value="${num(s.wd_min)}">
            <span class="loupe-setting__sep">~</span>
            <input class="no-form-base-input --xs" data-role="wd_max" type="number" step="0.1" value="${num(s.wd_max)}">
          </div>
        </td>

        <!-- VD -->
        <td>
          <div class="loupe-setting__range">
            <input class="no-form-base-input --xs" data-role="vd_min" type="number" step="0.1" value="${num(s.vd_min)}">
            <span class="loupe-setting__sep">~</span>
            <input class="no-form-base-input --xs" data-role="vd_max" type="number" step="0.1" value="${num(s.vd_max)}">
          </div>
        </td>

        <!-- far PD RIGHT -->
        <td>
          <div class="loupe-setting__range">
            <input class="no-form-base-input --xs" data-role="fd_right_min" type="number" step="0.1" value="${num(s.fd_right_min)}">
            <span class="loupe-setting__sep">~</span>
            <input class="no-form-base-input --xs" data-role="fd_right_max" type="number" step="0.1" value="${num(s.fd_right_max)}">
          </div>
        </td>

        <!-- far PD LEFT -->
        <td>
          <div class="loupe-setting__range">
            <input class="no-form-base-input --xs" data-role="fd_left_min" type="number" step="0.1" value="${num(s.fd_left_min)}">
            <span class="loupe-setting__sep">~</span>
            <input class="no-form-base-input --xs" data-role="fd_left_max" type="number" step="0.1" value="${num(s.fd_left_max)}">
          </div>
        </td>

        <!-- TOTAL PD -->
        <td>
          <input class="no-form-base-input --xs" data-role="fd_total_distance" type="number" step="0.1" value="${num(s.fd_total_distance)}">
        </td>

        <!-- 프레임 컬러: MultiSelect 컨테이너 -->
        <td>
          <div id="msi-${id}" class="loupe-setting__color-select"></div>
        </td>

        <!-- 작업 -->
        <td class="no-table-action">
          <div class="no-page-index-table__action no-prod-attr-list__action">
            <button type="button" class="no-btn-primary-outline --xs" data-action="save" data-id="${id}"><span>저장</span></button>
            <button type="button" class="no-btn-error-outline --xs" data-action="delete" data-id="${id}"><span>삭제</span></button>
          </div>
        </td>
      </tr>
    `;
  }

  _render() {
    super._render();
    this._mountColorSelects();   // MultiSelectInput 마운트
    this._bindRows();            // 버튼 이벤트
  }

  _mountColorSelects() {
    
  (this.state.settings || []).forEach((s) => {
    const id = s.id;
    const hookId = `msi-${id}`;
    const hook = document.getElementById(hookId);
    if (!hook || this._msi[id]) return;

    // 초기 선택값
    let selected = [];
    if (Array.isArray(s.frame_color)) {
      selected = s.frame_color.map(String);
    } else if (typeof s.frame_color === 'string' && s.frame_color.trim()) {
      try {
        selected = JSON.parse(s.frame_color);
      } catch (_) {
        selected = [];
      }
      selected = Array.isArray(selected) ? selected.map(String) : [];
    }

    // 옵션 변환
    const options = (this.state.colors || []).map((c) => ({
      value: String(c.id),
      label: c.name || `#${c.id}`,
    }));

    // MultiSelectInput 사용 (위 컴포넌트 API에 맞춰 props 정리)
    const view = MultiSelectInput.make(hookId, {
      name: 'frame_color_ids',
      label: '',
      value: selected,
      options,
      fallback: false,
      disabled: false,
      readOnly: false,
      required: false,
      invalid: false,
      spacing: true,
      onChange: () => {
        // 저장 시 DOM에서 읽을 예정이라 여기서는 생략
      },
    });

    console.log(view);
    

    this._msi[id] = view.render();
  });
}


  _bindRows() {
    const tbody = this.$el;
    if (!tbody) return;

    tbody.querySelectorAll('button[data-action="save"]').forEach((b) =>
      b.addEventListener('click', (e) => this._onSave(e))
    );
    tbody.querySelectorAll('button[data-action="delete"]').forEach((b) =>
      b.addEventListener('click', (e) => this._onDelete(e))
    );
  }

  // ========== Actions ==========
  async _onSave(e) {
    const id = e.currentTarget.dataset.id;
    const tr = this.$el.querySelector(`tr[data-id="${id}"]`);
    if (!tr) return;

    const D = (sel) => tr.querySelector(sel);

    const wd_min = this._numVal(D('[data-role="wd_min"]'));
    const wd_max = this._numVal(D('[data-role="wd_max"]'));
    const vd_min = this._numVal(D('[data-role="vd_min"]'));
    const vd_max = this._numVal(D('[data-role="vd_max"]'));
    const fr_min = this._numVal(D('[data-role="fd_right_min"]'));
    const fr_max = this._numVal(D('[data-role="fd_right_max"]'));
    const fl_min = this._numVal(D('[data-role="fd_left_min"]'));
    const fl_max = this._numVal(D('[data-role="fd_left_max"]'));
    const total  = this._numVal(D('[data-role="fd_total_distance"]'));

    // 검증
    if (!this._ensureMinMax(wd_min, wd_max, 'WD')) return;
    if (!this._ensureVD(vd_min, vd_max)) return;
    if (!this._ensureMinMax(fr_min, fr_max, 'far PD RIGHT')) return;
    if (!this._ensureMinMax(fl_min, fl_max, 'far PD LEFT')) return;
    if (this._isNum(fr_min) && this._isNum(fl_min) && Math.abs(fr_min - fl_min) > 2) {
      if (!confirm('좌/우 편차가 2mm를 초과합니다. 계속하시겠습니까?')) return;
    }

    // ✅ MultiSelectInput에서 선택값 수집
    const select = tr.querySelector('select[name="frame_color_ids[]"]');
    const checked = select
      ? Array.from(select.selectedOptions).map((opt) => opt.value)
      : [];

    const payload = new URLSearchParams();
    if (this._isNum(wd_min)) payload.set('wd_min', String(wd_min));
    if (this._isNum(wd_max)) payload.set('wd_max', String(wd_max));
    if (this._isNum(vd_min)) payload.set('vd_min', String(vd_min));
    if (this._isNum(vd_max)) payload.set('vd_max', String(vd_max));
    if (this._isNum(fr_min)) payload.set('fd_right_min', String(fr_min));
    if (this._isNum(fr_max)) payload.set('fd_right_max', String(fr_max));
    if (this._isNum(fl_min)) payload.set('fd_left_min', String(fl_min));
    if (this._isNum(fl_max)) payload.set('fd_left_max', String(fl_max));
    if (this._isNum(total))  payload.set('fd_total_distance', String(total));
    checked.forEach((v) => payload.append('frame_color_ids[]', v));

    try {
      e.currentTarget.disabled = true;
      const res = await this._ajax.put(`${this._props.endpoints.base}/${id}`, payload);
      const { success, message } = res || {};
      alert(message || (success ? '저장되었습니다.' : '저장에 실패했습니다.'));
      if (success) {
        const i = this.state.settings.findIndex((x) => String(x.id) === String(id));
        if (i >= 0) {
          this.state.settings[i] = {
            ...this.state.settings[i],
            wd_min, wd_max, vd_min, vd_max,
            fd_right_min: fr_min, fd_right_max: fr_max,
            fd_left_min: fl_min,  fd_left_max: fl_max,
            fd_total_distance: total,
            frame_color: checked.map((v) => parseInt(v, 10)),
          };
        }
      }
    } catch (err) {
      console.error(err);
      alert('요청 처리 중 오류가 발생했습니다.');
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
        this.state.settings = this.state.settings.filter((x) => String(x.id) !== String(id));
        this._update(); // re-render
      }
    } catch (err) {
      console.error(err);
      alert('요청 처리 중 오류가 발생했습니다.');
    } finally {
      e.currentTarget.disabled = false;
    }
  }

  // ========== Utils ==========
  _ensureMinMax(min, max, label) {
    if (this._isNum(min) && this._isNum(max) && min > max) {
      alert(`${label}의 최소값이 최대값보다 큽니다.`);
      return false;
    }
    return true;
  }

  _ensureVD(vdMin, vdMax) {
    const inRange = (v) => !this._isNum(v) || (v > 10 && v < 25);
    if (!inRange(vdMin)) { alert('VD 최소는 10~25mm 사이 값만 허용됩니다.'); return false; }
    if (!inRange(vdMax)) { alert('VD 최대는 10~25mm 사이 값만 허용됩니다.'); return false; }
    if (this._isNum(vdMin) && this._isNum(vdMax) && vdMin > vdMax) {
      alert('VD 최소값이 최대값보다 큽니다.');
      return false;
    }
    return true;
  }

  _isNum(v) { return typeof v === 'number' && !isNaN(v); }
  _numVal(el) {
    if (!el) return null;
    const v = parseFloat((el.value || '').trim());
    return isNaN(v) ? null : v;
  }

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
