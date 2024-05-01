<div id="cart_details d-flex flex-column mx-sm-auto">
    <?php if (is_array($products) && count($products) > 0) { ?>
			<h5>
				<?php echo $this->language->get('fast_checkout_order_summary');
				if ($cart_weight){
                    echo '  ('.$cart_weight.')';
                } ?>
			</h5>
        <?php
        $total_items = sizeof((array)$products);
        //To remove limit set $cart_view_limit = $total_items;
        //To enable scroll for all products look for #top_cart_product_list .products in styles.css
        $cart_view_limit = 5;
        if ($total_items > 0) { ?>
        <div class="products">
        <table class="table table-hover table-borderless">
            <tbody>
            <?php
            for ($i = 0; $i < $cart_view_limit && $i < $total_items; $i++) {
                $product = $products[$i];
            ?>
                <tr>
                    <td class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-none d-xl-flex mx-1 col-md-2">
                            <img style="width:<?php echo $product['thumbnail']['width']?>px;" class="product-icon" src="<?php echo $product['thumbnail']['main_url']; ?>">
                        </div>
                        <div class="ms-auto text-wrap col-12 col-xl-9">
                            <?php if($product['href']){ ?>
                                <a class="link-dark link-"  href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                            <?php }else{
                                echo $product['name'];
                            }?>
                            <div class="d-flex flex-column">
                                <?php foreach ($product['option'] as $option) { ?>
                                    <small class="text-muted text-wrap" title="<?php echo $option['title']?>">
                                        - <?php echo $option['name']; ?> <?php echo $option['value']; ?>
                                    </small>
                                <?php } ?>
                                <?php echo $this->getHookVar('fast_checkout_summary_product_'.$product['key'].'_additional_info'); ?>
                            </div>
                            <?php echo $this->getHookVar('fast_checkout_summary_product_'.$product['key'].'_additional_info_1'); ?>
                        </div>
                        <div class="ms-auto text-end">
                            <?php echo $product['price']; ?>
                            <span class="text-nowrap"><i class="mx-2 fa fa-times fa-fw"></i><?php echo $product['quantity']; ?></span>
                        </div>
                        <?php echo $this->getHookVar('fast_checkout_summary_product_'.$product['key'].'_additional_info_2'); ?>
                    </td>
                    <?php echo $this->getHookVar('fast_checkout_summary_product_'.$product['key'].'_additional_info_3'); ?>
                </tr>
            <?php } ?>

            <?php if ($total_items > $cart_view_limit) {  ?>
                <tr>
                    <td class="table-light text-center">
                        <a class="btn btn-lightblue btn-sm" title="see more cart products" href="<?php echo $this->html->getSecureUrl($cart_rt); ?>">
                            <i class="fa fa-angles-down fa-lg"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
        <?php foreach ($totals as $total) { ?>
            <div class="d-flex flex-nowrap justify-content-between mb-1">
                <div class="fw-bold"><?php echo $total['title']; ?></div>
                <div><span class="float-end"><?php echo $total['text']; ?></span></div>
            </div>
        <?php } ?>
        <?php } else { ?>
            <div class="empty_cart text-center">
                <i class="fa fa-shopping-cart"></i>
            </div>
        <?php } ?>

    <?php } ?>
</div>

<script>
	$('#cart_details').on('reload', function () {
		alert('reload please');
	});
</script>