<div class="cart-top-block navbar">
    <ul class="nav navbar-nav topcart">
        <?php //mobile view ?>
        <li class="nav-item d-block d-md-none">
            <a href="<?php echo $view; ?>" class="nav-link active">
                <i class="fa fa-shopping-cart fa-fw"></i>&nbsp;&nbsp;
                <span class="label-qnty">
                    <?php echo $total_qty;?>
                </span>
                <?php echo $text_items;?>
                -
                <span class="cart_total">
                    <?php echo $subtotal; ?>
                </span>
            </a>
        </li>
        <?php //medium device and larger view ?>
        <li class="nav-item dropdown d-none d-md-block">
            <a href="<?php echo $view; ?>" class="nav-link active dropdown-toggle">
                <i class="fa fa-shopping-cart fa-fw"></i>&nbsp;&nbsp;
                <span class="label-qnty">
                    <?php echo $total_qty;?>
                </span>
                <?php echo $text_items;?>
                -
                <span class="cart_total">
                    <?php echo $subtotal; ?>
                </span>
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
                                <i class="fa fa-shopping-cart fa-fw"></i>
                            </a>
                        </div>
                        <div class="col-sm-6 col-xs-6  text-center">
                            <a class="btn btn-outline-primary" href="<?php echo $checkout; ?>" title="<?php echo $text_checkout; ?>">
                                <i class="fa fa-money-bill fa-fw"></i>
                            </a>
                        </div>

                        <?php echo $this->getHookVar('cart_top_post_buttons_hook'); ?>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>