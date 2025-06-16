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
}
