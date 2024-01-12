<div class="cart-top-block">
    <ul class="nav navbar-nav topcart">
        <?php //mobile view ?>
        <li class="nav-item d-block d-md-none">
            <a class="image-link d-inline-flex position-relative align-items-center justify-content-center rounded-circle" data-bs-toggle="offcanvas" href="javascript:void(0)" role="button" aria-controls="cartoffcanvas" aria-label="cart link">
                <i class="bi bi-cart3"></i> <span class="link-badge bg-danger position-absolute rounded-circle d-flex align-items-center justify-content-center"><?php echo $total_qty;?> </span>
            </a>
        </li>
        <?php //medium device and larger view ?>
        <li class="nav-item dropdown d-none d-md-block">
            <a class="image-link d-inline-flex position-relative align-items-center justify-content-center rounded-circle" data-bs-toggle="offcanvas" href="#cartoffcanvas" role="button" aria-controls="cartoffcanvas" aria-label="cart link">
                <i class="bi bi-cart3"></i> <span class="link-badge bg-danger rounded-circle position-absolute d-flex align-items-center justify-content-center"><?php echo $total_qty;?> </span>
            </a>
        </li>
    </ul>

</div>
<div class="offcanvas card-offcanvas offcanvas-end" tabindex="-1" id="cartoffcanvas" aria-labelledby="cartoffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <div class="row w-100 align-items-center justify-content-between">
            <div class="col-auto">
                <h5 class="mb-0">Shopping cart (<?php echo $total_qty;?>)</h5>
            </div>
            <div class="col-auto"><a href="#" class="link-secondary"></a> <button type="button" class="btn-close"
                                                                                  data-bs-dismiss="offcanvas" aria-label="Close"></button></div>
        </div>
    </div>
    <div class="offcanvas-body">
        <div class="alert alert-dismissible fade show pe-3" role="alert">
            <div id="top_cart_product_list">
                <?php include( $this->templateResource('/template/responses/checkout/cart_details.tpl') ) ?>
            </div>
        </div>
    </div>
    <div class="offcanvas-body border-top mt-4 pt-4">
    <div class="row">
        <?php echo $this->getHookVar('cart_top_pre_buttons_hook'); ?>
        <div class="col-6">
            <a href="<?php echo $this->html->getSecureURL(('checkout/cart')); ?>"><div class="d-grid"><button class="btn btn-dark">VIEW CART</button></div></a>
        </div>
        <div class="col-6">
            <a href="<?php echo $this->html->getSecureURL('checkout/shipping');?>"><div class="d-grid"><button class="btn btn-warning">CHECKOUT</button></div></a>
        </div>
        <?php echo $this->getHookVar('cart_top_post_buttons_hook'); ?>
    </div>
    </div>
</div>