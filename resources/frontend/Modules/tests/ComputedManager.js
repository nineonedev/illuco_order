export default class ComputedManager {
    constructor(component) {
        this._component = component;
        this._rawDefinitions = {};  // computed 정의 원본
        this._cache = {};           // 캐시된 결과값
    }

    computed(definitions) {
        this._rawDefinitions = definitions || {};
        this._setup();
    }

    _setup() {
        for (const key in this._rawDefinitions) {
            Object.defineProperty(this, key, {
                get: () => {
                    if (!(key in this._cache)) {
                        this._cache[key] = this._rawDefinitions[key].call(this._component);
                    }
                    return this._cache[key];
                },
                enumerable: true,
            });
        }
    }

    invalidate() {
        this._cache = {}; // 모든 computed 캐시 무효화
    }
}
