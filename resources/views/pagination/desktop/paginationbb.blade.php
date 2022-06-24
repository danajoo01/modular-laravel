<?php
// config
$link_limit = 7; // maximum number of links (a little bit inaccurate, but will be ok for now)
?>
<style type="text/css">
    .deals-paging li a.disabled{
        cursor: not-allowed;
    }
    .deals-paging li a.disabled:hover{
        background-color:inherit;
    }
</style>
@if ($paginator->lastPage() > 1)
    <ul>
        <li>
            <a href="{{ ($paginator->currentPage() != 1) ? $paginator->fragment($anchor)->url(1) : '#' }}" class="{{ ($paginator->currentPage() == 1) ? ' disabled' : '' }}"><i class="fa fa-angle-up" aria-hidden="true"></i></a>
         </li>
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            <?php
            $half_total_links = floor($link_limit / 2);
            $from = $paginator->currentPage() - $half_total_links;
            $to = $paginator->currentPage() + $half_total_links;
            if ($paginator->currentPage() < $half_total_links) {
               $to += $half_total_links - $paginator->currentPage();
            }
            if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
                $from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
            }
            ?>
            @if ($from < $i && $i < $to)
                <li>
                    <a href="{{ $paginator->fragment($anchor)->url($i) }}" class="{{ ($paginator->currentPage() == $i) ? ' active' : '' }}">{{ $i }}</a>
                </li>
            @endif
        @endfor
        <li>
            <a href="{{ ($paginator->currentPage()!= $paginator->lastPage()) ? $paginator->fragment($anchor)->url($paginator->lastPage()) : '#' }}" class="{{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}"><i class="fa fa-angle-down" aria-hidden="true"></i></a>
        </li>
    </ul>
@endif