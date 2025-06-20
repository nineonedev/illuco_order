import Ajax from './Ajax';
import Logger from "../supports/Logger";

export default class Controller {
    constructor(){
        this._logger = new Logger(this.constructor.name);
        this._ajax = new Ajax();
    }

    _listen(eventName, callback) {
        if (!eventName || typeof callback !== 'function') {
            this._logger.warn(`Invalid listen() parameters`, `@${eventName}`);
            return;
        }

        document.body.addEventListener(`@${eventName}`, (event) => {
            callback(event.detail, event);
        });
        this._logger.success(`Listening globally to '@${eventName}'`);
    }

    async _process(e, callback = async () => {}) {
        e.preventDefault();

        const t = e.target;
        const data = new FormData(t);
        const action = t.action;

        try {
            e.submitter.disabled = true;
            const result = await callback(data, action);
            return result; 

        } catch(error) {
            this._logger.error('요청 처리 중 오류 발생', error);
        } finally {
            e.submitter.disabled = false;
        }
    }
}
