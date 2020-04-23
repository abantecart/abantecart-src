(function(){
var html = '';

$('#<?php echo $target;?>.abantecart_collection').append('<style>ul.abantecart_products li { float: none;} .priceold { text-decoration: line-through; }</style><ul class="abantecart_products"></ul>');
<?php foreach((array)$products as $product){
    ?>
	if($('#<?php echo $target;?> .abantecart_collection ul')) {
	html = '<li><a data-href="<?php echo $product['product_details_url'];?>"  data-id="<?php echo $product['product_id']; ?>" data-html="true" data-target="#abc_embed_modal" data-backdrop="static" data-keyboard="false" data-toggle="abcmodal" href="#" class="product_thumb" data-original-title="">'
		+ '<?php echo $product['thumb']['thumb_html']; ?></a>';

	html += '<div><h3 class="abantecart_product_name"><a data-href="<?php echo $product['product_details_url'];?>"  data-id="<?php echo $product['product_id']; ?>" data-backdrop="static" data-keyboard="false" data-html="true" data-target="#abc_embed_modal" data-toggle="abcmodal" href="#" class="collection_thumb">'
				+ '<?php echo addslashes($product['name']); ?></a></h3></div>';



	<?php
if($product['price'] && $display_price){?>
	<?php if ($product['special']) { ?>
			html += '<div class="priceold"><?php echo $product['price'] ?></div>'+
					'<div class="pricenew"><?php echo $product['special'] ?></div>';
		<?php } else { ?>
			html += '<div class="oneprice"><?php echo $product['price'] ?></div>'
		<?php }
		}
?>

<?php
if($product['rating']){?>
		html += '<?php echo '<div class="raiting"><img src="' .AUTO_SERVER. $this->templateResource('/image/stars_' . (int)$product['rating'] . '.png') . '" alt="' . $product['stars'] . '" /></div>'?>';
<?php
}
?>

	html += '</li>'
		$('#<?php echo $target;?>.abantecart_collection ul').append(html);
	}
<?php }  ?>



})();
