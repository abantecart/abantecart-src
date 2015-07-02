<?php //populate product div on client side ?>
(function(){
	var html = '';
	if($('#<?php echo $target;?> .abantecart_name')){
		$('#<?php echo $target;?> .abantecart_name').html('<?php echo $product['name']?>');
	}

	if($('#<?php echo $target;?> .abantecart_image')){
		html = '<a data-href="<?php echo $product_details_url;?>"  data-id="<?php echo $product['product_id']; ?>" data-html="true" data-target="#abc_embed_modal" data-toggle="abcmodal" href="#" class="product_thumb" data-original-title="">'
			+ '<?php echo $product['thumbnail']['thumb_html']?></a>';
		$('#<?php echo $target;?> .abantecart_image').html(html);
	}

<?php
if($product['price']){?>
	if($('<?php echo $target;?> .abantecart_price')){
	<?php if ($product['special']) { ?>
			html = '<div class="priceold"><?php echo $product['price'] ?></div>'+
					'<div class="pricenew"><?php echo $product['special'] ?></div>';
		<?php } else { ?>
			html = '<div class="oneprice"><?php echo $product['price'] ?></div>'
		<?php } ?>

		$('#<?php echo $target;?> .abantecart_price').html(html);
	}
<?php
}

if($product['rating']){?>
	if($('<?php echo $target;?> .abantecart_rating')){
		html = '<?php echo '<img src="' . $this->templateResource('/image/stars_' . (int)$product['rating'] . '.png') . '" alt="' . $product['stars'] . '" />'?>';
		$('#<?php echo $target;?> .abantecart_rating').html(html);
	}
<?php
}

if($product['quantity']){?>
	if($('<?php echo $target;?> .abantecart_quantity')){
		html = '<div class="input-group col-sm-6 pull-left"><span class="input-group-addon"><?php echo $text_qty;?></span><input type="text" size="3" class="form-control short" placeholder="" value="<?php echo $product['quantity']->value?>" id="product_quantity" name="<?php echo $product['quantity']->name?>"></div>';
		$('#<?php echo $target;?> .abantecart_quantity').html(html);
	}
<?php }

if($product['button_addtocart']){?>
	if($('<?php echo $target;?> .abantecart_addtocart')){
		html = '<?php echo str_replace("\n",'',$product['button_addtocart']); ?>';
		$('#<?php echo $target;?> .abantecart_addtocart').html(html);
	}
<?php }

if($product['blurb']){?>
	if($('#<?php echo $target;?> .abantecart_blurb')){
		$('#<?php echo $target;?> .abantecart_blurb').html('<?php echo $product['blurb']?>');
	}
<?php }?>

})();
