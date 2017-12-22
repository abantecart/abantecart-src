<ul class="nav topcart pull-left">
    <li class="dropdown hover"> 
        <a href="<?php echo $view; ?>" class="dropdown-toggle"><i class="fa fa-shopping-cart fa-fw"></i>&nbsp;&nbsp;
			<span class="label label-orange font14">
				<?php echo $total_qty;?>
			</span>
			<?php echo $text_items;?>
			-
			<span class="cart_total">
				<?php echo $subtotal; ?>
			</span>
			<b class="caret"></b>
		</a>
        <ul class="dropdown-menu topcartopen ">
            <li>
				<div id="top_cart_product_list">
				<?php include( $this->templateResource('/template/responses/checkout/cart_details.tpl') ) ?>
				</div>

				<div class="row">
				    <?php echo $this->getHookVar('cart_top_pre_buttons_hook'); ?>

					<div class="col-sm-6 col-xs-6 text-center">
					    <a class="btn btn-default" href="<?php echo $view; ?>" title="<?php echo $text_view;?>">
							<i class="fa fa-shopping-cart fa-fw"></i>
						</a>
					</div>
					<div class="col-sm-6 col-xs-6  text-center">
				    	<a class="btn btn-primary" href="<?php echo $checkout; ?>" title="<?php echo $text_checkout; ?>">
							<i class="fa fa-money fa-fw"></i>
						</a>
					</div>

				    <?php echo $this->getHookVar('cart_top_post_buttons_hook'); ?>
				</div>
            </li>
        </ul>
    </li>
</ul>