<?php if ($paginator) : ?>

<div class="no-pagination">
    <p class="no-pagination__text">Rows per page:</p>
    <div class="no-pagination__input">
        <select name="perpage">
            <option value="15" <?= request()->query('perpage') == 15 ? 'selected' : '' ?>>15</option>
            <option value="25" <?= request()->query('perpage') == 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= request()->query('perpage') == 50 ? 'selected' : '' ?>>50</option>
            <option value="75" <?= request()->query('perpage') == 75 ? 'selected' : '' ?>>75</option>
            <option value="100" <?= request()->query('perpage') == 100 ? 'selected' : '' ?>>100</option>
        </select>
    </div>
    <div class="no-pagination__text">
        <?= $paginator->from() ?>-<?= $paginator->to() ?> of <?= $paginator->total() ?>
    </div>
    <div class="no-pagination__btn">
        <a href="<?= $paginator->previousPageUrl() ?>" class="no-btn-move <?= $paginator->hasPreviousPage() ? '' : '--disabled' ?>">
            <i class="fa-duotone fa-light fa-chevron-left"></i>
        </a>
        <a href="<?= $paginator->nextPageUrl() ?>" class="no-btn-move <?= $paginator->hasNextPage() ? '' : '--disabled' ?>">
            <i class="fa-duotone fa-light fa-chevron-right"></i>
        </a>
    </div>
</div>

<?php endif; ?>