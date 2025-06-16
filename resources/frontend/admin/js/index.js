import Controllers from "../bootstrap/Controllers";
import '../scss/index.scss';
class App {
    static init() {
        document.addEventListener('DOMContentLoaded', this.run.bind(this));
    }

    static run() {
        const controllerName = document.body.dataset.controller;
        const actionName = document.body.dataset.action;

        if (!controllerName || !actionName) {
            console.warn('[App] data-controller 또는 data-action이 비어 있습니다.');
            return;
        }

        const ControllerClass = Controllers[controllerName];

        if (!ControllerClass) {
            console.warn(`[App] 컨트롤러 '${controllerName}'를 찾을 수 없습니다.`);
            return;
        }

        const controller = new ControllerClass();

        if (typeof controller[actionName] !== 'function') {
            console.error(`[App] ${controller.constructor.name}.${actionName}() 메서드가 존재하지 않습니다.`);
            return; 
        }

        try {
            controller[actionName]();
        } catch (e) {
            console.error(`[App] ${controller.constructor.name}.${actionName}() 실행 중 오류 발생:`, e);
        }
    }
}

App.init();



// new Summernote('#content').render();
// Sortable.create(document.querySelector('.no-form-group'));

// const form = document.querySelector('#frm');
    
//     form.addEventListener('submit', async (e) => {
//         e.preventDefault(); 
//         e.submitter.disabled = true; 
        
//         const fd = new FormData(e.target); 
        
//         try {
//             const response = await fetch(e.target.action, {
//                 headers: {
//                     'Accept': 'application/json'
//                 },
//                 method: e.target.method,
//                 body: fd,
//             }); 

//             const resData = await response.json(); 
//             const {message, data, success} = resData;

//             console.log(resData);
            
//             alert(message);

//             // if (success && data.redirect){
//             //     location.href = data.redirect;
//             // }

//         } catch ($error) {
//             alert($error.message); 

//         } finally {

//             e.submitter.disabled = false; 
//         }
        
        
//     })