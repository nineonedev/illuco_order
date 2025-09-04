import Controllers from "./bootstrap/Controllers";

class App {
    static init() {
        document.addEventListener("DOMContentLoaded", this.run.bind(this));
        import("../scss/index.scss");
    }

    static run() {
        this.initHeader();
        this.initTab();
        this.dispatch();
        this.initTel();
    }

    static initTel() {
        document.querySelectorAll('input[type="tel"]').forEach((input) => {
            // 최초 로딩 시 포맷 적용
            input.value = this.formatPhone(input.value);

            // 입력 시 자동 포맷
            input.addEventListener("input", (e) => {
                const pos = input.selectionStart;
                const raw = input.value.replace(/[^0-9]/g, "");
                const formatted = this.formatPhone(raw);
                input.value = formatted;

                // 커서 위치 유지 개선 필요시 추가 로직 삽입
            });
        });
    }

    static formatPhone(value) {
        // 숫자만 남기기
        value = value.replace(/[^0-9]/g, "");

        // 02 지역번호 (서울)
        if (value.startsWith("02")) {
            if (value.length < 3) {
                return value;
            } else if (value.length < 6) {
                // 02-1~3자리
                return value.replace(/(\d{2})(\d+)/, "$1-$2");
            } else if (value.length < 9) {
                // 02-XXX-XXXX
                return value.replace(/(\d{2})(\d{3})(\d+)/, "$1-$2-$3");
            } else {
                // 02-XXXX-XXXX
                return value.replace(/(\d{2})(\d{4})(\d{4})/, "$1-$2-$3");
            }
        }

        // 기타 지역번호 or 휴대폰
        if (value.length < 4) {
            return value;
        } else if (value.length < 8) {
            return value.replace(/(\d{3})(\d+)/, "$1-$2");
        } else if (value.length < 12) {
            return value.replace(/(\d{3})(\d{3,4})(\d+)/, "$1-$2-$3");
        } else {
            return value.replace(/(\d{3})(\d{4})(\d{4})/, "$1-$2-$3");
        }
    }

    static dispatch() {
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

    static initTab() {
        const tabs = document.querySelectorAll(".no-base-tab-btn");
        const sections = document.querySelectorAll(
            ".no-base-tab-contents > section, .no-base-tab-contents > div"
        );

        if (!tabs || tabs.length === 0) return;

        // 초기 상태: 첫 번째 탭 활성화
        tabs[0].classList.add("is-active");
        sections.forEach((s, i) => {
            s.style.display = i === 0 ? "block" : "none";
        });

        tabs.forEach((tab, idx) => {
            tab.addEventListener("click", () => {
                tabs.forEach((t) => t.classList.remove("is-active"));
                tab.classList.add("is-active");

                sections.forEach((section, i) => {
                    section.style.display = i === idx ? "block" : "none";
                });
            });
        });
    }

    static initHeader() {
        // #drawer-menu-btn 클릭 시 #drawer에 --shrink 클래스 토글
        const menuBtn = document.getElementById("drawer-menu-btn");
        const drawer = document.getElementById("drawer");
        const openBtn = document.getElementById("drawer-open-btn");
        const closeBtn = document.getElementById("drawer-close-btn");

        
        if (!menuBtn || !drawer || !openBtn || !closeBtn) return;

        // 화면 크기 확인 후, 1024px 이하일 경우 --shrink 클래스 제거
        function checkScreenSize() {
            if (window.innerWidth <= 1024) {
                drawer.classList.remove("--shrink"); // 화면이 1024px 이하일 때 --shrink 클래스 제거
            } else {
                drawer.classList.remove("--open"); // PC에서는 --open 클래스 제거
            }
        }

        // 클릭 이벤트 리스너 추가
        menuBtn.addEventListener("click", function () {
            drawer.classList.toggle("--shrink"); // --shrink 클래스 토글
            checkScreenSize(); // 화면 크기 체크하여 --shrink 클래스 자동 제거
        });

        openBtn.addEventListener("click", function () {
            drawer.classList.add("--open"); // --open 클래스 추가
            checkScreenSize(); // 화면 크기 체크하여 --open 클래스 유지
        });

        closeBtn.addEventListener("click", function () {
            drawer.classList.remove("--open"); // --open 클래스 제거
            checkScreenSize(); // 화면 크기 체크하여 --open 클래스 제거
        });

        // 화면 크기 변경 시 자동으로 --shrink, --open 클래스 처리
        window.addEventListener("resize", function () {
            checkScreenSize();
        });

        document.querySelectorAll(".no-drawer-gnb").forEach((gnb) => {
            if (gnb.children.length === 0) {
                gnb.classList.add("--empty");
            }
        });
    }
}

App.init();
