<ul class="nav topcart pull-left">
    <li class="dropdown hover"> 
        <a href="<?php echo $view; ?>" class="dropdown-toggle"><i class="fa fa-shopping-cart fa-fw"></i>&nbsp;&nbsp;<span
            class="label label-orange font14"><?php echo $total_qty;?></span> <?php echo $text_items;?> - <span
            class="cart_total"><?php echo $subtotal; ?></span> <b class="caret"></b></a>
        <ul class="dropdown-menu topcartopen ">
            <li>
				<div id="top_cart_product_list">
				<?php include( $this->templateResource('/template/responses/checkout/cart_details.tpl') ) ?>
				</div>
					
				<div class="buttonwrap">
				    <?php echo $this->getHookVar('cart_top_pre_buttons_hook'); ?>
				    <a class="btn btn-orange btn-xs pull-left" href="<?php echo $view; ?>"><i class="fa fa-shopping-cart fa-fw"></i> <?php echo $text_view;?></a>&nbsp;&nbsp;
				    <a class="btn btn-orange btn-xs pull-right"
				       href="<?php echo $checkout; ?>"><i class="fa fa-pencil fa-fw"></i>  <?php echo $text_checkout; ?></a>
				    <?php echo $this->getHookVar('cart_top_post_buttons_hook'); ?>
				</div>
            </li>
        </ul>
    </li>
</ul>