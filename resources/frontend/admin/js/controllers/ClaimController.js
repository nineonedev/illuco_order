import Controller from "../core/Controller";
import Modal from "../shared/Modal";
import Loader from "../shared/Loader";
import SelectInput from "../components/Inputs/SelectInput";
import FileInput from "../components/Inputs/FileInput";
import DateTimeInput from "../components/Inputs/DateTimeInput";
import EditorInput from "../components/Inputs/EditorInput";
import SearchButton from "../components/Cart/SearchButton";
import Ajax from "../core/Ajax";
import TemplateSelection from "../components/Cart/TemplateSelection";
import ProductZone from "../components/Claim/ProductZone";
import Helper from "../supports/Helper";
import CustomerSelection from "../components/Cart/CustomerSelection";
import CustomerZone from "../components/Claim/CustomerZone";

export default class ClaimController extends Controller {
    form;
    cancelBtn;
    modal;
    loader;

    index() {
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

    create() {
        this._logger.info("create");
        this._prepare();

        this.productZone = ProductZone.make('product-zone').render();
        this.customerZone = CustomerZone.make('customer-zone').render(); 
        
        const productSearchBtn = document.getElementById('search-product');
        const customerSearchBtn = document.getElementById('search-customer');
        
        if (!productSearchBtn){
            console.error(`No found search button by Id: "search-product"`);
            return; 
        }
        SearchButton.make(productSearchBtn, {
            onClick: this._handleProductSearch.bind(this),
        }).render();

        if (!customerSearchBtn){
            console.error(`No found search button by Id: "search-customer"`);
            return; 
        }
        SearchButton.make(customerSearchBtn, {
            onClick: this._handleCustomerSearch.bind(this),
        }).render();


        const refreshBtn = document.getElementById('refresh-btn'); 

        if(refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.productZone.setState({template: {}});
                this.customerZone.setState({customer: {}});
            });
        }
        
        this._listen("fetch.customers", this._fetchAllCustomers.bind(this));
        this._listen("fetch.templates", this._fetchAllTemplates.bind(this));

        this._listen("pick.customer", this._pickCustomer.bind(this));
        this._listen("pick.template", this._pickTemplate.bind(this));

        this.form.addEventListener("submit", this._store.bind(this));
    }

    show() {
        this._logger.info("show");

        // const deleteBtn = this.form.querySelector('[data-action="delete"]');
        // deleteBtn?.addEventListener("click", this._destroy.bind(this));
    }

    async _store(e) {
        e.preventDefault();

        if (Helper.isEmptyObject(this.productZone.state.template)) {
            alert('문의할 제품을 선택해주세요.');
            return; 
        }

        if (Helper.isEmptyObject(this.customerZone.state.template)) {
            alert('클레임 대상을 선택해주세요.');
            return; 
        }

        const t = e.target;
        const fd = new FormData(t);
        const action = t.action;
        const submitter = e.submitter;
        
        if (!fd.get('product_serial_number')) {
            alert('문의내용을 입력해주세요.');
            return; 
        }

        if (!fd.get('title')) {
            alert('제목을 입력해주세요.');
            return; 
        }

        if (!fd.get('content')) {
            alert('문의내용을 입력해주세요.');
            return; 
        }
        
        if(!confirm('클레임을 한 번 접수하면 이후 내용을 수정할 수 없습니다. 접수하시겠습니까?')) {
            return;
        }
        
        try {
            submitter.disabled = true;
            this.loader.show();

            const result = await this._ajax.post(action, fd);
            const { success, data } = result;

            if (success) {
                this.cancelBtn?.click();
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

    async _fetchAllTemplates({ button, query = null }, evt) {
        let url = `/admin/product-templates`;
        if (query) {
            url += `?` + query;
        }

        try {
            if (button) {
                button.setState({ disabled: true });
            }
            this.loader.show();

            const result = await new Ajax(true).get(url);
            const { success, data } = result;

            if (!success) return;

            const { templates, categories, query } = data;

            if (this.modal.state.open) {
                this.modal.setState({
                    open: false,
                    content: "",
                    header: "",
                })
            }

            this.modal.setState({
                header: "제품 검색",
                content: TemplateSelection.make(
                    null,
                    { paginator: templates, categories, query: query },
                    false
                ),
                open: true,
            });
        } finally {
            if (button) {
                button.setState({ disabled: false });
            }

            this.loader.hide(); 
        }
    }

    async _fetchAllCustomers({ button, query = null }, evt) {
        let url = `/admin/customers`;
        if (query) {
            url += `?` + query;
        }

        try {
            if (button) {
                button.setState({ disabled: true });
            }

            this.loader.show();
            const result = await new Ajax(true).get(url);

            if (!result.success) {
                return;
            }

            const { customers, query, dealers, countries} = result.data;

            if (this.modal.state.open) {
                this.modal.setState({
                    open: false,
                    content: "",
                    header: "",
                })
            }

            this.modal.setState({
                header: "고객 검색",
                content: CustomerSelection.make(
                    null,
                    { paginator: customers, query: query, dealers, countries },
                    false
                ),
                open: true,
            });
        } finally {
            if (button) {
                button.setState({ disabled: false });
            }

            this.loader.hide();
        }
    }

    _handleProductSearch(view){
        this._fetchAllTemplates({button: view});
    }

    _handleCustomerSearch(view){
        this._fetchAllCustomers({button: view});
    }

    _pickTemplate({ template }, evt) {
        this.productZone.setState({template: template})
        this.modal.setState({
            open: false,
            content: "",
            header: "",
        });
    }

    _pickCustomer({ customer }, evt) {
        this.customerZone.setState({customer: customer})
        this.modal.setState({
            open: false,
            content: "",
            header: "",
        });
    }

    async _prepare() {
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

        await this._loadAllData();
    }

    async _loadAllData() {
        this.loader.show();

        try {
            const [
                attrResult,
                setGroupItemsResult,
                labelResult,
            ] = await Promise.all([
                new Ajax(true).get("/admin/product-templates/attributes"),
                new Ajax(true).get('/admin/product-templates/set-group-items'),
                new Ajax(true).get('/admin/product-templates/labels')
            ]);

            ClaimController.attributes = attrResult.data;
            ClaimController.setGroupItems = setGroupItemsResult.data; 
            ClaimController.labels = labelResult.data; 

            this._logger.success("속성 로드 결과", ClaimController.attributes);
            this._logger.success("세트 로드 결과", ClaimController.setGroupItems);

        } catch (err) {
            alert(err.message);
        } finally {
            this.loader.hide();
        }
    }
}
