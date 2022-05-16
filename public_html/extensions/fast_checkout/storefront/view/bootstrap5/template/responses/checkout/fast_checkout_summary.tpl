
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
        if ($total_items > 0) {
        ?>
        <div class="products">
        <table class="table table-hover table-borderless">
            <tbody>
            <?php
            for ($i = 0; $i < $cart_view_limit && $i < $total_items; $i++) {
                $product = $products[$i];
            ?>
                <tr>
                    <td class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-none d-md-flex mx-1">
                            <img alt="" class="product-icon" src="<?php echo $product['thumbnail']['main_url']; ?>">
                        </div>
                        <div class="ms-auto me-1 text-wrap">
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
                            </div>
                        </div>
                        <div class="ms-auto text-end">
                            <?php echo $product['price']; ?>
                            <span class="text-nowrap"><i class="mx-2 fa fa-times fa-fw"></i><?php echo $product['quantity']; ?></span>
                        </div>
                    </td>
                </tr>
            <?php } ?>

            <?php if ($total_items > $cart_view_limit) {  ?>
                <tr>
                    <td >
                        <a class="d-flex justify-content-center" title="see more cart products" href="<?php echo $view; ?>">
                            <i class="fa fa-chevron-down fa-lg"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
        <table class="table">
            <tbody>
            <?php foreach ($totals as $total) { ?>
                <tr>
                    <th><?php echo $total['title']; ?></th>
                    <td><span class="float-end"><?php echo $total['text']; ?></span></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
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
