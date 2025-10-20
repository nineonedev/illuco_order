<div class="no-side-drawer">
    <div class="no-drawer <?= cookie()->get('drawerShrink') === '1' ? '--shrink' : '' ?>" id="drawer">
        <div class="no-drawer-scroll">
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
                        <li class="no-drawer-gnb-item <?= route_is('admin.dashboard') ? '--active' : '' ?>">
                            <a href="<?= route('admin.dashboard') ?>" class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
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
                    </ul>

                    <ul class="no-drawer-gnb">
                        <!-- 주문 목록 메뉴 -->
                        <?php if (can('order.read')): ?>
                            <li class="no-drawer-gnb-item <?= 
                                    route_is_exact('admin.orders.index') || 
                                    route_is_exact('admin.orders.edit') ||  
                                    route_is_exact('admin.order_documents.print') ||
                                    route_is_exact('admin.order_documents.edit') ||
                                    route_is_exact('admin.order_documents.download') || 
                                    route_is_exact('admin.order_documents.show') 
                                    ? '--active' : '' ?>">
                                <a href="<?= route('admin.orders.index') ?>" class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                                    <div class="no-drawer-gnb-link__icon">
                                        <i class="fa-light fa-receipt"></i>
                                    </div>
                                    <div class="no-drawer-gnb-link__text">
                                        <span>주문 목록</span>
                                    </div>
                                    <div data-tooltip-text>
                                    <span>주문 목록</span>
                                        <span data-tooltip-arrow></span>
                                    </div>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- 주문 문서 관리 메뉴 -->
                        <?php if (can('producttemplate.read')): ?>
                            <!-- <li class="no-drawer-gnb-item <?= ''//route_is('admin.orders.docs') ? '--active' : '' ?>">
                                <a href="<?= '#' //route('admin.orders.docs') ?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
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
                                </a>
                            </li> -->
                        <?php endif; ?>

                        <!-- 주문 메뉴 -->
                        <?php if (can('cart.read')): ?>
                            <li class="no-drawer-gnb-item <?= route_is('admin.cart') ? '--active' : '' ?>">
                                <a href="<?= route('admin.cart.index') ?>" class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
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
                        <?php endif; ?>

                         <?php if (can('cart.read')): ?>
                            <li class="no-drawer-gnb-item <?= route_is_exact('admin.orders.serial') ? '--active' : '' ?>">
                                <a href="<?= route('admin.orders.serial') ?>" class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                                    <div class="no-drawer-gnb-link__icon">
                                        <i class="fa-brands fa-product-hunt"></i>
                                    </div>
                                    <div class="no-drawer-gnb-link__text">
                                        <span>시리얼번호 조회</span>
                                    </div>
                                    <div data-tooltip-text>
                                        <span>시리얼번호 조회</span>
                                        <span data-tooltip-arrow></span>
                                    </div>
                                </a>
                            </li>

                            <li class="no-drawer-gnb-item <?= route_is_exact('admin.loupe_settings.index') ||  route_is_exact('admin.loupe_frame_colors.index')   ? '--active' : '' ?>">
                                <a href="<?= route('admin.loupe_settings.index') ?>" class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                                    <div class="no-drawer-gnb-link__icon">
                                        <i class="fa-solid fa-microscope"></i>
                                    </div>
                                    <div class="no-drawer-gnb-link__text">
                                        <span>루페 세팅창</span>
                                    </div>
                                    <div data-tooltip-text>
                                        <span>루페 세팅창</span>
                                        <span data-tooltip-arrow></span>
                                    </div>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <?php if (can('product.read') || can('category.read')): ?>
                    <ul class="no-drawer-gnb">
                        <!-- 제품 메뉴 -->
                        <?php if (can('product.read')): ?>
                            <li class="no-drawer-gnb-item <?= route_is('admin.product_templates') ? '--active' : '' ?>">
                                <a href="<?= route('admin.product_templates.index') ?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
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
                        <?php endif; ?>

                        <!-- 카테고리 메뉴 -->
                        <?php if (can('category.read')): ?>
                            <li class="no-drawer-gnb-item <?= route_is('admin.product_categories.index') ? '--active' : '' ?>">
                                <a href="<?= route('admin.product_categories.index') ?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
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
                        <?php endif; ?>
                    </ul>
                    <?php endif; ?>


                    <ul class="no-drawer-gnb">
                        <!-- 직원 메뉴 -->
                        <?php if (can('employee.read')): ?>
                            <li class="no-drawer-gnb-item <?= route_is('admin.employees') ? '--active' : '' ?>">
                                <a href="<?= route('admin.employees.index') ?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
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
                        <?php endif; ?>
                        

                        <!-- 대리점 메뉴 -->
                        <?php if (can('dealer.read')): ?>
                            <li class="no-drawer-gnb-item <?= route_is('admin.dealers') ? '--active' : '' ?>">
                                <a href="<?= route('admin.dealers.index') ?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
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
                        <?php endif; ?>
                        
                        <!-- 고객 메뉴 -->
                        <?php if (can('customer.read')): ?>
                            <li class="no-drawer-gnb-item <?= route_is('admin.customers') ? '--active' : '' ?>">
                                <a href="<?= route('admin.customers.index') ?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                                    <div class="no-drawer-gnb-link__icon">
                                        <i class="fa-light fa-user"></i>
                                    </div>
                                    <div class="no-drawer-gnb-link__text">
                                        <span>고객</span>
                                    </div>
                                    <div data-tooltip-text>
                                        <span>고객</span>
                                        <span data-tooltip-arrow></span>
                                    </div>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>


                    <ul class="no-drawer-gnb">
                        <!-- 공지사항 메뉴 -->
                        <?php if (can('notice.read')): ?>
                            <li class="no-drawer-gnb-item <?= route_is('admin.notices') ? '--active' : '' ?>">
                                <a href="<?= route('admin.notices.index') ?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
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
                        <?php endif; ?>

                        <!-- 클레임 메뉴 -->
                        <?php if (can('claim.read')): ?>
                            <li class="no-drawer-gnb-item <?= route_is('admin.claims') ? '--active' : '' ?>">
                                <a href="<?= route('admin.claims.index') ?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
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
                        <?php endif; ?>
                        <!-- 클레임 메뉴 -->
                        <?php if (can('salesinfo.read')): ?>
                            <li class="no-drawer-gnb-item <?= route_is('admin.salesinfo') ? '--active' : '' ?>">
                                <a href="<?= route('admin.salesinfo.index') ?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                                    <div class="no-drawer-gnb-link__icon">
                                        <i class="fa-light fa-sitemap"></i>
                                    </div>
                                    <div class="no-drawer-gnb-link__text">
                                        <span>사이트 정보</span>
                                    </div>
                                    <div data-tooltip-text>
                                        <span>사이트 정보</span>
                                        <span data-tooltip-arrow></span>
                                    </div>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    
                    <?php if (can('role.read')) : ?>
                    <ul class="no-drawer-gnb">
                        <!-- 권한 생성 메뉴 -->
                        <li class="no-drawer-gnb-item <?= route_is('admin.roles') ? '--active' : '' ?>">
                            <a href="<?= route('admin.roles.index') ?>" data-drawer-menu class="no-drawer-gnb-link no-drawer-link" data-menu-tooltip="right">
                                <div class="no-drawer-gnb-link__icon">
                                    <i class="fa-light fa-shield-check"></i>
                                </div>
                                <div class="no-drawer-gnb-link__text">
                                    <span>권한 관리</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <?php endif; ?>

                </nav>
            </div>
        </div>
    </div>
    <div class="no-drawer-backdrop" id="drawer-backdrop"></div>
</div>
