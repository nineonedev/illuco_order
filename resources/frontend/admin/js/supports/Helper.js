export default class Helper {
    /**
     * 숫자를 통화 형식으로 변환
     * 예: 13.22 -> "$13.22"
     */
    static formatCurrency(value, currency = 'USD', locale = 'en-US') {
        const number = Number(value);
        if (isNaN(number)) return '$0.00'; // fallback
        return number.toLocaleString(locale, {
            style: 'currency',
            currency,
        });
    }

    static debounce(fn, delay) {
        let timer = null;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => {
                fn.apply(this, args);
            }, delay);
        };
    }
    
    /**
     * 객체가 비어 있는지 확인
     */
    static isEmptyObject(obj) {
        return obj && typeof obj === 'object' && !Array.isArray(obj) && Object.keys(obj).length === 0;
    }

    /**
     * 배열이 비어 있는지 확인
     */
    static isEmptyArray(arr) {
        return Array.isArray(arr) && arr.length === 0;
    }

    /**
     * 문자열이 비어 있는지 확인 (null, undefined 포함)
     */
    static isEmptyString(str) {
        return typeof str !== 'string' || str.trim() === '';
    }

    /**
     * null 또는 undefined 여부 확인
     */
    static isNil(value) {
        return value === null || value === undefined;
    }

    /**
     * 값이 정의되어 있는지 확인
     */
    static isDefined(value) {
        return !Helper.isNil(value);
    }

    /**
     * 깊은 복사 (JSON 기반, 순수 데이터용)
     */
    static deepClone(obj) {
        return JSON.parse(JSON.stringify(obj));
    }

    /**
     * 타입 문자열 반환 ("array", "object", "null", "string" 등)
     */
    static getType(value) {
        if (value === null) return 'null';
        if (Array.isArray(value)) return 'array';
        return typeof value;
    }

    /**
     * 정해진 범위 안에 숫자가 포함되는지 확인
     */
    static between(num, min, max) {
        return typeof num === 'number' && num >= min && num <= max;
    }

    /**
     * 값이 숫자인지 확인
     */
    static isNumeric(value) {
        return !isNaN(parseFloat(value)) && isFinite(value);
    }

    /**
     * UUID (v4) 생성기 (간단 버전)
     */
    static uuid() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    /**
     * 현재 타임스탬프 (ms 단위)
     */
    static now() {
        return Date.now();
    }

    /**
     * 문자열을 파스칼 케이스로 변환
     * 예: "hello_world" -> "HelloWorld"
     */
    static toPascalCase(str) {
        return str
            .replace(/[_\- ]+/g, ' ')
            .replace(/(?:^|\s)(\w)/g, (_, c) => c.toUpperCase())
            .replace(/\s+/g, '');
    }
}
