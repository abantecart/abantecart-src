<?php //show html content of cart in float div on client side ?>

 (function(){
	$('.abantecart-widget-cart').remove();
	<?php if($cart_html){?>
		html = '<div class="abantecart-widget-cart"><?php echo str_replace("\n",'',$cart_html); ?></div>>';
		$('body').append(html);
	<?php } ?>
 })();
