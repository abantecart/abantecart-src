<?php if ($reviews) {?>
    <ul class="list-group list-group-flush">
<?php    foreach ($reviews as $review) { ?>
        <li class="list-group-item px-0" id="review-set">
            <div class="d-flex">
                <div class="flex-shrink-0"><div class="user-avtar bg-primary text-white"><span><?php
                            echo $review['initials']; ?></span></div></div>
                <div class="flex-grow-1 ms-3"><h5 class="mb-1"><?php echo $review['author']; ?> <small
                                class="text-muted fw-normal"><?php echo $review['date_added'];?></small>
                        <?php if ($review['verified_purchase']) {?>
                            <small class="text-muted fw-normal"><?php echo $this->language->get('text_verified_review'); ?></small>
                        <?php } ?>
                    </h5>
                    <div class="d-flex align-items-center gap-1 text-warning"><?php echo renderRatingStarsNv($review['stars'],'')?></div>
                    <p class="mb-2 text-muted mt-1"><?php echo $review['text']; ?></p></div><hr class="review-separator">
            </div>
        </li>
    <?php } ?>
    </ul>
<div class="pagination"><?php echo $pagination_bootstrap; ?></div>
<?php } else { ?>
<div class="content"><?php echo $text_no_reviews; ?></div>
<?php } ?>
