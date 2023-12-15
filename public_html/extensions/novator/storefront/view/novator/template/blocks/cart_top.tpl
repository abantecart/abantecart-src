<div class="cart-top-block">
    <ul class="nav navbar-nav topcart">
        <?php //mobile view ?>
        <li class="nav-item d-block d-md-none">
            <!-- Commented by TM 
            <a href="<?php echo $view; ?>" class="nav-link active">
                <i class="bi bi-shopping-cart fa-fw"></i>&nbsp;&nbsp;
                <span class="label-qnty">
                    <?php echo $total_qty;?>
                </span>
                <?php echo $text_items;?>
                -
                <span class="cart_total">
                    <?php echo $subtotal; ?>
                </span>
            </a> 
            
            -->
            <a class="image-link d-inline-flex position-relative align-items-center justify-content-center rounded-circle" data-bs-toggle="offcanvas" href="#cartoffcanvas" role="button" aria-controls="cartoffcanvas" aria-label="cart link">
                <i class="bi bi-cart3"></i> <span class="link-badge bg-danger position-absolute rounded-circle d-flex align-items-center justify-content-center"><?php echo $total_qty;?> </span>
            </a>
        </li>
        <?php //medium device and larger view ?>
        <li class="nav-item dropdown d-none d-md-block">
            <!-- <a href="<?php echo $view; ?>" class="nav-link active dropdown-toggle">
                <i class="bi bi-shopping-cart fa-fw"></i>&nbsp;&nbsp;
                <span class="label-qnty">
                    <?php echo $total_qty;?>
                </span>
                <?php echo $text_items;?>
                -
                <span class="cart_total">
                    <?php echo $subtotal; ?>
                </span>
            </a>
             -->
            <a class="image-link d-inline-flex position-relative align-items-center justify-content-center rounded-circle" data-bs-toggle="offcanvas" href="#cartoffcanvas" role="button" aria-controls="cartoffcanvas" aria-label="cart link">
                <i class="bi bi-cart3"></i> <span class="link-badge bg-danger rounded-circle position-absolute d-flex align-items-center justify-content-center"><?php echo $total_qty;?> </span>
            </a>

            <ul class="dropdown-menu topcartopen ">
                <li class="dropdown-item-text">
                    <div id="top_cart_product_list">
                    <?php include( $this->templateResource('/template/responses/checkout/cart_details.tpl') ) ?>
                    </div>

                    <div class="d-flex justify-content-evenly">
                        <?php echo $this->getHookVar('cart_top_pre_buttons_hook'); ?>

                        <div class="col-sm-6 col-xs-6 text-center">
                            <a class="btn btn-outline-dark" href="<?php echo $view; ?>" title="<?php echo $text_view;?>">
                                <i class="bi bi-shopping-cart"></i>
                            </a>
                        </div>
                        <div class="col-sm-6 col-xs-6  text-center">
                            <a class="btn btn-outline-primary" href="<?php echo $checkout; ?>" title="<?php echo $text_checkout; ?>">
                                <i class="bi bi-money-bill"></i>
                            </a>
                        </div>

                        <?php echo $this->getHookVar('cart_top_post_buttons_hook'); ?>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
    
</div>