<?php if ($total > 0 ) { ?>

<nav aria-label="Page navigation example">
  <ul class="pagination">
      <li class="page-item disabled">
          <a class=" bg-light text-secondary page-link" href="Javascript:void(0);">
          <?php echo $text_limit ?>&nbsp;&nbsp;
              <?php echo $limit_select ?>&nbsp;&nbsp;
              <?php echo $text ?>
          </a>
      </li>
    <?php if ($page > 1) { ?>
        <li class="page-item">
          <a class="page-link" href="<?php echo $first_url; ?>" aria-label="<?php echo $text_prev; ?>">
            <?php echo $text_first; ?>
          </a>
        </li>
        <li class="page-item">
          <a class="page-link" href="<?php echo $prev_url; ?>" aria-label="<?php echo $text_prev; ?>">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
    <?php
    }
        for ($i = $start; $i <= $end; $i++) { ?>
        <li class="page-item <?php echo $page == $i ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i; ?></a>
        </li>
    <?php } ?>

<?php if ($page < $total_pages) { ?>
    <li class="page-item">
        <a class="page-link" href="<?php echo $next_url; ?>" aria-label="<?php echo $text_next; ?>">
            <span aria-hidden="true">&raquo;</span>
        </a>
    </li>
    <li class="page-item">
        <a class="page-link" href="<?php echo $last_url; ?>"><?php echo $text_last; ?></a>
    </li>
<?php } ?>
  </ul>
</nav>
<?php } ?>