import Form from '../../modules/components/Form';
import Controller from '../../modules/core/Controller';
import File from '../components/File';
import Summernote from '../components/Summernote';

export default class NoticeController extends Controller {
    index(){
        this._logger.info('index');
    }

    create(){
        this._logger.info('create');
        Summernote.make('#content');

        const form = Form.make('form-hook', {}, true);
        new File('file-hook');


        this._listen('store', this._store.bind(this));
    }

    edit(){
        this._logger.info('edit');
    }

    async _store({data, action}, evt){
        const resData = await this._ajax.post(action, data);
        console.log(resData);
    }
}