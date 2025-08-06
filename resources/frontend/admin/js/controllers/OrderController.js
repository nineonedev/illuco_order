import Controller from "../core/Controller";
import Modal from '../shared/Modal';
import Loader from '../shared/Loader';
import SelectInput from "../components/Inputs/SelectInput";
import DateInput from "../components/Inputs/DateInput";
import Ajax from "../core/Ajax";

export default class OrderController extends Controller {
    loader;
    modal;
    form;
    cancelBtn;


    _prepare(){
        this.modal = Modal.make('portal').render();
        this.loader = Loader.make('portal').render();

        
        document.querySelectorAll('[data-view-type="select"]').forEach(el => {
            SelectInput.make(el).render();
        });

        document.querySelectorAll('[data-view-type="date"]').forEach(el => {
            DateInput.make(el).render();
        });
    }

    index(){
        this._prepare(); 
        this._handleListDelete();
        this._logger.info("index");
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
                const result = await new Ajax(false).delete('/admin/orders/bulk-delete', new URLSearchParams({
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


    show(){
        this._prepare();

        this._logger.info("show");

        const form = document.getElementById('cancel-frm'); 
        if (form) {
            this.cancelBtn = document.querySelector('[data-action=cancel]');
            this.form = form;
            form.addEventListener('submit', this.cancel.bind(this));
        }

        const restoreForm = document.getElementById('restore-form'); 
        
        if (restoreForm){
            restoreForm.addEventListener('submit', this._restoreAll.bind(this));
        }

    }

    async cancel(e){
        e.preventDefault();

        if (!confirm("정말로 이 주문을 취소하시겠습니까?\n취소 이후에는 되돌릴 수 없습니다.")) return;

        const button = e.submitter; 
        const form = e.target; 
        this._logger.info(button, form); 

        try {
            button.disabled = true; 
            this.loader.show();
            
            const result = await new Ajax(false).put(form.action);
            this._logger.success(result);

            if (result.success) {
                location.reload(); 
            }

        } finally {

            this.loader.hide();
            button.disabled = false; 
        }
    }

    edit() {
        this._prepare();
        this._logger.info("index");
        
        const form = document.getElementById('frm'); 
        this.form = form;

        const items = document.querySelectorAll('[data-view="order-item-form"]');

        if(items) {
            items.forEach(form => {
                form.addEventListener('submit', this._restore.bind(this));
            })

        }

        const historyItems = document.querySelectorAll('[data-order-history]'); 


        if (historyItems) {
            historyItems.forEach(item => {
                const deleteBtn = item.querySelector('[data-history-method="delete"]'); 
                const updateBtn = item.querySelector('[data-history-method="put"]');

                deleteBtn.addEventListener('click', async (e) => {
                    if(!confirm('정말로 삭제하시겠습니까')) {
                        return;
                    }

                    const button = e.currentTarget; 
                    try {
                        button.disabled = true; 
                        this.loader.show();
                        
                        const result = await new Ajax(false).delete(button.getAttribute('data-history-action'), fd);
                        this._logger.success(result);
                        
                        if (result.success) {
                            location.reload(); 
                        }

                    } finally {
                        this.loader.hide();
                        button.disabled = false; 
                    }
                });

                updateBtn.addEventListener('click', async (e) => {
                    const button = e.currentTarget; 
                    const settledInput = item.querySelector('[name="settled"]');
                    
                    try {
                        button.disabled = true; 
                        this.loader.show();

                        const fd = new URLSearchParams({
                            settled: settledInput.value,
                        });
                        
                        const result = await new Ajax(false).put(button.getAttribute('data-history-action'), fd);
                        this._logger.success(result);
                        
                        if (result.success) {
                            location.reload(); 
                        }

                    } finally {
                        this.loader.hide();
                        button.disabled = false; 
                    }
                });
            })
        }
        

        this.cancelBtn = form.querySelector('[data-action=cancel]');

        const historyForm = document.getElementById('history-form'); 

        if (historyForm) {
            historyForm.addEventListener('submit', this._storeHistory.bind(this)); 
        }

        if(form){
            const deleteBtn = form.querySelector('[data-action=delete]');

            document.querySelectorAll('[data-view-type="select"]').forEach(el => {
                const props = el.dataset.viewProps || '{}';
                SelectInput.make(el, JSON.parse(props)).render();
            })
            
            document.querySelectorAll('[data-view-type="date"]').forEach(el => {
                const props = el.dataset.viewProps || '{}';
                DateInput.make(el, JSON.parse(props)).render();
            })
            
            const restoreForm = document.getElementById('restore-form')
            if (restoreForm){
                restoreForm.addEventListener('submit', this._restoreAll.bind(this));
            }
            
            if(deleteBtn){
                deleteBtn.addEventListener('click', this._destroy.bind(this));
                form.addEventListener('submit', this._update.bind(this));
            }
        }

    }

    async _storeHistory(e) {
        e.preventDefault();
        const button = e.submitter;
        const form = e.target;
        this._logger.info(button, form);

        try {
            button.disabled = true; 
            this.loader.show();

            const fd = new FormData(form); 
                
            if (!fd.get('memo')) {
                throw new Error("메모를 입력해주세요.");
            }
            
            const result = await new Ajax(false).post(form.action, fd);
            this._logger.success(result);
            
            if (result.success) {
                location.reload(); 
            }

        } finally {
            this.loader.hide();
            button.disabled = false; 
        }
    }

    async _restoreAll(e){
        e.preventDefault();
        const button = e.submitter;
        const form = e.target;
        this._logger.info(button, form);

        try {
            button.disabled = true; 
            this.loader.show();

            const fd = new FormData(form); 
            const result = await new Ajax(false).post(form.action, fd);
            this._logger.success(result);

        } finally {
            this.loader.hide();
            button.disabled = false; 
        }
    }

    async _restore(e) {
        e.preventDefault();
        const button = e.submitter;
        const form = e.target;
        this._logger.info(button, form);

        try {
            button.disabled = true; 
            this.loader.show();

            const fd = new FormData(form); 
            const result = await new Ajax(false).post(form.action, fd);
            this._logger.success(result);

        } finally {
            this.loader.hide();
            button.disabled = false; 
        }
    }

    async _update(e) {
        e.preventDefault();

        const button = e.submitter; 
        const form = e.target; 
        this._logger.info(button, form); 
        
        const fd = new FormData(form);

        if (!fd.get('order_status')) {
            alert('주문 상태를 선택해주세요.');
            return;
        }

        try {
            button.disabled = true; 
            this.loader.show();
            
            const result = await new Ajax(false).put(form.action, fd);
            this._logger.success(result);

            if (result.success) {
                location.reload(); 
            }

        } finally {

            this.loader.hide();
            button.disabled = false; 
        }
    }

    async _destroy(e) {
        
        if (!confirm('정말로 삭제하시겠습니까?')) {
            return;
        }

        const button = e.currentTarget;
        const form = this.form;
        this._logger.info(button, form);

        try {
            button.disabled = true; 
            this.loader.show();

            const fd = new FormData(form); 
            const result = await new Ajax(false).delete(form.action, fd);
            this._logger.success(result);

            if (result.data.redirect) {
                location.href = result.data.redirect;
            } else {
                this.cancelBtn.click();
            }

        } finally {
            this.loader.hide();
            button.disabled = false; 
        }
    }
}
