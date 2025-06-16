export default class WatcherManager {
    constructor(component) {
        this._component = component;
        this._watchers = []; // { getter, callback, prev }
    }

    register(getterFn, callbackFn) {
        const prev = getterFn.call(this._component);
        this._watchers.push({ getter: getterFn, callback: callbackFn, prev });
    }

    notify() {
        for (const entry of this._watchers) {
            const newVal = entry.getter.call(this._component);
            if (newVal !== entry.prev) {
                entry.callback.call(this._component, newVal, entry.prev);
                entry.prev = newVal;
            }
        }
    }
}
