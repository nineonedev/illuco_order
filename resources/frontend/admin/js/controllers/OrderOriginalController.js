import Ajax from "../core/Ajax";
import Controller from "../core/Controller";

import TemplateSelection from "../components/Cart/TemplateSelection";
import CustomerSelection from "../components/Cart/CustomerSelection";
import Template from "../components/Cart/Template";

import DealerPicker from '../components/OrderOriginal/DealerPicker'
import CustomerPicker from "../components/OrderOriginal/CustomerPicker";

import Modal from "../shared/Modal";
import Loader from "../shared/Loader";
import DealerSelection from "../components/Cart/DealerSelection";

export default class OrderOriginalController extends Controller {
  modal;
  loader;

  // picker instances
  dealerPicker = null;
  customerPicker = null;

  // template form (제품 옵션 폼)
  tplForm = null;

  // spec caches
  static attributes = {};
  static setGroupItems = {};
  static labels = {};

  countries = {}; // attributes API에서 같이 내려오는 국가 라벨

  /* ===== Template 컴포넌트 헬퍼 ===== */
  static findSubProductByModel(model) {
    for (const category in this.attributes) {
      const specs = this.attributes[category];
      for (const modelName in specs) {
        if (modelName === model) {
          return { model: modelName, category, attributes: specs[modelName] };
        }
      }
    }
    return null;
  }
  static findLabelsByType(type) {
    return this.labels[type] ?? null;
  }
  static findSetGroupItemByModel(model) {
    for (const modelName in this.setGroupItems) {
      if (modelName === model) return this.setGroupItems[modelName];
    }
    return null;
  }

  /* ===== entry ===== */
  async index() {
    this.modal = Modal.make("portal").render();
    this.loader = Loader.make("portal").render();

    // 사전 로드 (TemplateForm, 라벨/세트)
    await this._loadAllData();

    // 이벤트 버스 (컴포넌트 <-> 컨트롤러)
    this._listen("fetch.customers", this._fetchAllCustomers.bind(this));
    // this._listen("fetch.dealers", this._fetchAllDealers.bind(this));
    this._listen("fetch.templates", this._fetchAllTemplates.bind(this));

    this._listen("pick.customer", this._pickCustomer.bind(this));
    // this._listen("pick.dealer", this._pickDealer.bind(this));
    this._listen("pick.template", this._pickTemplate.bind(this));

    // TemplateForm에서 서버로 바로 추가할 때 사용(프로젝트에 맞게 구현됨)
    this._listen("add.original", this._addToOrderItem.bind(this));

    // 화면 훅 렌더
    this._renderHooks();
  }

  /* ===== 훅 렌더 ===== */
  _renderHooks() {
    const customerHook = document.getElementById("customer-hook");
    // const dealerHook = document.getElementById("dealer-hook");
    const templateHook = document.getElementById("template-hook");

    // 대리점 픽커
    // if (dealerHook) {
    //   this.dealerPicker = DealerPicker.make(dealerHook, {
    //     dealer: null,         // 초기표시는 비움 (선택 시 내부에서 정보패널과 hidden 생성)
    //     countries: this.countries,
    //     showReset: true,
    //   }).render();
    // }

    // 고객 픽커
    if (customerHook) {
      this.customerPicker = CustomerPicker.make(customerHook, {
        customer: null,
        countries: this.countries,
        showReset: true,
      }).render();
    }

    // 제품 옵션 폼
    if (templateHook) {
      this.tplForm = Template.make(templateHook, {
        useCart: false, // 오리지널 주문 편집이므로 장바구니 경유 X
        useWrapper: false,
      }).render();
    }
  }

  /* ===== 사전 데이터 ===== */
  async _loadAllData() {
    this.loader.show();
    try {
      const [attrResult, setGroupItemsResult, labelResult] = await Promise.all([
        new Ajax(true).get("/admin/product-templates/attributes"),
        new Ajax(true).get("/admin/product-templates/set-group-items"),
        new Ajax(true).get("/admin/product-templates/labels"),
      ]);

      OrderOriginalController.attributes = attrResult.data?.loupe ? attrResult.data : {};
      OrderOriginalController.setGroupItems = setGroupItemsResult.data || {};
      OrderOriginalController.labels = labelResult.data || {};
      this.countries = attrResult.data?.countries || {};

      this._logger.success("attributes loaded");
    } catch (err) {
      console.error(err);
      alert("필요 데이터 로드 중 문제가 발생했습니다.");
    } finally {
      this.loader.hide();
    }
  }

  /* ===== 고객 ===== */
  async _fetchAllCustomers({ button = null, query = null }) {
    let url = `/admin/customers`;
    if (query) url += `?${query}`;

    try {
      button?.setState?.({ disabled: true });
      this.loader.show();

      const result = await new Ajax(true).get(url);
      if (!result.success) return;

      const { customers, query: q, dealers, countries } = result.data;
      this.modal.setState({
        header: "고객 검색",
        content: CustomerSelection.make(null, { paginator: customers, query: q, dealers, countries }, false),
        open: true,
      });
    } catch (e) {
      console.error(e);
      alert("고객 목록을 불러오지 못했습니다.");
    } finally {
      this.loader.hide();
      button?.setState?.({ disabled: false });
    }
  }

  _pickCustomer({ customer }) {
    if (!customer) return;

    // Picker 컴포넌트에 반영(정보 패널 & hidden 생성)
    this.customerPicker?.setState({ customer });

    // id hidden 동기화(서버 저장용)
    const $id = this._input("#customer_id");
    if ($id) {
      $id.value = customer.id;
      $id.dataset.label = customer.name || "";
    }

    // 버튼 라벨 즉시 반영
    const $btn = document.querySelector("#customer-hook [data-action='open-customer-picker'] span");
    if ($btn) $btn.textContent = customer.name || "고객 선택";

    this.modal.setState({ open: false, content: "", header: "" });
  }

