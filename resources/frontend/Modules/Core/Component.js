import Logger from "../supports/Logger";
import Ajax from "./Ajax";

export default class Component {
    static HOOK_COUNT = 0;
    static NODE_COUNT = 0;
    static NODE_BINDING_COUNT = 0;
    static NODE_PROP_SEL = "data-component-props";
    static NODE_TYPE_SEL = "data-component-type";
    static NODE_HYDRATED_SEL = "data-component-hydrated";

    constructor(hookId, props = {}) {
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

        this._setup();
    }

    static make(hookId, props = {}) {
        return new this(hookId, props);
    }

    /** =============================
     * Setup & Boot
     ============================== */
    _setup() {
        if (this._isSetup) return;

        this._boot();
        this._hydrateIfNeeded();

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

        if (this._hookId instanceof HTMLElement) {
            element = this._hookId;
            this._hookId = this._fallbackHookId;
        } else {
            element = document.getElementById(this._hookId);
            if (!element) {
                throw new Error(`No found element by hookId: #${this._hookId}`);
            }
        }

        if (
            !element.hasAttribute(Component.NODE_PROP_SEL) &&
            !element.hasAttribute(Component.NODE_TYPE_SEL)
        ) {
            this._hostEl = element;
            this._syncProps(this._oldProps);
            return;
        }

        this._hydrated = true;
        this._el = element;
        const props = element.getAttribute(Component.NODE_PROP_SEL);

        this._hydrate();
        this._syncProps(JSON.parse(props ? props : "{}"));
        this._logger.info("component hydrated");
    }

    _boot() {
        this._ajax = new Ajax();
        this._logger = new Logger(this.constructor.name, true);
        this._syncState({});
    }

    _mounted() {
        if (this._type === null) {
            throw new Error("Componnet type missing.");
        }

        this._el.setAttribute(Component.NODE_TYPE_SEL, this._type);
        this._el.setAttribute("id", this._id);
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
    _hydrate() {
        if (!(this._el && this._hydrated)) return;

        const hostEl = document.createElement("div");
        hostEl.setAttribute(Component.NODE_HYDRATED_SEL, true);
        hostEl.setAttribute("id", this._fallbackHookId);

        const element = this._el.cloneNode(true);

        hostEl.appendChild(element);
        this._el.replaceWith(hostEl);

        this._hostEl = hostEl;
        this._el = element;
    }

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
        const html = this._template();
        const template = document.createElement("template");
        template.innerHTML = html.trim();
        const content = template.content.firstElementChild;

        if (!this._el) {
            this._hostEl.innerHTML = "";
            this._hostEl.appendChild(content);
            this._el = content;
        } else {
            this._el.replaceWith(content);
            this._el = content;
        }

        this._mounted();
        this._connectBindings();
        this._callMountedHooks();
    }

    render() {
        this._render();
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
        this._callDestroyedHooks();
        this._el?.remove();
        this._hostEl?.remove();
        this._logger.info("Component destroyed");
    }

    /** =============================
     * State Management
     ============================== */
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

    watch(keyPath, callback) {
        if (!this._watchers[keyPath]) {
            this._watchers[keyPath] = [];
        }

        this._watchers[keyPath].push(callback);
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
        const prevState = { ...this._state };

        Object.keys(newState).forEach((key) => {
            const oldVal = this._state[key];
            const newVal = newState[key];

            if (oldVal !== newVal) {
                this._state[key] = newVal;
                this._triggerWatchers(key, newVal, oldVal);
            }
        });

        // this._logger.info(`State updated`, {
        //     from: prevState,
        //     to: this._state,
        // });

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
