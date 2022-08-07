<?php if ($reviews) {
    foreach ($reviews as $review) { ?>
        <div class="card w-100 mb-2 ">
          <div class="card-body">
            <h5 class="card-subtitle d-flex flex-nowrap align-items-center mb-3">
                <p class="fw-bold me-3 mb-0 pe-3 border-end" ><?php echo $review['author']; ?></p>
                <div class="fs-6 text-warning"><?php echo renderRatingStars($review['stars'],'')?></div>
            </h5>
            <h6 class="card-subtitle d-flex flex-nowrap">
            <?php echo $review['date_added'];
                if ($review['verified_purchase']) {?>
                    <span class="ms-4 verified_review"><?php echo $this->language->get('text_verified_review'); ?></span>
                <?php } ?>
            </h6>
            <p class="mt-4 card-text"><?php echo $review['text']; ?></p>
          </div>
        </div>
<?php } ?>
<div class="pagination"><?php echo $pagination_bootstrap; ?></div>
<?php } else { ?>
<div class="content"><?php echo $text_no_reviews; ?></div>
<?php } ?>
