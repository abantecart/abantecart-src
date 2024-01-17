<?php
$total_items = sizeof((array)$products);
//To remove limit set $cart_view_limit = $total_items;
//To enable scroll for all products look for #top_cart_product_list .products in styles.css
$cart_view_limit = 5;
if ($total_items > 0) {
?>
<div class="products">

    <?php echo $this->getHookVar('cart_top_pre_list_hook'); ?>
    <?php
    for ($i = 0; $i < $cart_view_limit && $i < $total_items; $i++) {
        $product = $products[$i];
        ?>
        <div class="alert alert-dismissible fade show" role="alert">
            <div id="top_cart_product_list">
                <div class="d-flex mb-1">
                    <div class="flex-shrink-0"><img src="<?php echo $product['thumb']['thumb_url']; ?>" alt="image"
                                                    class="img-fluid h-auto"></div>
                    <div class="flex-grow-1 ms-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <p class="mb-0"><?php echo $product['name']; ?></p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="input-group input-group-sm mx-auto" style="width: 150px;">
                                <button title="Min: 1" class="minus-qnty input-group-text btn btn-outline-danger">−
                                </button>
                                <input type="text" name="quantity[111]" id="cart_quantity111" value="<?php echo $product['quantity']; ?>" placeholder=""
                                       class="form-control text-center fw-bold short form-control-sm text-center"
                                       size="6" min="1">
                                <button title="" class="plus-qnty input-group-text btn btn-outline-success">+</button>
                            </div>
                            <h5 class="mb-0"><?php echo $product['price']; ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $this->getHookVar('cart_details_'.$product['key'].'_additional_info_1'); ?>
    <?php } ?>
</div>
    <div class="offcanvas-body border-top mt-4 p-0 pt-4 ">
        <?php foreach ($totals as $total) { ?>
            <div class="d-flex  align-items-center justify-content-between mb-3 px-0 mx-4">
                <div class="col-auto ">
                    <h5 class="mb-0"><?php echo $total['title']; ?></h5>
                </div>
                <div class="col-auto">
                    <h5 class="mb-0"><?php echo $total['text']; ?></h5>
                </div>
            </div>
        <?php } ?>
        <?php echo $this->getHookVar('cart_top_pre_buttons_hook'); ?>
        <div class="d-flex justify-content-between ">
            <div class="col-5">
                <a href="<?php echo $this->html->getSecureURL('checkout/cart'); ?>">
                    <div class="d-grid">
                        <button class="btn btn-dark">VIEW CART</button>
                    </div>
                </a>
            </div>
            <div class="col-5">
                <a href="<?php echo $this->html->getSecureURL('checkout/shipping'); ?>">
                    <div class="d-grid">
                        <button class="btn btn-warning">CHECKOUT</button>
                    </div>
                </a>
            </div>
        </div>
        <?php echo $this->getHookVar('cart_top_post_buttons_hook'); ?>
    </div>
<?php } else { ?>
	<div class="empty_cart text-center">
		<i class="bi bi-shopping-cart"></i>
	</div>
<?php } ?>