    /* ===== 대리점 ===== */
    async _fetchAllDealers({ button = null, query = null } = {}) {
    let url = `/admin/dealers`;
    if (query) url += `?${query}`;

    try {
        button?.setState?.({ disabled: true });
        this.loader.show();

        const result = await new Ajax(true).get(url);
        if (!result?.success) return;

        const data = result.data || {};

        // paginator 정규화
        const paginator =
        data.dealers ??      // 보통 { data: [...], current_page, ... }
        data.users ??        // 혹시 users로 내려올 경우
        data.paginator ??    // 혹시 paginator라는 키로 올 경우
        data;                // 최후의 수단

        const q = data.query ?? {};
        const countries = data.countries ?? this.countries ?? {};

        // dealers 리스트(선택 필터에 사용) — paginator가 객체/배열 어떤 형식이든 안전하게 추출
        const dealersList = Array.isArray(paginator?.data)
        ? paginator.data
        : (Array.isArray(paginator) ? paginator : []);

        this.modal.setState({
        header: "대리점 검색",
        content: DealerSelection.make(
            null,
            { paginator, query: q, countries, dealers: dealersList },
            false
        ),
        open: true,
        });
    } catch (e) {
        console.error(e);
        alert("대리점 목록을 불러오지 못했습니다.");
    } finally {
        this.loader.hide();
        button?.setState?.({ disabled: false });
    }
    }



  _renderDealerList(list) {
    const rows = list
      .map((u) => {
        const dealer = u.dealer || {};
        const id = dealer.id || u.id || "";
        const name = (u.name || dealer.name || "-").toString();
        const code = (dealer.code || "").toString();
        const country = (dealer.country || "").toString();
        const address = (dealer.address || "").toString();
        const phone = (u.phone || dealer.phone || "").toString();
        const email = (u.email || dealer.email || "").toString();
        const text = `${name} ${code} ${country}`.toLowerCase();

        return `
          <tr class="dealer-row" data-text="${this._esc(text)}">
            <td>${this._esc(name)}</td>
            <td>${this._esc(code)}</td>
            <td>${this._esc(country)}</td>
            <td class="no-text-right">
              <button
                type="button"
                class="no-btn-primary --xs"
                data-pick-dealer
                data-id="${this._esc(id)}"
                data-name="${this._esc(name)}"
                data-code="${this._esc(code)}"
                data-country="${this._esc(country)}"
                data-address="${this._esc(address)}"
                data-phone="${this._esc(phone)}"
                data-email="${this._esc(email)}"
              >선택</button>
            </td>
          </tr>
        `;
      })
      .join("");

    return `
      <div class="no-mb-8">
        <label class="no-form-control-inner">
          <span class="no-form-label">검색</span>
          <input type="text" id="dealer-filter" class="no-form-control-input" placeholder="이름/코드/국가로 필터링">
        </label>
      </div>
      <div class="no-page-index-table-outer">
        <table class="no-page-index-table">
          <thead>
            <tr><th>이름</th><th>코드</th><th>국가</th><th>선택</th></tr>
          </thead>
          <tbody>${rows || ""}</tbody>
        </table>
      </div>
    `;
  }

  _pickDealer({ dealer }) {
    if (!dealer) return;

    // Picker 컴포넌트 업데이트(정보/hidden 생성)
    this.dealerPicker?.setState({ dealer });

    // 서버용 id hidden
    const $id = this._input("#dealer_id");
    if ($id) {
      $id.value = dealer.id || "";
      $id.dataset.label = dealer.name || "";
    }
    const $btn = document.querySelector("#dealer-hook [data-action='open-dealer-picker'] span");
    if ($btn) $btn.textContent = dealer.name || "대리점 선택";

    this.modal.setState({ open: false, content: "", header: "" });
  }

  /* ===== 제품 ===== */
  async _fetchAllTemplates({ button = null, query = null }) {
    let url = `/admin/product-templates`;
    if (query) url += `?${query}`;

    try {
      button?.setState?.({ disabled: true });
      this.loader.show();

      const result = await new Ajax(true).get(url);
      if (!result.success) return;

      const { templates, categories, query: q, prices } = result.data;
      this.modal.setState({
        header: "제품 검색",
        content: TemplateSelection.make(
          null,
          { paginator: templates, categories, query: q, prices },
          false
        ),
        open: true,
      });
    } catch (e) {
      console.error(e);
      alert("제품 목록을 불러오지 못했습니다.");
    } finally {
      this.loader.hide();
      button?.setState?.({ disabled: false });
    }
  }

  _pickTemplate({ template, cartitem = {} }) {
    if (!this.tplForm) return;
    this.tplForm.setState({ template, cartitem });
    this.modal.setState({ open: false, content: "", header: "" });
  }

  /* ===== 주문 아이템 추가 ===== */
  async _addToOrderItem({ data, button }, evt) {
    this._logger.info(Object.fromEntries(data), button);

    try {
      button?.setState?.({ disabled: true });
      this.loader.show();
      const result = await new Ajax(true).post("/admin/orderitems", data);
      this._logger.success("주문아이템 추가", result);
      // TODO: 하단 주문 아이템 테이블 즉시 갱신 로직이 필요하면 여기에 추가
      // location.reload(); 
      alert(result.message);

      if (result.success) {
        location.reload(); 
      }
      

    } finally {
      this.loader.hide();
      button?.setState?.({ disabled: false });
    }
  }

  /* ===== 공통 ===== */
  _input(sel) {
    return /** @type {HTMLInputElement|null} */ (document.querySelector(sel));
  }
  _esc(s) {
    return String(s).replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[m]));
  }
}
