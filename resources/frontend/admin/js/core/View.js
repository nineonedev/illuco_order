import Logger from "../supports/Logger";

export default class View {
    static hookCount = 0;
    static elementCount = 0; 
    static SEL_HOST_CREATED = 'data-view-created';
    static SEL_HOST_HYDRATED = 'data-view-hydrated';
    static SEL_HAS_PROPS = 'data-view-props';
    static SEL_HAS_TYPE = 'data-view-type';
    static SEL_HAS_STATE = 'data-view-state';

    
    constructor(hookId, props = {}, boot = true){
        this._hookId = hookId;
        this._hostEl = null;
        this._el = null;
        this._id = this._generateElementId();
        this._type = null;

        this._hostCreated = false;
        this._booted = false; 
        this._hydrated = false; 
        
        this._oldProps = props;
        this._props = {};
        this._oldState = {};
        this._state = {};
        this._children = [];
        this._refs = {};

        this._watchers = {};
        this._computed = {};
        this._computedCache = {};
        this._isWrapper = false;

        this._mounted = false;
        this._mountedCallbacks = [];
        
        if (boot) {
            this._boot();
        }
    }

    _boot(){
        if (this._booted) return;

        this._setup();
        this._register();
        this._configureHookAndHost(this._hookId);
        this._hydrateIfNeeded();
        this._booted = true; 
        
        this._afterBoot();
    }

    _afterBoot(){}

    _setup(){
        this._computed = {...this._computed, ...this._defineComputed()};
    }
    
    _register(){
        this._logger = Logger.make(this.constructor.name);
    }

    setHookId(hookId){
        this._configureHookAndHost(hookId);
        return this; 
    }

    _configureHookAndHost(hookId){
        let insertHookId;
        let hostEl;

        if (hookId instanceof HTMLElement){

            const element = hookId;
            
            insertHookId = this._generateHookId();
            const hostElement = document.createElement('div');
            hostElement.setAttribute('id', insertHookId);
            hostElement.setAttribute(View.SEL_HOST_CREATED, true);

            [...element.attributes].forEach((attr) => {
                if (attr.name === 'id') {
                    insertHookId = attr.value; 
                }
                hostElement.setAttribute(attr.name, attr.value);
            });
            
            element.replaceWith(hostElement);
            hostElement.appendChild(element);

            this._el = element;
            this._hostCreated = true;
            hostEl = hostElement;
            

        } else {
            insertHookId = hookId;
            hostEl = document.getElementById(insertHookId);
            this._hostCreated = false;
                
            if (!hostEl) {
                console.log(this);
                throw new Error(`No found Hook Id: ${insertHookId}`)
            }
        }

        this._hookId = insertHookId;
        this._hostEl = hostEl;
    }

    _defineProps(){
        return {};
    }

    _defineState(){
        return {};
    }

    _defineComputed() {
        return {};

        // return {
        //     title: () => `Hello, ${this._state.name}`,
        //     doubled: () => this._state.count * 2
        // };
    }

    _hydrateIfNeeded(){
        const element = this._hostEl; 
        
        const isHydrated = element.hasAttribute(View.SEL_HAS_TYPE)
            || element.hasAttribute(View.SEL_HAS_PROPS)
            || element.hasAttribute(View.SEL_HAS_STATE);
        
        if (isHydrated) {
            this._hydrated = true; 
        } else {
            this._hydrated = false; 
        }

        const elementProps = element.getAttribute(View.SEL_HAS_PROPS) || '{}';
        const elementState = element.getAttribute(View.SEL_HAS_STATE) || '{}';

        let props = {}, state = {};
        try {
            props = JSON.parse(elementProps || '{}');
        } catch (e) {
            console.warn("Invalid props JSON in hydration:", elementProps);
        }
        try {
            state = JSON.parse(elementState || '{}');
        } catch (e) {
            console.warn("Invalid state JSON in hydration:", elementState);
        }

        this._syncProps(props);
        this._syncState(state);
    }

    _syncProps(props = {}){
        this._props = {...this._defineProps(), ...this._oldProps, ...props};
    }

    _syncState(state = {}){
        this._oldState = {...this._defineState(), ...state};
        this._state = {...this._oldState};
    }

    get children(){
        return this._children;
    }

    get props(){
        return this._props;
    }

    get state(){
        return this._state;
    }

    get computed() {
        const computedDefs = this._defineComputed();
        const computedKeys = Object.keys(computedDefs);
        const computedValues = {};

        for (const key of computedKeys) {
            if (!(key in this._computedCache)) {
                try {
                    this._computedCache[key] = computedDefs[key].call(this);
                } catch (e) {
                    console.warn(`Error in computed "${key}"`, e);
                }
            }
            computedValues[key] = this._computedCache[key];
        }

        return computedValues;
    }

