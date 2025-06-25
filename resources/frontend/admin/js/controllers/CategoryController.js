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
        this.categoryList = CategoryList.make('category-list');

        this._listen('category.create', this._handleCreate.bind(this));
        this._listen('category.delete', this._handleDelete.bind(this));
        this._listen('category.update', this._handleUpdate.bind(this));

        this._fetchCategories();
    }

    async _fetchCategories()
    {
        const result = await new Ajax(true).get('/admin/product-categories');
        this._logger.success(result);
        const items = result.data.categories;
    
        this.categoryList.setState({items: items});
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
        data.append('sort_order', this.categoryList.getCurrentIndex() + 1);

        this._logger.info(Object.fromEntries(data), form, button);

        try {
            button.disabled = true; 
            this.loader.show(); 
            const result = await new Ajax(false).post(form.action, data);
            this._logger.success(result);
            
            if (result.success) {
                form.reset(); 
                this.categoryList.addCategory(result.data.category);
            }
            
        } finally {

            this.loader.hide();
            button.disabled = false; 
        }

    }
}