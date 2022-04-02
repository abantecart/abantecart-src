<div class="container latest-column">
    <h2><?php echo $heading_title; ?></h2>
        <div class="d-flex flex-column">
<?php
if ($products) {
    $tax_exempt = $this->customer->isTaxExempt();
    $config_tax = $this->config->get('config_tax');
    foreach ($products as $product) {
        $tax_message = '';
        if ($config_tax && !$tax_exempt && $product['tax_class_id']){
            $tax_message = '&nbsp;&nbsp;'.$price_with_tax;
        }
        $item = [];
        $item['image'] = $product['thumb']['thumb_url'];
        $item['title'] = $product['name'];
        $item['description'] = $product['model'];

        $item['info_url'] = $product['href'];
        $item['buy_url'] = $product['add'];
        $item['rating'] = renderRatingStars($product['rating'], $product['stars']);
        if(!$display_price){
            $item['price'] = '';
        }

        $review = $button_write;
?>
        <div class="col d-flex align-items-start mt-5">
            <a class="flex-shrink-0" href="<?php echo $item['info_url']?>">
                <img alt="<?php echo_html2view($item['title']); ?>" class="thumbnail_small" src="<?php echo $item['image']?>"/>
            </a>
            <a href="<?php echo $item['info_url']?>" class="ms-2 text-decoration-none text-secondary">
                <h5 class="text-decoration-none text-wrap"><?php echo $item['title']?></h5>
                <?php if ($review_status) { ?>
                    <?php echo $item['rating']?>
                <?php }
                if ($display_price) { ?>
                    <div class="price text-muted d-flex align-items-center">
                    <?php  if ($product['special']) { ?>
                        <div class="fs-5 text-black me-2"><?php echo $product['special'] . $tax_message; ?></div>
                        <div class="fs-6 text-decoration-line-through me-2"><?php echo $product['price']; ?></div>
                    <?php } else { ?>
                        <span class="oneprice"><?php echo $product['price'] . $tax_message?></span>
                    <?php } ?>
                    </div>
            <?php } ?>
            </a>
        </div>
<?php
	}
}
?>
		</div>
</div>