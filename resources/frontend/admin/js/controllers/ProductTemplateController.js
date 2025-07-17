import Ajax from "../core/Ajax";
import Controller from "../core/Controller";
import AttributeEditor from "../components/Product/AttributeEditor";
import SelectInput from "../components/Inputs/SelectInput";
import FileInput from '../components/Inputs/FileInput';
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";

export default class ProductTemplateController extends Controller {
    form;
    cancelBtn;
    attrManager;
    attrModal;

    index(){
        this._logger.info("index");
        this._prepare();
        this._handleListDelete();
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
                const result = await new Ajax(false).delete('/admin/orders/bulk-delete', {
                    ids: ids
                });

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

    async _prepare() {
        this.form = document.getElementById('frm');
        this.cancelBtn = document.querySelector('[data-action="cancel"]');

        this.modal = Modal.make('portal').render();
        this.loader = Loader.make('portal').render();
        

        document.querySelectorAll('[data-view-type=file]').forEach(el => {
            FileInput.make(el).render();
        });

        document.querySelectorAll('[data-view-type=select]').forEach(el => {
            SelectInput.make(el).render();
        })
    }

    show(){
        this._logger.info("show");
    }

    async _fetchCategories()
    {
        const result = await new Ajax(true).get('/admin/product-categories'); 
        if (result.success){
            return result.data.categories; 
        }

        return [];
    }


    async create() {
        this._logger.info("create");
        this._prepare();

        this.form.addEventListener('submit', async (e) => {
            const result = await this._process(e, (data, action) => this._ajax.post(action, data, true))
            
            if (!result.success) {
                return; 
            }

            const {data} = result;
            
            if (data.redirect) {
                location.href = data.redirect; 
                return;
            }

            if (this.cancelBtn) {
                this.cancelBtn.click();
            }

        });
    }

    async _destroy(action, data) {
        if (!confirm("정말로 삭제하시겠습니까?")) return;

        const result = await this._ajax.delete(action, data);
        this._logger.success(result);

        if (result.success) {
            this.cancelBtn?.click();
        }
    }

    async edit() {
        this._logger.info("edit");
        this._prepare();

        this.form.addEventListener('submit', async (e) => {
            
            const result = await this._process(e, (data, action) => this._ajax.put(action, data, true))
            if (result?.success) {
                location.reload();
            }
            
        });

        const deleteBtn = this.form.querySelector('[data-action="delete"]');
        deleteBtn?.addEventListener('click', () => {
            const data = new FormData(this.form);
            data.set('_method', 'delete');
            this._destroy(this.form.action, data);
        });

        // ============================================================
        // manager 
        // ============================================================

        // this.attrManager = AttributeManager.make('attr-hook').render();
        // this.attrModal = AttributeModal.make('modal-hook').render();

        // this._listen('attr.store', this._storeAttribute.bind(this));
        // this._listen('attr.open', this._openAttribute.bind(this));
        // this._listen('attr.update', this._updateAttribute.bind(this));
        // this._listen('attr.destroy', this._destroyAttribute.bind(this));

        // this._listen('option.store', this._storeOption.bind(this));
        // this._listen('option.update', this._updateOption.bind(this));
        // this._listen('option.destroy', this._destroyOption.bind(this));

        // await this._allAttributes();
    }

    // ============================================================
    // Option 
    // ============================================================
    async _destroyOption({data, onDisabled, component}) {
        // this._logger.success(Object.fromEntries(data)); 
        if (!confirm('정말로 삭제하시겠습니까?')){
            return;
        }

        try {
            onDisabled(true); 
            const result = await new Ajax(false).delete(`/admin/product-options/${data.id}`);
            this._logger.success(result); 

            if (result.success) {
                const option = result.data.option;
                console.log('옵션 삭제됨.', option);
                // component.destroy();

                const {attribute} = await this.findAttribute({id: data.attribute_id});

                this.attrModal.setState({
                    header: `${attribute.label} 속성 수정`,
                    content: AttributeEditor.make(null, {
                        attribute: attribute,
                        status: 'option'
                    }, false),
                    open: true,
                });
            }

        } finally{
            onDisabled(false); 
        }
    }

    async _updateOption({data, onDisabled, component}) {
        // this._logger.success(Object.fromEntries(data)); 

        const id = data.get('id'); 

        try {
            onDisabled(true); 
            const result = await new Ajax(false).put(`/admin/product-options/${id}`, data);
            this._logger.success(result); 

            if (result.success) {
                const option = result.data.option;
                console.log('옵션 수정됨.', option);
                component.setState({...option});
            }

        } finally{
            onDisabled(false); 
        }
    }

    async _storeOption({data, onDisabled}) {
        // this._logger.success(Object.fromEntries(data)); 

        try {
            onDisabled(true); 
            const result = await new Ajax(false).post('/admin/product-options', data);
            this._logger.success(result); 

            if (result.success) {
                const option = result.data.option;
                console.log('옵션 생성됨.', option);
                
                const {attribute} = await this.findAttribute({id: option.attribute_id});

                this.attrModal.setState({
                    header: `${attribute.label} 속성 수정`,
                    content: AttributeEditor.make(null, {
                        attribute: attribute,
                        status: 'option'
                    }, false),
                    open: true,
                });
            }

        } finally{
            onDisabled(false); 
        }
    }

    // ============================================================
    // Attribute 
    // ============================================================
    async findAttribute({id, onDisabled = () => {}}) {
            const url = `/admin/product-attributes/${id}`;
            try {
                onDisabled(true); 
                const result = await new Ajax(true).get(url);
                if (result.success) {
                    return result.data; 
                }
            } finally{
                onDisabled(false); 
            }

            return {};
        }

        async _openAttribute(data){
            this._logger.info(data);
            const {attribute} = await this.findAttribute(data);

            this.attrModal.setState({
                header: `${attribute.label} 속성 수정`,
                content: AttributeEditor.make(null, {
                    attribute: attribute, 
                }, false),
                open: true,
            });
        }

        async _updateAttribute({data, onDisabled = () => {}}){
            const id = data.get('id');
            const url = `/admin/product-attributes/${id}`;
            try {
                onDisabled(true);
                const result = await new Ajax(false).put(url, data);
                if (result.success) {
                    const attributes = await this._allAttributes();
                    const attr = attributes.find(a => a.id == id);

                    this.attrModal.setState({
                        content: AttributeEditor.make(null, {attribute: attr}, false),
                    })
                }
            } finally{
                onDisabled(false); 
            }
        }

        async _destroyAttribute({id, onDisabled = () => {}}){
            if (!confirm('정말로 삭제하시겠습니까?')){
                return;
            }

            const url = `/admin/product-attributes/${id}`;
            try {
                onDisabled(true);
                const result = await new Ajax(false).delete(url);
                if (result.success) {
                    await this._allAttributes();
                    this.attrModal.setState({open: false, header: null, content: null});
                }
            } finally{
                onDisabled(false); 
            }

            return {};
        }

        async _allAttributes() {
            const action = this.form.dataset.showAction;
            const result = await new Ajax(true).get(action);
            const {data} = result;
            this.attrManager.renderList({attributes: data.template.attributes});

            return data.template.attributes;
        }

        async _storeAttribute({submitter, data, action}, event) {
            const sortOrder = this.attrManager.getCurrentSortOrder(); 
            data.append('sort_order', sortOrder);

            try {
                submitter.disabled = true; 
                
                const result = await new Ajax(false).post(action, data);

                if (result.success) {
                    this.attrManager.resetForm();
                    await this._allAttributes(); 
                }

            } finally {
                submitter.disabled = false; 
            }
        }

    
}
