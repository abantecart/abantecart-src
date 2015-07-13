<?php //populate category div on client side ?>
(function(){
	var html = '';

<?php foreach($manufacturers as $manufacturer){
	$target = $targets[$manufacturer['manufacturer_id']];	?>

	if($('#<?php echo $target;?> .abantecart_name')){
		html = '<a data-href="<?php echo $manufacturer['details_url'];?>"  data-id="<?php echo $manufacturer['manufacturer_id']; ?>" data-html="true" data-target="#abc_embed_modal" data-toggle="abcmodal" href="#">'
			+ '<?php echo $manufacturer['name']?></a>';
		$('#<?php echo $target;?> .abantecart_name').html(html);
	}

	if($('#<?php echo $target;?> .abantecart_image')){
		html = '<a data-href="<?php echo $manufacturer['details_url'];?>"  data-id="<?php echo $manufacturer['manufacturer_id']; ?>" data-html="true" data-target="#abc_embed_modal" data-toggle="abcmodal" href="#" class="category_thumb">'
			+ '<?php echo $manufacturer['thumbnail']['thumb_html']?></a>';
		$('#<?php echo $target;?> .abantecart_image').html(html);
	}

	if($('#<?php echo $target;?> .abantecart_products_count')){
		$('#<?php echo $target;?> .abantecart_products_count').html('(<?php echo $manufacturer['products_count']?>)');
	}

<?php } ?>

})();
