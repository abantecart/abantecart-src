<?php //show html content of cart in float div on client side ?>
 (function(){
	$('.abantecart-widget-cart').remove();
	<?php if($cart_count){?>
		html = '<a class="abantecart-widget-cart" data-href="<?php echo $cart_url;?>" data-html="true" data-target="#<?php echo $abc_embed_modal_id ;?>" data-toggle="<?php echo $abcmodal_class_id; ?>" href="#" title="<?php echo $text_view;?>"> \
				<div class="abantecart-widget-cart"> \
					<div class="cart_count"> \
						<?php echo $cart_count; ?> \
					</div>	\
				</div></a>';
		$('body').append(html);
	<?php } ?>
 })();
 