export default class Ajax {
    constructor(silent = false, headers = {}) {
        this._silent = silent;
        this._defaultHeaders = headers;
    }

    static make(silent = false, headers = {}) {
        return new this(silent, headers);
    }

    csrfToken() {
        return document.querySelector('meta[name="_csrf_token"]')?.content;
    }

    async request(method, url, data = {}, options = {}) {
        const originalMethod = method.toUpperCase();
        const override = ['PUT', 'PATCH', 'DELETE'].includes(originalMethod);
        const actualMethod = override ? 'POST' : originalMethod;

        const isFormData = data instanceof FormData;
        const isUrlEncoded = data instanceof URLSearchParams;
        const isJson = !isFormData && !isUrlEncoded;

        // _method 처리
        if (override) {
            if (isFormData) {
                data.has('_method')
                    ? data.set('_method', originalMethod)
                    : data.append('_method', originalMethod);
            } else if (isUrlEncoded) {
                data.set('_method', originalMethod);
            } else {
                data = { ...data, _method: originalMethod };
            }
        }

        const headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': this.csrfToken(),
            ...this._defaultHeaders,
            ...options.headers,
        };

        const config = {
            method: actualMethod,
            headers,
            ...options,
        };

        if (actualMethod !== 'GET') {
            config.body = isFormData || isUrlEncoded ? data : JSON.stringify(data);

            if (isJson) {
                config.headers['Content-Type'] = 'application/json';
            } else if (isUrlEncoded) {
                config.headers['Content-Type'] = 'application/x-www-form-urlencoded';
            }
        }

        try {
            const response = await fetch(url, config);
            const resData = await response.json();

            if (!response.ok) {
                throw new Error(`[${response.status}] ${resData?.message || response.statusText}`);
            }

            if (resData.message && !this._silent) alert(resData.message);
            if (resData.redirect) location.href = resData.redirect;

            return resData;
        } catch (e) {
            if (!this._silent) alert(e.message);
            console.warn(`[Ajax ERROR] ${originalMethod} ${url}`, e);
            throw e;
        }
    }

    setSilent(silent = false) {
        this._silent = silent;
        return this;
    }

    get(url, options = {}) {
        return this.request('GET', url, {}, options);
    }

    post(url, data = {}, options = {}) {
        return this.request('POST', url, data, options);
    }

    put(url, data = {}, options = {}) {
        return this.request('PUT', url, data, options);
    }

    patch(url, data = {}, options = {}) {
        return this.request('PATCH', url, data, options);
    }

    delete(url, data = {}, options = {}) {
        return this.request('DELETE', url, data, options);
    }
}
