export const Store = {
    state: {},
    watchers: {},

    set(key, value) {
        const old = this.state[key];
        this.state[key] = value;

        if (this.watchers[key]) {
            this.watchers[key].forEach((fn) => fn(value, old));
        }
    },

    get(key) {
        return this.state[key];
    },

    watch(key, callback) {
        if (!this.watchers[key]) this.watchers[key] = [];
        this.watchers[key].push(callback);
    },
};

export function createStore(initialState = {}) {
    const watchers = {};
    const store = new Proxy(initialState, {
        get(target, prop) {
            return target[prop];
        },
        set(target, prop, value) {
            const oldValue = target[prop];
            target[prop] = value;

            if (watchers[prop] && oldValue !== value) {
                watchers[prop].forEach((cb) => cb(value, oldValue));
            }

            return true;
        },
    });

    store.watch = (key, callback) => {
        if (!watchers[key]) watchers[key] = [];
        watchers[key].push(callback);
    };

    return store;
}
