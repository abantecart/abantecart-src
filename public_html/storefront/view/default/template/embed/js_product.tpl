<?php //populate product div on client side ?>
var abcDo = function(){
	var html = '';
	if($('<?php echo $target;?> .abantecart_name')){
		$('<?php echo $target;?> .abantecart_name').html('<?php echo $product['name']?>');
	}

	if($('<?php echo $target;?> .abantecart_image')){
		html = '<a href="<?php echo $product_details_url;?>"  data-id="<?php echo $product['product_id']; ?>" data-html="true" data-target="#abc_embed_modal" data-toggle="modal" href="#" class="product_thumb" data-original-title="">'
			+ '<?php echo $product['thumbnail']['thumb_html']?></a>';
		$('<?php echo $target;?> .abantecart_image').html(html);
	}

<?php
if($product['price']){?>
	if($('<?php echo $target;?> .abantecart_price')){
		$('<?php echo $target;?> .abantecart_price').html('<?php echo $product['price']?>');
	}
<?php
}

if($product['button_addtocart']){?>
	if($('<?php echo $target;?> .abantecart_addtocart')){
		html = '<?php echo str_replace("\n",'',$product['button_addtocart']); ?>'
		$('<?php echo $target;?> .abantecart_addtocart').html(html);
	}
<?php }?>

}
