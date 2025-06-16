import Component from "../../modules/core/Component";

export default class File extends Component {
    _defineProps(){
        return {
            text: '선택된 파일 없음',
        }
    }

    _template(){
        return `
            <div class="no-form-control no-form-file">
                <label for="file_attachable[]" class="no-form-control-inner no-form-file-inner">
                    <input 
                        type="file" 
                        name="file_attachable[]" 
                        id="file_attachable[]" 
                        class="no-form-control-input" 
                    >
                    <fieldset class="no-form-control-label">
                        <legend class="no-form-control-text">첨부파일</legend>
                    </fieldset>
                    <button class="no-form-file-input" type="button">
                        <div class="no-form-file-icon">
                            <i class="fa-light fa-paperclip-vertical"></i>
                        </div>
                        <span class="no-form-file-text"></span>
                        <span class="no-form-file-button-text">${this._props.text}</span>
                    </button>
                </label>
                <span class="no-form-control-space"></span>
            </div>
        `;
    }

    _bind(){
        this._inputEl = this._on('input');
        this._buttonEl = this._on('button');
        this._textEl = this._on('.no-form-file-text');
    }

    _bindEvents(){
        this._on(this._buttonEl, 'click', this._handleSelect.bind(this));
        this._on(this._inputEl, 'change', this._handleChange.bind(this));
    }

    _handleSelect(e){
        this._inputEl.click();
    }

    _handleChange(e){
        this._textEl.textContent = e.target.value ?? this._props.text;
    }
    
}