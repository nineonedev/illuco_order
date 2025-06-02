class Component {
    constructor(hookId, props = {}) {
        this._hookId = hookId;
        this._props = props;
        this._state = {};
        this._rootEl = null;
        this._hostEl = null;

        this._watchers = {};
        this._children = [];
        this._computed = {};
        this._lifecycle = {};
    }

    flush() {
        this._hookId = null;
        this._props = {};
        this._state = {};
        this._rootEl = null;
        this._hostEl = null;
    }

    setProps() {}

    setState() {}

    renderElement(html) {
        const template = document.createElement("template");
        template.innerHTML = html.trim();
        return template.content.firstChild;
    }

    _render() {}
}

class AsyncComponent extends Component {
    async _render() {}

    renderFallback() {}

    renderError() {}
}
const PROCESSOR_EVENTS = {
    REQUEST_START: "request:start",
    REQUEST_SUCCESS: "request:success",
    REQUEST_ERROR: "request:error",
};

export default class Processor {
    constructor(baseURL = "") {
        this.baseURL = baseURL;
        this._events = {};
        this._headers = {};
    }

    setHeader(key, value) {
        this._headers[key] = value;
    }

    removeHeader(key) {
        delete this._headers[key];
    }

    on(event, callback) {
        if (!this._events[event]) this._events[event] = [];
        this._events[event].push(callback);
    }

    emit(event, payload) {
        const listeners = this._events[event] || [];
        listeners.forEach((cb) => cb(payload));
    }

    async request(method, url, data = {}, config = {}) {
        this.emit(PROCESSOR_EVENTS.REQUEST_START, { method, url, data });

        const isForm = data instanceof FormData;
        const headers = Object.assign({}, this._headers, config.headers || {});

        let body;
        if (method !== "GET" && method !== "HEAD") {
            if (isForm) {
                body = data;
            } else {
                body = new FormData();
                Object.keys(data).forEach((key) => {
                    body.append(key, data[key]);
                });
            }
        }

        try {
            const response = await fetch(this.baseURL + url, {
                method,
                headers,
                body,
                credentials: "same-origin", // include cookies
                ...config,
            });

            const contentType = response.headers.get("Content-Type");
            const isJSON = contentType?.includes("application/json");
            const result = isJSON
                ? await response.json()
                : await response.text();

            if (!response.ok) throw { status: response.status, result };

            this.emit(PROCESSOR_EVENTS.REQUEST_SUCCESS, result);
            return result;
        } catch (err) {
            this.emit(PROCESSOR_EVENTS.REQUEST_ERROR, err);
            throw err;
        }
    }

    post(url, data = {}, config = {}) {
        return this.request("POST", url, data, config);
    }

    get(url, params = {}, config = {}) {
        const query = new URLSearchParams(params).toString();
        const fullUrl = query ? `${url}?${query}` : url;
        return this.request("GET", fullUrl, null, config);
    }

    put(url, data = {}, config = {}) {
        return this.request("PUT", url, data, config);
    }

    delete(url, data = {}, config = {}) {
        return this.request("DELETE", url, data, config);
    }
}

const input = new Component("#query", {});
input.on("input", async () => {});

class Client {
    components = [
        new Component(),
        new AsyncComponent(),
        new Component(),
        new AsyncComponent(),
        new Component(),
        new Component(),
    ];
    async init() {
        for (const comp of this.components) {
            if (typeof comp.render !== "function") continue;

            const result = comp.render();
            if (result instanceof Promise) {
                await result;
            }
        }
    }
}

new Client().init();
