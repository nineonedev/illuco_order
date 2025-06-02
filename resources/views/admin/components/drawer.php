
<div class="no-side-drawer">
    <div class="no-drawer <?= cookie()->get('drawerShrink') === '1' ? '--shrink' : '' ?>" id="drawer">
        <div class="no-drawer-inner">
            <button type="button" class="no-drawer-trigger" id="drawer-menu-btn" aria-label="메뉴 닫기">
                <i class="fa-light fa-chevron-left"></i>
            </button>

            <div class="no-drawer-head">
                <button type="button" id="drawer-close-btn" class="no-link-slate no-drawer-close-btn">
                    <i class="fa-light fa-arrow-left"></i>
                    <span>Back</span>
                </button>
            </div>

            <nav class="no-drawer-nav">
                <ul class="no-drawer-gnb">
                    <li class="no-drawer-gnb-item <?//= is_route_active('admin.dashboard') ?>">
                        <a href="<?//=route('admin.dashboard')?>" class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                            <div class="no-drawer-gnb-link__icon">
                                <i class="fa-light fa-chart-tree-map"></i>
                            </div>
                            <div class="no-drawer-gnb-link__text">
                                <span>대시보드</span>
                            </div>
                            <div data-tooltip-text>
                                <span>대시보드</span>
                                <span data-tooltip-arrow></span>
                            </div>
                        </a>
                    </li>
                    <!-- Gnb Item -->
                </ul>

                <hr class="no-hr --lg">

                <ul class="no-drawer-gnb">
                    <li class="no-drawer-gnb-item <?//= is_route_active('orders.index') ?>">
                        <a href="<?//=route('orders.index')?>" class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                            <div class="no-drawer-gnb-link__icon">
                                <i class="fa-light fa-receipt"></i>
                            </div>
                            <div class="no-drawer-gnb-link__text">
                                <span>주문내역</span>
                            </div>
                            <div data-tooltip-text>
                                <span>주문내역</span>
                                <span data-tooltip-arrow></span>
                            </div>
                        </a>
                    </li>
                    <!-- Gnb Item -->
                    <li class="no-drawer-gnb-item <?//= is_route_prefix_active('admin.orders.docs') ?>">
                        <a href="<?//=route('orders.docs.index')?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                            <div class="no-drawer-gnb-link__icon">
                                <i class="fa-light fa-file-invoice"></i>
                            </div>
                            <div class="no-drawer-gnb-link__text">
                                <span>주문 문서 관리</span>
                            </div>
                            <div data-tooltip-text>
                                <span>주문 문서 관리</span>
                                <span data-tooltip-arrow></span>
                            </div>
                            <!-- <div class="no-drawer-gnb-link__arrow">
                                <i class="fa-light fa-chevron-down"></i>
                            </div> -->
                        </a>
                        <div class="no-drawer-gnb-children">
                            <!-- <ul class="no-drawer-lnb">
                                <li class="no-drawer-lnb-item --active">
                                    <a href="#" class="no-drawer-lnb-link no-drawer-link"><span>선견적서</span></a>
                                </li>
                                <li class="no-drawer-lnb-item">
                                    <a href="#" class="no-drawer-lnb-link no-drawer-link"><span>생산의뢰서</span></a>
                                </li>
                                <li class="no-drawer-lnb-item">
                                    <a href="#" class="no-drawer-lnb-link no-drawer-link"><span>포장 명세서</span></a>
                                </li>
                                <li class="no-drawer-lnb-item">
                                    <a href="#" class="no-drawer-lnb-link no-drawer-link"><span>상업 송장</span></a>
                                </li>
                            </ul> -->
                        </div>
                    </li>
                    
                    <li class="no-drawer-gnb-item <?//= is_route_active('cartitem.index') ?>">
                        <a href="<?//=route('cartitem.index')?>" class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                            <div class="no-drawer-gnb-link__icon">
                                <i class="fa-light fa-bags-shopping"></i>
                            </div>
                            <div class="no-drawer-gnb-link__text">
                                <span>주문</span>
                            </div>
                            <div data-tooltip-text>
                                <span>주문</span>
                                <span data-tooltip-arrow></span>
                            </div>
                        </a>
                    </li>
                    <!-- Gnb Item -->
                    <li class="no-drawer-gnb-item <?//= is_route_active('guests.index') ?>">
                        <a href="<?//=route('guests.index')?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                            <div class="no-drawer-gnb-link__icon">
                                <i class="fa-light fa-user"></i>
                            </div>
                            <div class="no-drawer-gnb-link__text">
                                <span>주문자</span>
                            </div>
                            <div data-tooltip-text>
                                <span>주문자</span>
                                <span data-tooltip-arrow></span>
                            </div>
                        </a>
                    </li>
                    <!-- Gnb Item -->
                </ul>
                
                <hr class="no-hr --lg">
                
                <ul class="no-drawer-gnb">
                    <li class="no-drawer-gnb-item <? //is_route_active('products.index') ?>">
                        <a href="<?//=route('products.index')?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                            <div class="no-drawer-gnb-link__icon">
                                <i class="fa-light fa-shop"></i>
                            </div>
                            <div class="no-drawer-gnb-link__text">
                                <span>제품</span>
                            </div>
                            <div data-tooltip-text>
                                <span>제품</span>
                                <span data-tooltip-arrow></span>
                            </div>
                        </a>
                    </li>
                    <!-- Gnb Item -->
                    <li class="no-drawer-gnb-item <?//= is_route_active('products.category') ?>">
                        <a href="<?//=route('products.category')?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                            <div class="no-drawer-gnb-link__icon">
                                <i class="fa-light fa-layer-group"></i>
                            </div>
                            <div class="no-drawer-gnb-link__text">
                                <span>카테고리</span>
                            </div>
                            <div data-tooltip-text>
                                <span>카테고리</span>
                                <span data-tooltip-arrow></span>
                            </div>
                        </a>
                    </li>
                    <!-- Gnb Item -->
                </ul>

                <hr class="no-hr --lg">

                <ul class="no-drawer-gnb">
                    <li class="no-drawer-gnb-item <?//= is_route_prefix_active('admin.agents') ?>">
                        <a href="<?//=route('agents.index')?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                            <div class="no-drawer-gnb-link__icon">
                                <i class="fa-light fa-building"></i>
                            </div>
                            <div class="no-drawer-gnb-link__text">
                                <span>대리점</span>
                            </div>
                            <div data-tooltip-text>
                                <span>대리점</span>
                                <span data-tooltip-arrow></span>
                            </div>
                        </a>
                    </li>
                    <!-- Gnb Item -->
                    <li class="no-drawer-gnb-item <?//= is_route_prefix_active('admin.employees') ?>">
                        <a href="<?//=route('employees.index')?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                            <div class="no-drawer-gnb-link__icon">
                                <i class="fa-light fa-user-tie"></i>
                            </div>
                            <div class="no-drawer-gnb-link__text">
                                <span>직원</span>
                            </div>
                            <div data-tooltip-text>
                                <span>직원</span>
                                <span data-tooltip-arrow></span>
                            </div>
                        </a>
                    </li>
                    <!-- Gnb Item -->
                    <li class="no-drawer-gnb-item">
                        <a href="" class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                            <div class="no-drawer-gnb-link__icon">
                                <i class="fa-light fa-bell"></i>
                            </div>
                            <div class="no-drawer-gnb-link__text">
                                <span>알림</span>
                            </div>
                            <div data-tooltip-text>
                                <span>알림</span>
                                <span data-tooltip-arrow></span>
                            </div>
                        </a>
                    </li>
                    <!-- Gnb Item -->
                </ul>

                
                <hr class="no-hr --lg">

                <ul class="no-drawer-gnb">
                    <!-- Gnb Item -->
                    <li class="no-drawer-gnb-item <?//= is_route_prefix_active('admin.notices') ?>">
                        <a href="<?//=route('notices.index')?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                            <div class="no-drawer-gnb-link__icon">
                                <i class="fa-light fa-megaphone"></i>
                            </div>
                            <div class="no-drawer-gnb-link__text">
                                <span>공지사항</span>
                            </div>
                            <div data-tooltip-text>
                                <span>공지사항</span>
                                <span data-tooltip-arrow></span>
                            </div>
                        </a>
                    </li>
                    <!-- Gnb Item -->
                    <li class="no-drawer-gnb-item <?//= is_route_prefix_active('admin.claims') ?>">
                        <a href="<?//=route('claims.index')?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                            <div class="no-drawer-gnb-link__icon">
                                <i class="fa-light fa-comment"></i>
                            </div>
                            <div class="no-drawer-gnb-link__text">
                                <span>클레임</span>
                            </div>
                            <div data-tooltip-text>
                                <span>클레임</span>
                                <span data-tooltip-arrow></span>
                            </div>
                        </a>
                    </li>
                    <!-- Gnb Item -->
                </ul>
                
            </nav>
        </div>
    </div>
    <div class="no-drawer-backdrop" id="drawer-backdrop"></div>
</div>