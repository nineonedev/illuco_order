import View from "../../core/View";
import Helper from "../../supports/Helper";
import SelectInput from "../Inputs/SelectInput";

export default class TemplateSelection extends View {
    _defineProps(){
        return {
            paginator: {},
            categories: [],
            query: {
                category_id: '',
                search: '',
                sort: 'latest'
            },
        };
    }

    _defineState(){
        return {
            ...this._props
        };
    }

    _template() {
        const { paginator, query } = this._state;
        const { data } = paginator;

        return `
            <div>
                <form id="search-form" class="no-page-search-form">
                    <div class="no-page-search-form__actions">
                        <button type="button" class="no-btn-success --xs" id="btn-reset">
                            초기화
                        </button>
                    </div>

                    <div class="no-page-search-form-row">
                        <div data-ref="category"></div>

                        <div class="no-form-search">
                            <label for="query" class="no-form-label">검색</label>
                            <div class="no-form-search-inner">
                                <div class="no-form-search__icon">
                                    <i class="fa-light fa-magnifying-glass"></i>
                                </div>
                                <input 
                                    type="search" 
                                    name="search" 
                                    id="query" 
                                    class="no-form-search-input" 
                                    placeholder="제품명, 모델명, 코드, 설명" 
                                    value="${query.search || ''}"
                                />
                            </div>
                        </div>
                        
                        <div data-ref="sort"></div>
                    </div>
                </form>
            
                <div class="no-page-index-table-outer">
                    <table class="no-page-index-table">
                        <thead>
                            <tr>
                                <th>이미지</th>
                                <th>제품명</th>
                                <th>모델명</th>
                                <th>코드</th>
                                <th>단가(USD)</th>
                                <th>관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.map(this._renderItem).join('')}
                        </tbody>
                    </table>
                </div>

                ${this._renderPagination(paginator)}
            </div>
        `;
    }

    _render(){
        super._render();

        this.form = this.qs('#search-form');

        SelectInput.make(this.refs.sort, {
            label: "정렬",
            name: 'sort',
            value: this._state.query.sort || '',
            options: [
                { label: "최신순", value: "latest" },
                { label: "오래된순", value: "oldest" }
            ],
            onChange: this._handleSearch.bind(this),
        }).render();

        SelectInput.make(this.refs.category, {
            label: '제품군 선택',
            name: 'category_id',
            value: this._state.query.category_id || '',
            options: [
                { label: '전체', value: '' },
                ...this._state.categories.map(c => ({
                    label: c.label,
                    value: c.id
                }))
            ],
            onChange: this._handleSearch.bind(this),
        }).render();
    }

    _renderPagination(paginator) {
        const {
            from = 0,
            to = 0,
            total = 0,
            has_previous_page = false,
            has_next_page = false,
            current_page = 1,
            per_page = 15
        } = paginator;

        return `
            <div class="no-pagination">
                <p class="no-pagination__text">Rows per page:</p>
                <div class="no-pagination__input">
                    <select name="perpage" class="no-pagination__select">
                        ${[15, 25, 50, 75, 100].map(v => `
                            <option value="${v}" ${v == per_page ? 'selected' : ''}>${v}</option>
                        `).join('')}
                    </select>
                </div>
                <div class="no-pagination__text">
                    ${from}-${to} of ${total}
                </div>
                <div class="no-pagination__btn">
                    <a href="#" class="no-btn-move ${!has_previous_page ? '--disabled' : ''}" data-move="prev">
                        <i class="fa-duotone fa-light fa-chevron-left"></i>
                    </a>
                    <a href="#" class="no-btn-move ${!has_next_page ? '--disabled' : ''}" data-move="next">
                        <i class="fa-duotone fa-light fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        `;
    }

    _renderItem({ id, fileattachment, name, model, code, price } = {}) {
        const mainImage = fileattachment.find(file => file.file_key === 'main_image');
        
        return `
            <tr>
                <td>
                    ${mainImage ? `
                        <div class="no-page-index-table__thumb">
                            <img width="80" src="${mainImage.upload_path}" alt="${model || name}"/>
                        </div>
                    ` : `-` }
                </td>
                <td><span>${name}</span></td>
                <td><span>${model || '-'}</span></td>
                <td><span>${code || '-'}</span></td>
                <td><span>${price || '-'}</span></td>
                <td>
                    <div class="no-prod-attr-list__action">
                        <button 
                            type="button" 
                            class="no-btn-primary-outline" 
                            data-row-id="${id}">
                            <span>선택</span>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    _bindEvents() {
        const buttons = this.qsAll('button[data-row-id]');
        buttons.forEach(btn => {
            this.on(btn, 'click', this._handleClick.bind(this));
        });

        const searchInput = this.qs('#query');
        if (searchInput) {
            this.on(searchInput, 'input', Helper.debounce(this._handleSearch.bind(this), 500));
        }

        const moveButtons = this.qsAll('.no-btn-move');
        moveButtons.forEach(btn => {
            this.on(btn, 'click', this._handleMove.bind(this));
        });

        const perPageSelect = this.qs('.no-pagination__select');
        if (perPageSelect) {
            this.on(perPageSelect, 'change', this._handlePerPage.bind(this));
        }

        const resetButton = this.qs('#btn-reset');
        if (resetButton) {
            this.on(resetButton, 'click', this._handleReset.bind(this));
        }
    }

    _handleReset(){
        const params = new URLSearchParams({
            perpage: this._state.paginator.per_page || 15,
            page: 1, 
            category_id: '', 
            search: '',
            sort: 'latest',
        });

        this._dispatch('fetch.templates', {
            query: params.toString()
        });
    }

    _handleSearch(form, evt) {
        if (evt) evt.preventDefault();

        const formData = new FormData(this.form);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }

        this._dispatch('fetch.templates', {
            query: params.toString()
        });
    }

    _handleMove(button, evt) {
        evt.preventDefault();

        if (button.classList.contains('--disabled')) return;

        const move = button.getAttribute('data-move');
        let page = parseInt(this._state.paginator.current_page || 1);

        if (move === 'prev') page -= 1;
        if (move === 'next') page += 1;

        const params = new URLSearchParams();

        if (this._state.search) params.append('search', this._state.search);
        if (this._state.category_id) params.append('category_id', this._state.category_id);
        if (this._state.sort) params.append('sort', this._state.sort);
        if (this._state.perpage) params.append('perpage', this._state.perpage);
        params.append('page', page);

        this._dispatch('fetch.templates', {
            query: params.toString()
        });
    }

    _handlePerPage(select, evt) {
        const perpage = select.value;

        const params = new URLSearchParams();

        if (this._state.search) params.append('search', this._state.search);
        if (this._state.category_id) params.append('category_id', this._state.category_id);
        if (this._state.sort) params.append('sort', this._state.sort);
        params.append('perpage', perpage);
        params.append('page', 1);

        this._dispatch('fetch.templates', {
            query: params.toString()
        });
    }

    _handleClick(form, evt) {
        const id = evt.currentTarget.getAttribute('data-row-id');
        const template = this._state.paginator.data.find(item => item.id === +id);
        this._dispatch('pick.template', { template });
    }
}
