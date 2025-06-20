import Logger from "../supports/Logger";
import Ajax from "./Ajax";

export default class Component {
    static HOOK_COUNT = 0;
    static NODE_COUNT = 0;
    static NODE_BINDING_COUNT = 0;
    static NODE_PROP_SEL = "data-component-props";
    static NODE_TYPE_SEL = "data-component-type";
    static NODE_HYDRATED_SEL = "data-component-hydrated";

    constructor(hookId, props = {}, setup = true) {
        this._id = this._generateComponentId();
        this._fallbackHookId = this._generateComponentHookId();
        this._hookId = hookId;
        this._hostEl = null;
        this._el = null;
        this._type = null;
        this._DOM = {};
        this._refs = {};
        this._watchers = {};
        this._lifecycle = {
            mounted: [],
            destroyed: [],
        };

        this._children = [];
        this._isSetup = false;
        this._hydrated = false;
        this._logger = null;
        this._ajax = null;

        this._oldProps = props;
        this._initialProps = {};
        this._initialState = {};
        this._computed = {};
        this._props = {};
        this._state = {};

        if (setup) {
            this._setup();
        }
    }

    static make(hookId, props = {}, setup = true) {
        return new this(hookId, props, setup);
    }

    setHookId(hookId){
        this._hookId = hookId; 
        return this; 
    }

    /** =============================
     * Setup & Boot
     ============================== */
    _setup() {
        if (this._isSetup) return;

        this._boot();
        this._hydrateIfNeeded();
        this._booted();

        this._isSetup = true;
    }

    /**
     *
     * @param {Component} component
     */
    addChild(component) {
        if (!(component instanceof Component)) {
            return;
        }

        this._children.push(component);
    }

    _hydrateIfNeeded() {
        let element;
        if (this._hookId === 'dealer-hook') {
            console.log(this, document.getElementById(this._hookId));
                
        }

        if (this._hookId instanceof HTMLElement) {
            element = this._hookId;

            if (element.hasAttribute('id')) {
                this._hookId = element.getAttribute('id'); 
            } else {
                this._hookId = this._fallbackHookId; 
            }

            element.setAttribute('id', this._hookId); 

        } else {
            element = document.getElementById(this._hookId); 
            if (!element) {
                this._logger.error(`Element not found by hookId: ${this._hookId}`, this);
                throw new Error(`Element not found by hookId: ${this._hookId}`);
            }
        }

        const isHydratable = element.hasAttribute(Component.NODE_PROP_SEL);

        if (isHydratable) {
            this._hydrated = true;
            const props = element.getAttribute(Component.NODE_PROP_SEL);
            this._syncProps({
                ...JSON.parse(props || "{}"),
                ...this._oldProps
            });
            element.setAttribute('data-hydrated', true);
            this._logger.info("component hydrated");

        } else {
            this._syncProps(this._oldProps);
        }

        
        this._hostEl = element;
    }

    _boot() {
        this._ajax = new Ajax();
        this._logger = new Logger(this.constructor.name, true);
    }

    _booted(){
        this._syncState({});
    }

    _defineState() {
        return {};
    }

    _defineProps() {
        return {};
    }

    _defineComputed() {
        return {};
    }

    _syncProps(props = {}) {
        this._initialProps = { ...this._defineProps(), ...props };
        this._props = { ...this._initialProps };
    }

    _syncState(state = {}) {
        this._initialState = { ...state, ...this._defineState() };
        this._state = { ...this._initialState };
    }

    /** =============================
     * Rendering
     ============================== */

    _generateComponentId() {
        return `component-${Component.NODE_COUNT++}`;
    }

    _generateComponentHookId() {
        return `component-hook-${Component.HOOK_COUNT++}`;
    }

    _generateNodeId() {
        return `node-${Component.NODE_BINDING_COUNT++}`;
    }

    _render() {
        if (!this._isSetup) {
            this._setup();
        }

        this._beforeRender();
        this._performRender();
        this._connectBindings();
        this._afterRender(); 
        this._callMountedHooks();
    }

    _performRender(){
        const html = this._template();
        const template = document.createElement("template");
        template.innerHTML = html.trim();
        const content = template.content.firstElementChild;

        if (!this._el) {
            this._hostEl.appendChild(content);
            this._el = content;
        } else {
            this._el.replaceWith(content);
            this._el = content;
        }

        if (this._type === null) {
            throw new Error("Componnet type missing.");
        }

        this._el.setAttribute(Component.NODE_TYPE_SEL, this._type);
        this._el.setAttribute("id", this._id);
    }

    _beforeRender(){
        
    }

    _afterRender(){
        
    }

    render(shouldRender = true) {
        if (shouldRender) {
            this._render();
        }

        return this; 
    }

    _template() {
        return "<div></div>";
    }

    _bindDOM() {}

    _bindEvents() {}

    _bindCleanUp() {}

    _bindSlots() {
        const slotNodes = this.qsAll("[data-slot]");

        this._slots = {};

        slotNodes.forEach((node) => {
            const name = node.getAttribute("data-slot") || "default";
            this._slots[name] = node;
        });
    }

    _bindRefs() {
        this._refs = {};
        const refNodes = this.qsAll("[data-ref]");

        refNodes.forEach((node) => {
            const refName = node.getAttribute("data-ref");
            if (refName) {
                this._refs[refName] = node;
            }
        });
    }

