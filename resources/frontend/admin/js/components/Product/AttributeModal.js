import Component from "../../core/Component";

export default class AttributeModal extends Component {
    _type = 'attribute-modal';
    _boot(){
        super._boot(); 
        this._headerHookId = this._generateNodeId();
        this._contentHookId = this._generateNodeId();
    }

    _defineProps() {
        return {
            header: null,
            content: null,
            open: false,
            remove: false,
        };
    }

    _defineState() {
        return {
            ...this._props
        };
    }

    _template() {
        const {open} = this._state;
        return `
            <dialog open class="no-main-modal ${open ? '--open' : ''}">
                <div class="no-main-modal-wrapper">
                    <div class="no-main-modal-inner">
                        <header class="no-main-modal-header" data-slot="header">
                        </header>
                        <section class="no-main-modal-content" data-slot="content">
                        </section>
                        <button class="no-main-modal-close" aria-label="닫기" data-ref="closeBtn">
                            <i class="fa-regular fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </dialog>
        `;
    }

    _render(){
        super._render(); 
        this._renderHeader();
        this._renderContent(); 
    }

    _renderContent(){
        const content = this._state.content;
        if (content && content instanceof Component) {
            content.setHookId(this._slots.content).render();
        } else {
            this._slots.content.innerHTML = content;
        }
    }

    _renderHeader(){
        const header = this._state.header;

        if(header && header instanceof Component) {
            header.setHookId(this._slots.header).render();
        } else {
            this._slots.header.innerHTML = `<h2 class="no-main-modal-header__title">${header}<h2>`;
        }
    }

    _bindEvents(){
        this.on(this.refs.closeBtn, 'click', this._handleClose.bind(this)); 
    }

    _handleClose(){
        this._el.classList.remove('--open');
        
        if(this._state.remove){
            this.destroy();
        }
    }

    show(){
        this._el.classList.add('--open');
    }

    hide(self, e){
        this._handleClose();
    }
}
