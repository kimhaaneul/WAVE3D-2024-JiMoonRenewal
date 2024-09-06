<?php
// 페이지 네비게이션 코드
function renderPagination($current_page, $total_pages) {
    echo '<nav aria-label="Page navigation">';
    echo '<ul class="pagination justify-content-center">';

    if ($current_page > 1) {
        echo '<li class="page-item">';
        echo '<a class="page-link" href="?page=' . ($current_page - 1) . '" aria-label="Previous">';
        echo '<span aria-hidden="true">&laquo;</span>';
        echo '</a>';
        echo '</li>';
    }

    for ($i = 1; $i <= $total_pages; $i++) {
        $activeClass = $i == $current_page ? 'active' : '';
        echo '<li class="page-item ' . $activeClass . '">';
        echo '<a class="page-link" href="?page=' . $i . '">' . $i . '</a>';
        echo '</li>';
    }

    if ($current_page < $total_pages) {
        echo '<li class="page-item">';
        echo '<a class="page-link" href="?page=' . ($current_page + 1) . '" aria-label="Next">';
        echo '<span aria-hidden="true">&raquo;</span>';
        echo '</a>';
        echo '</li>';
    }

    echo '</ul>';
    echo '</nav>';
}
