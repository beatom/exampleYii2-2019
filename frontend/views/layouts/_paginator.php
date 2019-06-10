<?php
use yii\helpers\Url;
// отображаем постраничную разбивку
$count_page = (int)($pages->totalCount / $pageSize);
$count_page = ($pages->totalCount % $pageSize) ? $count_page + 1 : $count_page;
if ($count_page > 1) {
    $next_page = $now_page == $count_page ? $now_page : $now_page + 1;
    echo '<nav aria-label="Page navigation example"><ul class="pagination">';
    for ($i = 1; $i <= $count_page; $i++) {
        $class = '';
        if ($now_page == $i) {
            $class = ' active';
        }
        echo '<li class="page-item' . $class . '"><a class="page-link" href="' . Url::to([$link . '?page=' . $i]) . '">' . $i . '</a></li>';
    }
    echo '<li class="page-item"><a class="page-link" href="' . Url::to([$link . '?page=' . $next_page]) . '" aria-label="Next">
                        <svg class="ico">
                            <use xlink:href="/img/sprites/sprite.svg#arrow"></use>
                        </svg></a></li>';
    echo '</ul></nav>';
}