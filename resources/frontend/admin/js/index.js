import Controllers from "./bootstrap/Controllers";
import SelectInput from "./components/Inputs/SelectInput";

class App {
    static init() {
        document.addEventListener("DOMContentLoaded", this.run.bind(this));
        import('../scss/index.scss');
    }

    static run() {
        this.initHeader();
        this.dispatch();
    }

    static dispatch(){
        
        const controllerName = document.body.dataset.controller;
        const actionName = document.body.dataset.action;


        if (!controllerName || !actionName) {
            console.warn(
                "[App] data-controller 또는 data-action이 비어 있습니다."
            );
            return;
        }

        const ControllerClass = Controllers[controllerName];

        if (!ControllerClass) {
            console.warn(
                `[App] 컨트롤러 '${controllerName}'를 찾을 수 없습니다.`
            );
            return;
        }

        const controller = new ControllerClass();

        if (typeof controller[actionName] !== "function") {
            console.error(
                `[App] ${controller.constructor.name}.${actionName}() 메서드가 존재하지 않습니다.`
            );
            return;
        }

        try {
            controller[actionName]();
        } catch (e) {
            console.error(
                `[App] ${controller.constructor.name}.${actionName}() 실행 중 오류 발생:`,
                e
            );
        }
    }

    static initHeader() {
        // #drawer-menu-btn 클릭 시 #drawer에 --shrink 클래스 토글
        const menuBtn = document.getElementById('drawer-menu-btn');
        const drawer = document.getElementById('drawer');
        const openBtn = document.getElementById('drawer-open-btn');
        const closeBtn = document.getElementById('drawer-close-btn');
        
        if (!menuBtn || !drawer || !openBtn || !closeBtn) return;

        // 화면 크기 확인 후, 1024px 이하일 경우 --shrink 클래스 제거
        function checkScreenSize() {
            if (window.innerWidth <= 1024) {
                drawer.classList.remove('--shrink'); // 화면이 1024px 이하일 때 --shrink 클래스 제거
            } else {
                drawer.classList.remove('--open'); // PC에서는 --open 클래스 제거
            }
        }

        // 클릭 이벤트 리스너 추가
        menuBtn.addEventListener('click', function() {
            drawer.classList.toggle('--shrink'); // --shrink 클래스 토글
            checkScreenSize(); // 화면 크기 체크하여 --shrink 클래스 자동 제거
        });

        openBtn.addEventListener('click', function() {
            drawer.classList.add('--open'); // --open 클래스 추가
            checkScreenSize(); // 화면 크기 체크하여 --open 클래스 유지
        });

        closeBtn.addEventListener('click', function() {
            drawer.classList.remove('--open'); // --open 클래스 제거
            checkScreenSize(); // 화면 크기 체크하여 --open 클래스 제거
        });

        // 화면 크기 변경 시 자동으로 --shrink, --open 클래스 처리
        window.addEventListener('resize', function() {
            checkScreenSize();
        });


        document.querySelectorAll('.no-drawer-gnb').forEach(gnb => {
            console.log(gnb);
            
            if (gnb.children.length === 0){
                gnb.classList.add('--empty');
            }
        });
    }
}

App.init();