    _connectBindings() {
        this._bindDOM();
        this._bindRefs();
        this._bindSlots();
        this._bindEvents();
        this._bindCleanUp();
    }

    /** =============================
     * Lifecycle Hooks (override)
     ============================== */

    onMounted(callback) {
        this._lifecycle.mounted.push(callback);
    }

    onDestroyed(callback) {
        this._lifecycle.destroyed.push(callback);
    }

    _callMountedHooks() {
        for (const cb of this._lifecycle.mounted) {
            try {
                cb.call(this);
            } catch (e) {
                this._logger.error("Mounted hook error", e);
            }
        }
    }

    _callDestroyedHooks() {
        for (const cb of this._lifecycle.destroyed) {
            try {
                cb.call(this);
            } catch (e) {
                this._logger.error("Destroyed hook error", e);
            }
        }
    }

    destroy() {
       // 1. 라이프사이클 훅 실행
        this._callDestroyedHooks();

        // 2. 자식 컴포넌트 모두 제거
        this._children.forEach(child => {
            if (typeof child.destroy === 'function') {
                child.destroy();
            }
        });
        this._children = [];

        // 3. DOM 제거
        if (this._el && this._el.parentNode) {
            this._el.remove();
        }
        this._el = null;

        // host 엘리먼트는 유지하거나, 제거할 경우 다음도 가능:
        // if (this._hostEl && this._hostEl.parentNode) {
        //     this._hostEl.remove();
        // }
        this._hostEl = null;

        // 4. 내부 상태 초기화
        this._refs = {};
        this._DOM = {};
        this._slots = {};
        this._watchers = {};
        this._props = {};
        this._state = {};
        this._initialProps = {};
        this._initialState = {};
        this._computed = {};

        // 5. 플래그 초기화
        this._isSetup = false;
        this._hydrated = false;

        // 6. 로그
        this._logger?.info("Component destroyed");
    }

    /** =============================
     * State Management
     ============================== */
    get slots() {
        return this._slots;
    }

    get refs() {
        return this._refs;
    }
    get state() {
        return this._state;
    }

    get props() {
        return this._props;
    }

    get computed() {
        const result = {};
        const computedDefs = this._computed;

        for (const key in computedDefs) {
            const getter = computedDefs[key];
            if (typeof getter === "function") {
                result[key] = getter.call(this);
            }
        }

        return result;
    }

    watch(state, callback) {
        if (!this._watchers[state]) {
            this._watchers[state] = [];
        }

        this._watchers[state].push(callback);
    }

    _triggerWatchers(keyPath, newVal, oldVal) {
        const callbacks = this._watchers[keyPath] || [];
        for (const cb of callbacks) {
            try {
                cb.call(this, newVal, oldVal);
            } catch (e) {
                this._logger.error(`Watcher error for "${keyPath}"`, e);
            }
        }
    }

    setState(newState = {}, shouldRender = true) {
        Object.keys(newState).forEach((key) => {
            const oldVal = this._state[key];
            const newVal = newState[key];

            if (oldVal !== newVal) {
                this._state[key] = newVal;
                this._triggerWatchers(key, newVal, oldVal);
            }
        });

        if (shouldRender) {
            this._render();
        }
    }

    getChanges() {
        const changes = {};
        for (const key in this._state) {
            if (this._state[key] !== this._initialState[key]) {
                changes[key] = {
                    from: this._initialState[key],
                    to: this._state[key],
                };
            }
        }
        return changes;
    }

    isDirty() {
        return Object.keys(this.getChanges()).length > 0;
    }

    isClean() {
        return !this.isDirty();
    }

    /** =============================
     * Event System
     ============================== */
    _emit(eventName, data) {
        const handler = this._props?.on?.[eventName];
        if (typeof handler === "function") {
            this._logger.info(`Emitting '${eventName}'`, data);
            handler(data);
        } else {
            this._logger.warn(`No handler for emitted event '${eventName}'`);
        }
    }

    dispatch(name, detail = {}) {
        const event = new CustomEvent(`@${name}`, {
            detail,
            bubbles: true,
            cancelable: true,
        });

        document.body.dispatchEvent(event);
    }

    /** =============================
     * DOM Utilities
     ============================== */
    _setDOM(name, selector) {
        this._DOM[name] = this.qs(selector);
    }

    _getDOM(name) {
        return this._DOM[name];
    }

    qs(selector) {
        return this._el?.querySelector(selector);
    }

    qsAll(selector) {
        return [...(this._el?.querySelectorAll(selector) || [])];
    }

    on(selector, eventName, callback, strict = false) {
        const el =
            selector instanceof HTMLElement ? selector : this.qs(selector);

        if (!el) {
            const msg = `Event binding failed: '${eventName}' on`;
            if (strict) throw new Error(msg);
            return;
        }

        el.addEventListener(eventName, (e) => callback(this, e));
    }

    off(selector, eventName, callback) {
        const el = this.qs(selector);
        if (!el) {
            return;
        }

        el.removeEventListener(eventName, callback);
    }

    /** =============================
     * Debug Helper
     ============================== */
    get logger() {
        return this._logger;
    }
}