    get refs() {
        return this._refs;
    }

    get slots() {
        return this._slots;
    }

    
    render(){
        this._render();
        return this; 
    }

    _render(){
        if (!this._booted) {
            this._boot();
            console.log(this);
            
        }

        this._renderElement();
        this._fireBindings();
        this._fireMounted();
    }

    _renderElement(){
        const html = this._template();
        let content;

        if (html instanceof HTMLElement) {
            content = html;
        } else {
            const template = document.createElement('template')
            template.innerHTML = html.trim();
            content = template.content.firstElementChild;

        }
        
        if (!this._el) {
            this._hostEl.appendChild(content);
        } else {
            this._el.replaceWith(content);
        }

        this._el = content;
        this._el.setAttribute('id', this._id);
    }

    _fireBindings(){
        this._bindRefs();
        this._bindSlots();
        this._bindEvents();
    }

    fresh(){
        this.setState({...this._oldState});
    }

    setState(newState = {}, shouldRender = true){
        let hasChanged = false;

        Object.keys(newState).forEach((key) => {
            const oldVal = this._state[key];
            const newVal = newState[key];

            if (oldVal !== newVal) {
                this._state[key] = newVal;
                this._triggerWatchers(key, newVal, oldVal);
                hasChanged = true;
            }
        });

        if (hasChanged) {
            this._computedCache = {};
        }

        if (shouldRender) {
            this._render();
        }
    }

    _triggerWatchers(key, newVal, oldVal) {
        const watchers = this._watchers[key] || [];
        for (const cb of watchers) {
            try {
                cb.call(this, newVal, oldVal);
            } catch (e) {
                console.warn(`Watcher error on "${key}"`, e);
            }
        }
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

    watch(key, callback) {
        if (!this._watchers[key]) {
            this._watchers[key] = [];
        }
        this._watchers[key].push(callback);
        return this;
    }
    
    _bindEvents(){

    }

    _bindSlots() {
        this._slots = {};
        const slotNodes = this.qsAll('[data-slot]');

        slotNodes.forEach((node) => {
            const name = node.getAttribute('data-slot') || 'default';
            this._slots[name] = node;
        });
    }

    addChild(view) {
        if (!(view instanceof View)) return;
        this._children.push(view);
        return this;
    }

    qs(selector) {
        return this._el?.querySelector(selector) || null;
    }

    qsAll(selector) {
        return [...(this._el?.querySelectorAll(selector) || [])];
    }

    on(selector, eventName, callback, strict = false) {
        const el = selector instanceof HTMLElement ? selector : this.qs(selector);

        if (!el) {
            if (strict) {
                throw new Error(`Event binding failed: '${eventName}' on '${selector}'`);
            }
            return;
        }

        el.addEventListener(eventName, (e) => callback(this, e));
    }

    off(selector, eventName, callback) {
        const el = selector instanceof HTMLElement ? selector : this.qs(selector);
        if (!el) return;

        el.removeEventListener(eventName, callback);
    }

    mountTo(target) {
        const el = target instanceof HTMLElement
            ? target
            : document.querySelector(target);

        if (!el) {
            throw new Error("Mount target not found");
        }

        el.appendChild(this._hostEl);
        return this;
    }

    remove(){
        if (this._el && this._el.parentNode) {
            this._el.remove();
        }

        if (this._hostEl && this._hostCreated && this._hostEl.parentNode) {
            this._hostEl.remove();
        }
    }

    destroy() {
        this._children.forEach((child) => {
            if (typeof child.destroy === "function") {
                child.destroy();
            }
        });
        this._children = [];
        
        this.remove();
        this._refs = {};
        this._props = {};
        this._state = {};
        this._oldState = {};
        this._slots = {};
        this._computed = {};
        this._computedCache = {};
        this._el = null; 
        this._hostEl = null;  
        this._mounted = false;
        this._mountedCallbacks = [];
    }


    _dispatch(name, detail = {}){
        const event = new CustomEvent(`@${name}`, {
            detail,
            bubbles: true,
            cancelable: true,
        });

        document.body.dispatchEvent(event);
    }

    _fireMounted() {
        if (this._mounted) return;

        this._mounted = true;
        for (const cb of this._mountedCallbacks) {
            try {
                cb.call(this);
            } catch (e) {
                console.warn('Error in onMounted callback:', e);
            }
        }

        this._mountedCallbacks = [];
    }


    onMounted(callback) {
        if (typeof callback === 'function') {
            this._mountedCallbacks.push(callback);
        }
    }

    _template(){
        return `<div></div>`
    }

    _generateHookId(){
        return `view-hook-${View.hookCount++}`;
    }
    
    _generateElementId(){
        return `view-element-${View.elementCount++}`;
    }

    static make(hookId, props = {}, boot = true){
        return new this(hookId, props, boot)
    }
}