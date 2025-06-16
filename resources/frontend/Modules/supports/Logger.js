export default class Logger {
    constructor(context = 'App', debug = true) {
        this._context = context;
        this._debug = debug; 
    }

    getCallerInfo() {
        const stack = new Error().stack;
        const lines = stack.split('\n');
        const methodLine = lines[4] || '';
        const match = methodLine.match(/at\s+(.*?)\s+\(/);
        const method = match ? match[1].split('.').pop() : 'anonymous';
        return method;
    }

    formatPrefix(method, theme) {
        return [
            `%c${this._context}%c ${method}()`,
            theme.contextStyle,
            theme.methodStyle,
        ];
    }

    print(theme, ...data) {
        if (!this._debug) return; 

        const method = this.getCallerInfo();
        const prefix = this.formatPrefix(method, theme);

        if (data.length === 1 && typeof data[0] === 'object') {
            console.groupCollapsed(...prefix);
            console.dir(data[0]);
            console.groupEnd();
        } else {
            console.log(...prefix, ...data);
        }
    }

    info(...data) {
        this.print({
            contextStyle: 'background: #007acc; color: white; font-weight: bold; padding: 2px 6px; border-radius: 4px;',
            methodStyle: 'color: #007acc; font-weight: bold;',
        }, ...data);
    }

    success(...data) {
        this.print({
            contextStyle: 'background: #28a745; color: white; font-weight: bold; padding: 2px 6px; border-radius: 4px;',
            methodStyle: 'color: #28a745; font-weight: bold;',
        }, ...data);
    }

    warn(...data) {
        this.print({
            contextStyle: 'background: #ffcc00; color: black; font-weight: bold; padding: 2px 6px; border-radius: 4px;',
            methodStyle: 'color: #ff9900; font-weight: bold;',
        }, ...data);
    }

    error(...data) {
        this.print({
            contextStyle: 'background: #d32f2f; color: white; font-weight: bold; padding: 2px 6px; border-radius: 4px;',
            methodStyle: 'color: #d32f2f; font-weight: bold;',
        }, ...data);
    }
}
