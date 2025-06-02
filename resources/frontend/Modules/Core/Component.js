export default class Component {
    static ON_UPDATING = "updating";
    static ON_UPDATED = "updated";
    static ON_DESTROYING = "destroying";
    static ON_DESTROYED = "destroyed";
    static ON_MOUNTING = "mounting";
    static ON_MOUNTED = "mounted";

    constructor(hookId, props = {}) {
        this._hostEl = null;
        this._rootEl = null;
        this._hookId = hookId;
        this._props = props;
        this._state = {};
        this._children = [];

        this._parent = null;
        this._watchers = {};
        this._computed = {};
        this._lifecycle = {};
        this._cache = new Map();

        this.setHost(hookId);
        this._setup();
        this._mount();
    }

    get root() {
        return this._rootEl;
    }

    get props() {
        return this._props;
    }

    get host() {
        return this._hostEl;
    }

    get state() {
        return Object.freeze(this._state);
    }

    _fireUpdateCallback(
        oldObj,
        newObj,
        options = { shouldRender: true, prefix: "" }
    ) {
        const changedKeys = Object.keys(newObj).filter(
            (key) => oldObj[key] !== newObj[key]
        );
        if (changedKeys.length === 0) return;

        const { shouldRender, prefix } = options;

        for (const key of changedKeys) {
            const oldValue = oldObj[key];
            const newValue = newObj[key];
            oldObj[key] = newValue;

            const watcherKey = prefix ? `${prefix}.${key}` : key;
            const watchers = this._watchers[watcherKey];
            if (watchers) {
                watchers.forEach((cb) => cb(newValue, oldValue));
            }
        }

        if (shouldRender) this._update();
    }

    setProps(newProps, shouldRender = true) {
        this._fireUpdateCallback(this._props, newProps, {
            shouldRender,
            prefix: "props",
        });
    }

    setState(newState, shouldRender = true) {
        this._fireUpdateCallback(this._state, newState, { shouldRender });
    }

    addChild(child) {
        if (!(child instanceof Component)) return;
        child._parent = this;
        this._children.push(child);
    }

    removeChild(child) {
        if (!(child instanceof Component)) return;
        this._children = this._children.filter((c) => c !== child);
        child.flush();
    }

    defineComputed(key, computeFn) {
        let cachedValue;
        Object.defineProperty(this._computed, key, {
            get: () => {
                const newValue = computeFn.call(this);
                if (newValue !== cachedValue) cachedValue = newValue;
                return cachedValue;
            },
            enumerable: true,
        });
    }

    get computed() {
        return this._computed;
    }

    emit(eventName, payload) {
        const handler = this._props?.on?.[eventName];
        if (typeof handler === "function") handler(payload);
    }

    watch(key, callback) {
        if (!this._watchers[key]) this._watchers[key] = [];
        this._watchers[key].push(callback);
    }

    on(hookName, callback) {
        this._lifecycle[hookName] = callback;
    }

    _render() {}

    _renderElement(string) {
        const html = string || "";

        const template = document.createElement("template");
        template.innerHTML = html.trim();

        return template.content.firstChild;
    }

    flush() {
        for (const child of this._children) {
            if (typeof child.flush === "function") {
                child.flush();
            }
        }

        this._unmount();
        this._children = [];
        this._state = {};
        this._props = {};
        this._hookId = null;
        this._watchers = {};
        this._computed = {};
        this._lifecycle = {};
    }

    _unmount() {
        this._runLifecycle(Component.ON_DESTROYING);
        if (this._rootEl) {
            this._rootEl.remove();
            this._rootEl = null;
        }
        this._runLifecycle(Component.ON_DESTROYED);
    }

    _update() {
        this._runLifecycle(Component.ON_UPDATING);
        const newRootEl = this._renderElement(this._render());

        if (this._rootEl && newRootEl instanceof HTMLElement) {
            this._rootEl.replaceWith(newRootEl);
        } else if (this._hostEl) {
            this._hostEl.innerHTML = "";
            this._hostEl.appendChild(newRootEl);
        }

        this._rootEl = newRootEl;
        this._setEvents();
        this._runLifecycle(Component.ON_UPDATED);
    }

    // async _finalizeMount(el) {
    //     if (this._rootEl && el !== this._rootEl) {
    //         this._rootEl.replaceWith(el);
    //     } else {
    //         this._hostEl.innerHTML = "";
    //         this._hostEl.appendChild(el);
    //     }

    //     this._rootEl = el;
    //     await this._mountChildren();
    //     this._setEvents();
    //     this._runLifecycle(Component.ON_MOUNTED);
    // }

    async _mount() {
        this._runLifecycle(Component.ON_MOUNTING);

        if (this._render === Component.prototype._render) {
            throw new Error(`render must implement`);
        }

        if (typeof this._render === "async") {
        } else if (typeof this._render === "function") {
        }

        if (
            typeof this._render === "function" &&
            this._render !== Component.prototype._render
        ) {
            try {
                const asyncEl = this._render();
            } catch (e) {
                this._finalizeMount(asyncEl);
                const fallbackEl = this._createFallbackElement();
            }

            return;
        }

        const rootEl = this._render();
        this._finalizeMount(rootEl);
    }

    async _mountChildren() {
        for (const child of this._children) {
            await child._mount();
        }
    }

    _createRootElement(tag = "div", classList = [], attrs = {}) {
        const el = document.createElement(tag);

        if (Array.isArray(classList)) {
            el.className = classList.join(" ");
        }

        for (const key in attrs) {
            el.setAttribute(key, attrs[key]);
        }

        return el;
    }

    _createFragment(...children) {
        const fragment = document.createDocumentFragment();

        children.forEach((child) => {
            if (typeof child === "string") {
                fragment.appendChild(document.createTextNode(child));
            } else if (child instanceof Node) {
                fragment.appendChild(child);
            }
        });

        return fragment;
    }

    _createFallbackElement() {
        return this._renderElement("<p>Loading...</p>");
    }

    _createErrorElement() {
        return this._renderElement(
            `<p class="error">Something went wrong.</p>`
        );
    }

    _renderSlot(name = "default", scope = {}) {
        const slots = this._props?.slots || {};
        const slot = slots[name];
        return typeof slot === "function" ? slot(scope) : slot || "";
    }

    _runLifecycle(hookName) {
        const cb = this._lifecycle[hookName];
        if (typeof cb === "function") cb.call(this);
    }

    setHost(hookId) {
        const el = document.getElementById(hookId);
        if (!el) throw new Error(`Element with id ${hookId} not found`);
        this._hostEl = el;
        if (this._rootEl) {
            this._hostEl.innerHTML = "";
            this._hostEl.appendChild(this._rootEl);
        }
    }

    /**
     * @override
     */
    _setup() {}

    /**
     * @override
     */
    _render() {
        throw new Error("Component must implement its own _render() method.");
    }

    /**
     * @override
     */
    _setEvents() {}

    /**
     * @override (optional)
     */
    async _renderAsync() {}
}
