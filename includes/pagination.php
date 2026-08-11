<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Pagination Component
|--------------------------------------------------------------------------
|
| Usage:
|
| renderPagination(
|     current page,
|     total pages,
|     additional query parameters
| );
|
*/

function renderPagination(
    int $currentPage,
    int $totalPages,
    array $query = []
): void {

    if ($totalPages <= 1) {
        return;
    }

    $currentPage = max(
        1,
        $currentPage
    );

    $totalPages = max(
        1,
        $totalPages
    );

    $buildUrl = function (int $page) use ($query): string {

        $query['page'] = $page;

        return '?' . http_build_query($query);

    };

?>
<nav aria-label="Page navigation">

    <ul class="pagination justify-content-center">

        <?php if ($currentPage > 1): ?>

            <li class="page-item">

                <a
                    class="page-link"
                    href="<?= e($buildUrl($currentPage - 1)) ?>">

                    Previous

                </a>

            </li>

        <?php else: ?>

            <li class="page-item disabled">

                <span class="page-link">

                    Previous

                </span>

            </li>

        <?php endif; ?>


<?php

$start = max(
    1,
    $currentPage - 2
);

$end = min(
    $totalPages,
    $currentPage + 2
);

?>

<?php if ($start > 1): ?>

    <li class="page-item">

        <a
            class="page-link"
            href="<?= e($buildUrl(1)) ?>">

            1

        </a>

    </li>

    <?php if ($start > 2): ?>

        <li class="page-item disabled">

            <span class="page-link">

                ...

            </span>

        </li>

    <?php endif; ?>

<?php endif; ?>


<?php for ($i = $start; $i <= $end; $i++): ?>

    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">

        <a
            class="page-link"
            href="<?= e($buildUrl($i)) ?>">

            <?= $i ?>

        </a>

    </li>

<?php endfor; ?>
<?php if ($end < $totalPages): ?>

    <?php if ($end < $totalPages - 1): ?>

        <li class="page-item disabled">

            <span class="page-link">

                ...

            </span>

        </li>

    <?php endif; ?>

    <li class="page-item">

        <a
            class="page-link"
            href="<?= e($buildUrl($totalPages)) ?>">

            <?= $totalPages ?>

        </a>

    </li>

<?php endif; ?>


<?php if ($currentPage < $totalPages): ?>

    <li class="page-item">

        <a
            class="page-link"
            href="<?= e($buildUrl($currentPage + 1)) ?>">

            Next

        </a>

    </li>

<?php else: ?>

    <li class="page-item disabled">

        <span class="page-link">

            Next

        </span>

    </li>

<?php endif; ?>


    </ul>

</nav>

<?php

}
