<?php extend('layouts.admin'); ?>

<?php section('title') ?>
직원관리
<?php endSection() ?>

<?php section('content') ?>
<div class="no-page-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">직원관리</h1>
            <p class="no-text-secondary">등록된 직원 정보를 확인하실 수 있습니다.</p> 
        </div>
        <!-- Head -->

        <div class="no-page-index-filter">
            <form action="" class="no-page-index-filter__form">
                <div class="no-form-search --sm">
                    <label for="title" class="no-form-search-inner">
                        <fieldset class="no-form-search-label --blind">
                            <legend class="no-form-search-text">검색</legend>
                        </fieldset>
                        <div class="no-form-search__icon">
                            <i class="fa-light fa-magnifying-glass"></i>
                        </div>
                        <input type="search" name="title" id="title" class="no-form-search-input" placeholder="Search">
                    </label>
                </div>
            </form>

            <div class="no-page-index-link">
                <a href="<?=route('employees.create')?>" class="no-btn-primary --sm">
                    <span>Create</span>
                </a>
            </div>
        </div>
        <!-- Filter -->

        <form method="get" class="no-page-index-table-outer">
            <table class="no-page-index-table">
                <thead>
                    <tr>
                        <th class="no-table-check">
                            <div class="no-form-checkbox --xs">
                                <label for="all" class="no-form-checkbox-pointer">
                                    <input type="checkbox" id="all" class="no-form-checkbox-input">
                                    <div class="no-form-checkbox-ripple">
                                        <span class="no-form-checkbox-box">
                                            <div class="no-form-checkbox-icon">
                                                <i class="fa-solid fa-check"></i>
                                            </div>
                                        </span>
                                    </div>
                                </label>
                            </div>
                        </th>
                        <th>국가</th>
                        <th>대리점</th>
                        <th>이름</th>
                        <th>연락처</th>
                        <th>아이디</th>
                        <th>이메일</th>
                        <th>작업</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < 10; $i++): ?>
                    <tr class="no-table-hover">
                        <td class="no-table-check">
                            <div class="no-form-checkbox --xs">
                                <label for="<?=$i?>" class="no-form-checkbox-pointer">
                                    <input type="checkbox" id="<?=$i?>" class="no-form-checkbox-input">
                                    <div class="no-form-checkbox-ripple">
                                        <span class="no-form-checkbox-box">
                                            <div class="no-form-checkbox-icon">
                                                <i class="fa-solid fa-check"></i>
                                            </div>
                                        </span>
                                    </div>
                                </label>
                            </div>
                        </td>
                        <td>프랑스</td>
                        <td>Arkan</td>
                        <td>Jean Dupont</td>
                        <td>010-1234-5678</td>
                        <td>jeandp</td>
                        <td>jean@example.com</td>
                        <td class="no-table-action">
                            <div class="no-page-index-table__action">
                                <a href="#" class="no-btn-action" data-tooltip>
                                    <div class="no-btn-action-ripple">
                                        <i class="fa-light fa-eye"></i>
                                        <span data-tooltip-text><span>보기</span><span data-tooltip-arrow></span></span>
                                    </div>
                                </a>
                                <a href="#" class="no-btn-action" data-tooltip>
                                    <div class="no-btn-action-ripple">
                                        <i class="fa-light fa-copy"></i>
                                        <span data-tooltip-text><span>복사</span><span data-tooltip-arrow></span></span>
                                    </div>
                                </a>
                                <a href="<?= url('employees.edit', ['id' => $i]) ?>" class="no-btn-action" data-tooltip>
                                    <div class="no-btn-action-ripple">
                                        <i class="fa-light fa-pen-to-square"></i>
                                        <span data-tooltip-text><span>수정</span><span data-tooltip-arrow></span></span>
                                    </div>
                                </a>
                                <a href="#" class="no-btn-action" data-tooltip>
                                    <div class="no-btn-action-ripple">
                                        <i class="fa-light fa-trash-can"></i>
                                        <span data-tooltip-text><span>삭제</span><span data-tooltip-arrow></span></span>
                                    </div>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </form>
        <!-- Table -->

        <div class="no-pagination">
            <p class="no-pagination__text">Rows per page:</p>
            <div class="no-pagination__input">
                <select>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="no-pagination__text">1-10 of 10</div>
            <div class="no-pagination__btn">
                <a href="#" class="no-btn-move --disabled">
                    <i class="fa-light fa-chevron-left"></i>
                </a>
                <a href="#" class="no-btn-move">
                    <i class="fa-light fa-chevron-right"></i>
                </a>
            </div>
        </div>
        <!-- Pagination -->

    </div>
    <!-- Row -->
</div>
<?php endSection() ?>
