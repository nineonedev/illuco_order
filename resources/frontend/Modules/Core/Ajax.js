export default class Ajax {
    constructor(silent = false, headers = {}) {
        this._silent = silent;
        this._defaultHeaders = headers;
    }

    csrfToken() {
        return document.querySelector('meta[name="_csrf_token"]')?.content;
    }

    async request(method, url, data = {}, options = {}) {
        const isFormData = data instanceof FormData;
        const upperMethod = method.toUpperCase();

        const needsOverride = ['PATCH', 'PUT', 'DELETE'].includes(upperMethod);
        const actualMethod = needsOverride ? 'POST' : upperMethod;

        if (!isFormData && needsOverride) {
            data._method = upperMethod;
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
            config.body = isFormData ? data : JSON.stringify(data);
            if (!isFormData) {
                config.headers['Content-Type'] = 'application/json';
            }
        }

        try {
            const response = await fetch(url, config);
            const resData = await response.json();

            if (!response.ok) {
                const errorMessage = resData?.message || response.statusText;
                throw new Error(`[Error ${response.status}] ${errorMessage}`);
            }

            if (resData.message && !this._silent) alert(resData.message);
            if (resData.redirect) location.href = resData.redirect;

            return resData;

        } catch (e) {
            if (!this._silent) alert(e.message);
            console.warn(`[Ajax ERROR] ${method} ${url}`, e);
            throw e;
        }
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
