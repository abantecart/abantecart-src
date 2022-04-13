<?php if ($total > 0 ) { ?>
    <nav class="w-100">
        <ul class="pagination bg-secondary bg-opacity-10 text-secondary align-items-center ps-1 pe-3 border ">
            <li class="page-item disabled d-flex flex-nowrap text-nowrap align-items-center p-3 me-auto">
                <?php echo $text_limit ?>&nbsp;&nbsp;
                <?php echo $limit_select ?>&nbsp;&nbsp;
                <?php echo $text ?>
            </li>
        <?php if ($page > 1) { ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo $first_url; ?>"
                   title="<?php echo_html2view($text_first); ?>" aria-label="<?php echo_html2view($text_first); ?>">
                    <i class="fa-solid fa-backward-step"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?php echo $prev_url; ?>"
                   title="<?php echo_html2view($text_prev); ?>" aria-label="<?php echo_html2view($text_prev); ?>">
                    <i class="fa-solid fa-angle-left"></i>
                </a>
            </li>
        <?php
        }
        for ($i = $start; $i <= $end; $i++) { ?>
            <li class="page-item <?php echo $page == $i ? 'disabled' : ''; ?>">
                <a class="page-link"
                   href="<?php echo str_replace('{page}', $i, $url) ?>"
                   title="<?php echo $i; ?>" ><?php echo $i; ?></a>
            </li>
        <?php }
        if ($page < $total_pages) { ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo $next_url; ?>" title="<?php echo_html2view($text_next); ?>" aria-label="<?php echo_html2view($text_next); ?>">
                    <i class="fa-solid fa-angle-right"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?php echo $last_url; ?>" title="<?php echo_html2view($text_last); ?>">
                    <i class="fa-solid fa-forward-step"></i>
                </a>
            </li>
        <?php } ?>
        </ul>
    </nav>
<?php } ?>