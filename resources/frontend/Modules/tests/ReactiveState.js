export default class ReactiveState {
    constructor(initialState = {}) {
        this._initialState = initialState;
        this._state = {...this._initialState};
    }
    
    get(key) {
        return this._state[key];
    }

    set(key, value) {
        this._state[key] = value;
    }

    getAll() {
        return this._state;
    }

    update(newState){
        const changes = {};
        for (const key in newState) {
            if (this._state[key] !== newState[key]) {
                changes[key] = { from: this._state[key], to: newState[key] };
                this._state[key] = newState[key];
            }
        }
        return changes;
    }

    getChanges() {
        const diff = {};
        for (const key in this._state) {
            if (this._state[key] !== this._initialState[key]) {
                diff[key] = {
                    from: this._initialState[key],
                    to: this._state[key],
                };
            }
        }
        return diff;
    }

    isDirty() {
        return Object.keys(this.getChanges()).length > 0;
    }

    isClean() {
        return !this.isDirty();
    }
}
