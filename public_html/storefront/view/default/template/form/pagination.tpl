<?php if ($total > 0 ) { ?>
    <nav class="w-100">
        <div class="pagination d-flex flex-wrap justify-content-center justify-content-md-start bg-secondary bg-opacity-10 text-secondary align-items-center pb-3 pb-md-0 ps-1 pe-3 border ">
            <div class="page-item disabled d-flex flex-wrap flex-sm-nowrap text-nowrap align-items-center p-3 me-auto">
                <?php echo $text_limit ?>&nbsp;&nbsp;
                <?php echo $limit_select ?>&nbsp;&nbsp;
                <?php echo $text ?>
            </div>
            <div class="d-flex flex-nowrap">
            <?php if ($page > 1) { ?>
                <div class="page-item">
                    <a class="page-link" href="<?php echo $first_url; ?>"
                       title="<?php echo_html2view($text_first); ?>" aria-label="<?php echo_html2view($text_first); ?>">
                        <i class="fa-solid fa-backward-step"></i>
                    </a>
                </div>
                <div class="page-item">
                    <a class="page-link" href="<?php echo $prev_url; ?>"
                       title="<?php echo_html2view($text_prev); ?>" aria-label="<?php echo_html2view($text_prev); ?>">
                        <i class="fa-solid fa-angle-left"></i>
                    </a>
                </div>
            <?php
            }
            for ($i = $start; $i <= $end; $i++) { ?>
                <div class="page-item <?php echo $page == $i ? 'disabled' : ''; ?>">
                    <a class="page-link"
                       href="<?php echo str_replace('{page}', $i, $url) ?>"
                       title="<?php echo $i; ?>" ><?php echo $i; ?></a>
                </div>
            <?php }
            if ($page < $total_pages) { ?>
                <div class="page-item">
                    <a class="page-link" href="<?php echo $next_url; ?>" title="<?php echo_html2view($text_next); ?>" aria-label="<?php echo_html2view($text_next); ?>">
                        <i class="fa-solid fa-angle-right"></i>
                    </a>
                </div>
                <div class="page-item">
                    <a class="page-link" href="<?php echo $last_url; ?>" title="<?php echo_html2view($text_last); ?>">
                        <i class="fa-solid fa-forward-step"></i>
                    </a>
                </div>
            <?php } ?>
            </div>
        </div>
    </nav>
<?php } ?>