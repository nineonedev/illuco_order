<header class="no-header">
    <div class="no-header-inner no-container-3xl">
        <button type="button" class="no-header-toggle" id="drawer-open-btn">
            <i class="fa-light fa-bars"></i>
        </button>
        <a href="<?=  '#' //route('admin.dashboard') ?>" class="no-header-logo">
            <img src="<?= asset_path('img/meta/logo-white.svg')?>" alt="">
        </a>
        <div class="no-header-option">
            <!-- <div class="no-header-option-item">    
                <button type="button" data-tooltip="bottom" data-theme="auto" class="no-btn-icon">
                    <i class="fa-light fa-eclipse"></i>
                    <span data-tooltip-text>
                        <span>System theme</span>
                        <div data-tooltip-arrow></div>
                    </span>
                </button>
            </div> -->
            <div class="no-header-option-item">    
                <button type="button" data-tooltip="bottom" data-theme="light" class="no-btn-icon">
                    <i class="fa-light fa-sun-bright"></i>
                    <span data-tooltip-text>
                        <span>Light theme</span>
                        <div data-tooltip-arrow></div>
                    </span>
                </button>
            </div>
            <div class="no-header-option-item">
                <button type="button" data-tooltip="bottom" data-theme="dark" class="no-btn-icon">
                    <i class="fa-light fa-moon"></i>
                    <span data-tooltip-text>
                        <span>Dark theme</span>
                        <div data-tooltip-arrow></div>
                    </span>
                </button>
            </div>
            <div class="no-header-option-item">
                <a href="<?= '#' //route('admin.setting') ?>" data-tooltip="bottom" class="no-btn-icon">
                    <i class="fa-light fa-gear"></i>
                    <span data-tooltip-text>
                        <span>Setting</span>
                        <div data-tooltip-arrow></div>
                    </span>
                </a>
            </div>
            <div class="no-header-option-item">
                <a href="<?= '#' //url('employees.edit', ['id' => 3]) ?>" type="button" class="no-btn-icon --2xl">
                    <div class="no-header-option-profile">나</div>
                </a>
                <!-- <button type="button" class="no-btn-icon --2xl">
                    <div class="no-header-option-profile">나</div>
                </button> -->
                <div class="no-header-option-profile-dropdown">
                    <ul>
                        <li><a href="#">내정보</a></li>
                    </ul>
                    <hr>
                    <button type="button">로그아웃</button>
                </div>
            </div>
        </div>
    </div>
</header>