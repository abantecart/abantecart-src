<?php if ($reviews) {
    foreach ($reviews as $review) { ?>
            <div class="d-flex">
                <div class="flex-grow-1 ms-3"><h5 class="mb-1"><?php echo $review['author']; ?> <small
                                class="text-muted fw-normal"><?php echo $review['date_added'];?></small>
                        <?php if ($review['verified_purchase']) {?>
                            <small class="text-muted fw-normal"><?php echo $this->language->get('text_verified_review'); ?></small>
                        <?php } ?>
                    </h5>
                    <div class="d-flex align-items-center gap-1 text-warning"><?php echo renderRatingStarsNv($review['stars'],'')?></div>
                    <p class="mb-2 text-muted mt-1"><?php echo $review['text']; ?></p></div>
            </div>
    <?php } ?>
<div class="pagination"><?php echo $pagination_bootstrap; ?></div>
<?php } else { ?>
<div class="content"><?php echo $text_no_reviews; ?></div>
<?php } ?>
