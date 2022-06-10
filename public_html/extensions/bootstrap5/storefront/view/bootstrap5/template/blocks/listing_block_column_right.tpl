<div class="listing-block-right mt-3">
<?php if ($block_framed) { ?>
	<h2><?php echo $heading_title; ?></h2>
<?php }?>
    <div class="d-flex flex-column">
<?php
    if ($content) {
        $tax_exempt = $this->customer->isTaxExempt();
        $config_tax = $this->config->get('config_tax');
        foreach ($content as $item) {
            $item['title'] = $item['name'] ? : $item['thumb']['title'];

            $item['image'] = $item['thumb']['origin'] == 'internal'
                            ? '<img alt="'.htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8').'" class="d-block" src="'. $item['thumb']['thumb_url'].'"/>'
                            : $item['thumb']['thumb_html'];

            $item['description'] = $item['model'];
            $item['info_url'] = $item['href'] ? : $item['thumb']['main_url'];
            $item['buy_url'] = $item['add'];
            if (!$display_price) {
                $item['price'] = '';
            }

            $review = $button_write;
            if ($item['rating']) {
                $review = $item['rating'];
            }
?>

            <div class="ms-2 d-flex align-items-start mt-5">
                <a href="<?php echo $item['info_url']?>">
                    <?php echo $item['image']?>
                </a>
                <a href="<?php echo $item['info_url']?>"
                   class="ms-4 text-decoration-none text-secondary d-flex flex-wrap flex-column justify-content-between align-items-start align-self-stretch p-1"
                    >
                    <h6 class="my-auto text-decoration-none text-wrap"><?php echo $item['title']?></h6>
                    <?php if ($review_status) { ?>
                        <?php echo renderRatingStars($item['rating'], $item['stars']); ?>
                    <?php }
                    if ($display_price && $item['price']) { ?>
                        <div class="price text-muted d-flex flex-wrap align-items-center">
                        <?php  if ($item['special']) { ?>
                            <div class="fs-6 text-black me-2"><?php echo $item['special'] . $tax_message; ?></div>
                            <div class="fs-6 text-decoration-line-through me-2"><?php echo $item['price']; ?></div>
                        <?php } else { ?>
                            <div class="text-black"><?php echo $item['price'] . $tax_message?></div>
                        <?php } ?>
                        </div>
                <?php } ?>
                </a>
            </div>
    <?php   }
        } ?>
    </div>
</div>
