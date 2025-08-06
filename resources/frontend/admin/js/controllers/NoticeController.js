import Controller from "../core/Controller";
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";
import SelectInput from "../components/Inputs/SelectInput";
import FileInput from "../components/Inputs/FileInput";
import DateTimeInput from "../components/Inputs/DateTimeInput";
import EditorInput from "../components/Inputs/EditorInput";
import DateInput from "../components/Inputs/DateInput";
import Ajax from "../core/Ajax";

export default class NoticeController extends Controller {
    form;
    cancelBtn;
    modal;
    loader;

    index() {
        this._logger.info("index");
        this._prepare(); 
        this._handleListDelete();
    }

    create() {
        this._logger.info("create");
        this._prepare();

        this.form.addEventListener("submit", this._store.bind(this));
    }

     _handleListDelete() {
        const deleteBtn = document.getElementById('select-delete-btn'); 
        const chkAll = document.getElementById('chk-all'); 
        const chkItems = document.querySelectorAll('[name="checked_ids[]"]');

        if (!deleteBtn || !chkAll || chkItems.length === 0) return; 

        const singleDeleteButtons = document.querySelectorAll('[data-item-action="delete"]');

        // ✅ 개별 삭제 처리
        singleDeleteButtons.forEach(link => {
            link.addEventListener('click', async e => {
                e.preventDefault(); 

                if (!confirm("정말로 삭제하시겠습니까?")) return;

                const action = link.getAttribute('href');
                this.loader.show();

                try {
                    const result = await new Ajax(false).delete(action);

                    if (result.success) {
                        location.reload();
                    } else {
                        alert("삭제에 실패했습니다.");
                    }
                } finally {
                    this.loader.hide();
                }
            });
        });

        const updateDeleteButtonState = () => {
            const checkedItems = Array.from(chkItems).filter(chk => chk.checked);
            const count = checkedItems.length;

            deleteBtn.disabled = count === 0;
            deleteBtn.textContent = count > 0 ? `총 ${count}개 항목 삭제` : '선택삭제';
        };

        // ✅ 개별 체크박스 변경 이벤트
        chkItems.forEach(chk => {
            chk.addEventListener('change', () => {
                const allChecked = Array.from(chkItems).every(chk => chk.checked);
                chkAll.checked = allChecked;
                updateDeleteButtonState();
            });
        });

        // ✅ 전체 선택 체크박스 처리
        chkAll.addEventListener('change', () => {
            const checked = chkAll.checked;
            chkItems.forEach(chk => chk.checked = checked);
            updateDeleteButtonState();
        });

        // ✅ 선택삭제 버튼 클릭 이벤트
        deleteBtn.addEventListener('click', async () => {
            const checked = Array.from(chkItems).filter(chk => chk.checked);
            if (checked.length === 0) {
                alert("삭제할 항목을 선택해주세요.");
                return;
            }

            if (!confirm(`정말로 ${checked.length}개의 항목을 삭제하시겠습니까?`)) return;

            const ids = checked.map(chk => chk.value);
            this.loader.show();

            try {
                const result = await new Ajax(false).delete('/admin/notices/bulk-delete', new URLSearchParams({
                    ids: ids
                }));

                if (result.success) {
                    location.reload();
                } else {
                    alert("삭제에 실패했습니다.");
                }
            } finally {
                this.loader.hide();
            }
        });

        // 초기 상태 동기화
        updateDeleteButtonState();
    }

    edit() {
        this._logger.info("edit");
        this._prepare();

        this.form.addEventListener("submit", this._update.bind(this));

        const deleteBtn = this.form.querySelector('[data-action="delete"]');
        deleteBtn?.addEventListener("click", this._destroy.bind(this));
    }

    async _store(e) {
        e.preventDefault();

        const t = e.target;
        const fd = new FormData(t);
        const action = t.action;
        const submitter = e.submitter;

        try {
            submitter.disabled = true;
            this.loader.show();

            const result = await this._ajax.post(action, fd);
            const { success, data } = result;
            

            if (success && data && data.notice) {
                if (this.cancelBtn) {
                    this.cancelBtn.click();
                } else {
                    location.href = `${action}/edit/${data.notice.id}`;
                }
            }
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    async _update(e) {
        e.preventDefault();

        const t = e.target;
        const fd = new FormData(t);
        const action = t.action;
        const submitter = e.submitter;

        try {
            submitter.disabled = true;
            this.loader.show();

            const result = await this._ajax.put(action, fd);
            const { success } = result;

            if (success) {
                location.reload();
            }
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    async _destroy(e) {
        if (!confirm("정말로 삭제하시겠습니까?")) return;

        const action = this.form.action;
        const submitter = e.currentTarget;

        try {
            submitter.disabled = true;
            this.loader.show();

            const result = await this._ajax.delete(action);
            this._logger.success(result);

            if (result.success) {
                this.cancelBtn?.click();
            }
        } finally {
            this.loader.hide();
            submitter.disabled = false;
        }
    }

    _prepare() {
        this.form = document.getElementById("frm");
        this.cancelBtn = document.querySelector('[data-action="cancel"]');
        this.modal = Modal.make("portal").render();
        this.loader = Loader.make("portal").render();

        document.querySelectorAll('[data-view-type="select"]').forEach(el => {
            SelectInput.make(el).render();
        });

        document.querySelectorAll('[data-view-type="editor"]').forEach(el => {
            EditorInput.make(el).render();
        });

        document.querySelectorAll('[data-view-type="file"]').forEach(el => {
            FileInput.make(el).render();
        });

        document.querySelectorAll('[data-view-type="datetime"]').forEach(el => {
            DateTimeInput.make(el).render();
        });

        document.querySelectorAll('[data-view-type="date"]').forEach(el => {
            const props = el.dataset.viewProps || '{}';
            DateInput.make(el, JSON.parse(props)).render();
        })
    }
}
