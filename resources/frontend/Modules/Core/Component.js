import Logger from "../supports/Logger";

export default class Component {
    constructor(hookId, props = {}, hydrated = false) {
        this._hookId = hookId;
        this._hydrated = hydrated;
        this._hasHydratedOnce = false;

        this._debug = true;
        this._logger = null;
        this._el = null;
        this._hostEl = null;
        this._children = [];

        this._initialProps = props;
        this._props = { ...this._initialProps, ...this._defineProps() };

        this._initialState = this._defineState();
        this._state = { ...this._initialState };

        this._setup();
        this._render();
    }

    static make(hookId, props = {}, hydrated = false) {
        return new this(hookId, props, hydrated);
    }

    /** =============================
     * Setup & Boot
     ============================== */
    _setup() {
        const el = document.getElementById(this._hookId);
        if (!el) throw new Error(`Element #${this._hookId} not found`);

        this._hostEl = el;
        this._el = this._hydrated ? el : null;

        this._boot();
        this._register();
    }

    _boot() {
        this._logger = new Logger(this.constructor.name, this._debug);
        this._logger.info(`Component booted`);
    }

    _register() {}

    /** =============================
     * Rendering
     ============================== */
    _render() {
        if (this._hydrated) {
            if (!this._hasHydratedOnce) {
                this._logger.info(`Hydrated existing DOM`);
                this._fireBindingCallbacks();
                this._hasHydratedOnce = true;
            } else {
                this._logger.info(`Skipped redundant hydration re-bind`);
            }
            
            return;
        }

        const html = this._template();
        const template = document.createElement('template');
        template.innerHTML = html.trim();
        const content = template.content.firstElementChild;

        if (!this._el) {
            this._hostEl.innerHTML = '';
            this._hostEl.appendChild(content);
            this._el = content;
        } else {
            this._el.replaceWith(content);
            this._el = content;
        }

        this._logger.info(`Rendered`);
        this._fireBindingCallbacks();
    }

    render() {
        this._render();
    }

    _fireBindingCallbacks() {
        this._bind();
        this._bindEvents();
        this._bound();
    }

    /** =============================
     * Lifecycle Hooks (override)
     ============================== */
    _defineState() {
        return {};
    }

    _defineProps() {
        return {};
    }

    _defineComputed() {
        return {};
    }

    _template() {
        return '<div></div>';
    }

    _bind() {}
    _bindEvents() {}
    _bound() {}

    /** =============================
     * State Management
     ============================== */
    get state() {
        return this._state;
    }

    get props() {
        return this._props;
    }

    setState(newState = {}, shouldRender = true) {
        const prevState = { ...this._state };
        Object.assign(this._state, newState);

        this._logger.info(`State updated`, {
            from: prevState,
            to: this._state,
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
        if (typeof handler === 'function') {
            this._logger.info(`Emitting '${eventName}'`, data);
            handler(data);
        } else {
            this._logger.warn(`No handler for emitted event '${eventName}'`);
        }
    }

    _dispatch(name, detail = {}) {
        const event = new CustomEvent(`@${name}`, {
            detail,
            bubbles: true,
            cancelable: true,
        });

        this._logger.info(`Dispatched custom event '@${name}'`, detail);
        document.body.dispatchEvent(event);
    }

    /** =============================
     * DOM Utilities
     ============================== */
    _qs(selector) {
        return this._el?.querySelector(selector);
    }

    _qsAll(selector) {
        return [...(this._el?.querySelectorAll(selector) || [])];
    }

    _on(selector, eventName, callback, strict = false) {
        const el = selector instanceof HTMLElement ? selector : this._qs(selector);

        if (!el) {
            const msg = `Event binding failed: '${eventName}' on`;
            this._logger.warn(msg, selector);
            if (strict) throw new Error(msg);
            return;
        }

        el.addEventListener(eventName, callback);
        this._logger.info(`Bound '${eventName}' to`, selector);
    }

    _off(selector, eventName, callback) {
        const el = this._qs(selector);
        if (!el) {
            this._logger.warn(`Failed to unbind '${eventName}' on`, selector);
            return;
        }

        el.removeEventListener(eventName, callback);
        this._logger.info(`Unbound '${eventName}' from`, selector);
    }

    /** =============================
     * Debug Helper
     ============================== */
    get logger() {
        return this._logger;
    }
}
