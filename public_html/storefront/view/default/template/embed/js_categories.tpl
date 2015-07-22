<?php //populate category div on client side ?>
(function(){
	var html = '';

<?php foreach($categories as $category){
	$target = $targets[$category['category_id']];	?>

	if($('#<?php echo $target;?> .abantecart_name')){
		html = '<a data-href="<?php echo $category['details_url'];?>"  data-id="<?php echo $category['category_id']; ?>" data-html="true" data-target="#abc_embed_modal" data-toggle="abcmodal" href="#" class="category_thumb">'
			+ '<?php echo $category['name']?></a>';
		$('#<?php echo $target;?> .abantecart_name').html(html);
	}

	if($('#<?php echo $target;?> .abantecart_image')){
		html = '<a data-href="<?php echo $category['details_url'];?>"  data-id="<?php echo $category['category_id']; ?>" data-html="true" data-target="#abc_embed_modal" data-toggle="abcmodal" href="#" class="category_thumb" data-original-title="">'
			+ '<?php echo $category['thumbnail']['thumb_html']?></a>';
		$('#<?php echo $target;?> .abantecart_image').html(html);
	}

	if($('#<?php echo $target;?> .abantecart_products_count')){
		$('#<?php echo $target;?> .abantecart_products_count').html('(<?php echo $category['products_count']?>)');
	}

<?php } ?>

})();
