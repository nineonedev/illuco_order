import AdminController from "../controllers/AdminController";
import CartController from "../controllers/CartController";
import CategoryController from "../controllers/CategoryController";
import CustomerController from "../controllers/CustomerController";
import DealerController from "../controllers/DealerController";
import EmployeeController from "../controllers/EmployeeController";
import NoticeController from "../controllers/NoticeController";
import OrderController from "../controllers/OrderController";
import ProductTemplateController from "../controllers/ProductTemplateController";
import RoleController from "../controllers/RoleController";
import OrderDocumentController from '../controllers/OrderDocumentController';

export default {
    notice: NoticeController,
    customer: CustomerController,
    employee: EmployeeController,
    dealer: DealerController,
    admin: AdminController,
    product_template: ProductTemplateController,
    role: RoleController,
    cart: CartController,
    order: OrderController,
    category: CategoryController,
    orderDocument: OrderDocumentController
};
