import AdminController from "../controllers/AdminController";
import CustomerController from "../controllers/CustomerController";
import DealerController from "../controllers/DealerController";
import EmployeeController from "../controllers/EmployeeController";
import NoticeController from "../controllers/NoticeController";
import ProductTemplateController from "../controllers/ProductTemplateController";
import RoleController from "../controllers/RoleController";

export default {
    notice: NoticeController,
    customer: CustomerController,
    employee: EmployeeController,
    dealer: DealerController,
    admin: AdminController,
    product_template: ProductTemplateController,
    role: RoleController,
};
