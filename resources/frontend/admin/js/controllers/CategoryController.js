import CategoryForm from "../components/Category/CategoryForm";
import CategoryList from "../components/Category/CategoryList";
import Ajax from "../core/Ajax";
import Controller from "../core/Controller";
import Loader from '../shared/Loader'; 

export default class CategoryController extends Controller
{
    async index() {
        this._logger.info('index');
        this.loader = Loader.make('portal').render();
        this.form = CategoryForm.make('category-form').render();

        this._handleListDelete(); 

        this._listen('category.create', this._handleCreate.bind(this));
        this._listen('category.delete', this._handleDelete.bind(this));
        this._listen('category.update', this._handleUpdate.bind(this));
    }

    _handleListDelete() {
        const forms = document.querySelectorAll('#category-list form');

        forms.forEach(form => {
            const deleteBtn = form.querySelector('[data-item-action="delete"]');

            if(deleteBtn){ 
                deleteBtn.addEventListener('click', async (e) => {
                    e.preventDefault();

                    if (!confirm('정말로 삭제하시겠습니까?')) return;

                    try {
                        this.loader.show();
                        const result = await new Ajax(false).delete(deleteBtn.href);

                        if (result.success) {
                            this._logger.success('카테고리 삭제 성공:', result);
                            location.reload(); 
                        } else {
                            alert('카테고리 삭제 실패');
                        }
                    } finally {
                        this.loader.hide();
                    }
                });
            }


            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(form);

                try {
                    this.loader.show();
                    const result = await new Ajax(false).put(form.action, formData);

                    if (result.success) {
                        this._logger.success('카테고리 수정 성공:', result);
                        location.reload(); 
                    } else {
                        alert('카테고리 수정 실패');
                    }
                } finally {
                    this.loader.hide();
                }
            });
        });

    }


    async _handleDelete({id}) 
    {
        if (!confirm('정말로 삭제하시겠습니까?')) {
            return; 
        }

        this._logger.info(id); 

        try {
            this.loader.show(); 
            const result = await new Ajax(false).delete(`/admin/product-categories/${id}`);
            this._logger.success(result);
            
            if (result.success) {
                this.categoryList.removeCategory(id);
            }
            
        } finally {
            this.loader.hide();
        }

    }

    async _handleUpdate({id, data})
    {
        this._logger.info(id, Object.fromEntries(data));

        try {
            this.loader.show(); 
            const result = await new Ajax(false).put(`/admin/product-categories/${id}`, data);
            this._logger.success(result);
            
            if (result.success) {
                await this._fetchCategories();
            }
            
        } finally {
            this.loader.hide();
        }
    }

    async _handleCreate({data, form, button})
    {
        this._logger.info(Object.fromEntries(data), form, button);

        try {
            button.disabled = true; 
            this.loader.show(); 
            const result = await new Ajax(false).post(form.action, data);
            this._logger.success(result);
            
            if (result.success) {
                location.reload(); 
            }
            
        } finally {

            this.loader.hide();
            button.disabled = false; 
        }

    }
}