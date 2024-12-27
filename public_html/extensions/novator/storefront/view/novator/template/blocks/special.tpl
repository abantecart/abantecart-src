<div class="special-column mt-3">
    <?php
    	if ( $block_framed ) { ?>
            <h1 class="h2 heading-title"><?php echo $heading_title; ?></h1>
    <?php } ?>
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
        $item['rating'] = renderRatingStarsNv($product['rating'], $product['stars']);
        if(!$display_price){
            $item['price'] = '';
        }

        $review = $button_write;
?>
        <div class="d-flex align-items-start mt-5">
            <a href="<?php echo $item['info_url']?>" class="flex-shrink-0">
                <img alt="<?php echo_html2view($item['title']); ?>" class="d-block product-image-column-list"
                     src="<?php echo $item['image']?>"/>
            </a>
            <a href="<?php echo $item['info_url']?>" class="d-block ms-2 text-decoration-none text-secondary flex-grow-1">
                <h6 class="text-decoration-none text-wrap"><?php echo $item['title']?></h6>
                <?php echo $this->getHookvar('product_listing_details1_'.$product['product_id']);?>
                <?php if ($review_status) { ?>
                    <?php echo $item['rating']?>
                <?php }
                if ($display_price) { ?>
                    <div class="price text-muted d-flex flex-wrap align-items-center">
                        <?php  if ($product['special']) { ?>
                            <div class="fs-6 text-black me-2"><?php echo $product['special'] . $tax_message; ?></div>
                            <div class="fs-6 text-decoration-line-through me-2"><?php echo $product['price']; ?></div>
                        <?php } else { ?>
                            <div class="text-black"><?php echo $product['price'] . $tax_message?></div>
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