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
        const isFormData = data instanceof FormData;
        const originalMethod = method.toUpperCase();
        const override = ['PUT', 'PATCH', 'DELETE'].includes(originalMethod);

        const actualMethod = override ? 'POST' : originalMethod;

        // _method 처리
        if (override) {
            if (isFormData) {
                if(data.has('_method')) {
                    data.set('_method', originalMethod);
                } else {
                    data.append('_method', originalMethod);
                }
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
            config.body = isFormData ? data : JSON.stringify(data);
            if (!isFormData) {
                config.headers['Content-Type'] = 'application/json';
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
