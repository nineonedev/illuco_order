import AdminController from "../controllers/AdminController";
import CustomerController from "../controllers/CustomerController";
import DealerController from "../controllers/DealerController";
import EmployeeController from "../controllers/EmployeeController";
import NoticeController from "../controllers/NoticeController";

export default {
    notice: NoticeController,
    customer: CustomerController,
    employee: EmployeeController,
    dealer: DealerController,
    admin: AdminController,
};
