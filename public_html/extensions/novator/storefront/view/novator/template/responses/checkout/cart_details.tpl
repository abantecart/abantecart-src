<?php
$total_items = sizeof((array)$products);
$cart_view_limit = $total_items;
if ($total_items > 0) { ?>
<div id="top_cart_product_list" class="products">

    <?php echo $this->getHookVar('cart_top_pre_list_hook'); ?>
    <?php
    for ($i = 0; $i < $cart_view_limit && $i < $total_items; $i++) {
        $product = $products[$i];
        ?>
        <div class="alert alert-dismissible fade show" role="alert">
                <div class="d-flex mb-1">
                    <div class="flex-shrink-0">
                        <img src="<?php echo $product['thumb']['thumb_url']; ?>" alt="<?php echo_html2view($product['name']);?>"
                             class="img-fluid">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <p class="mb-0"><?php echo $product['name']; ?></p>
                            <h5 class="mb-0"><?php echo $product['price']; ?></h5>
                            <button type="button" id="delete_product" class="btn-close" data-bs-dismiss="alert" aria-label="Close" data-product-key="<?php echo $product['key']; ?>"></button>
                        </div>
                        <div class="input-group input-group-sm mx-auto" style="width: 150px;">
                            <button title="Min: 1" class="minus-qnty input-group-text btn btn-outline-danger"
                                    data-product-id="<?php echo $product['key']; ?>">−</button>
                            <input type="number" name="quantity[<?php echo $product['key']; ?>]"
                                   class="cart-quantity-input form-control text-center fw-bold short form-control-sm text-center"
                                   value="<?php echo $product['quantity']; ?>" placeholder="" size="6" min="1"
                                   data-product-id="<?php echo $product['key']; ?>">
                            <button title="" class="plus-qnty input-group-text btn btn-outline-success" data-product-id="<?php echo $product['key']; ?>">+</button>
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
                        <button class="btn btn-dark"><?php echo $this->language->get('text_view', 'blocks/cart');?></button>
                    </div>
                </a>
            </div>
            <div class="col-5">
                <a href="<?php echo $this->html->getSecureURL('checkout/fast_checkout'); ?>">
                    <div class="d-grid">
                        <button class="btn btn-warning"><?php echo $this->language->get('text_checkout', 'blocks/cart'); ?></button>
                    </div>
                </a>
            </div>
        </div>
        <?php echo $this->getHookVar('cart_top_post_buttons_hook'); ?>
    </div>
<?php } else { ?>
	<div class="empty_cart text-center">
        <?php echo $this->getHookVar('cart_top_pre_cart_emty_title'); ?>
        <i class="bi bi-shopping-cart"><?php echo $text_empty;?></i>
        <?php echo $this->getHookVar('cart_top_post_cart_emty_title'); ?>
	</div>
<?php } ?>