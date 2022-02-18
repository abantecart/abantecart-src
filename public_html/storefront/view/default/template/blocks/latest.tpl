<div class="sidewidt">
	<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>
		<ul class="side_prd_list">
<?php
if ($products) {
	$tax_exempt = $this->customer->isTaxExempt();
	$config_tax = $this->config->get('config_tax');
    foreach ($products as $product) {
		$tax_message = '';
		if ($config_tax && !$tax_exempt && $product['tax_class_id']){
			$tax_message = '&nbsp;&nbsp;'.$price_with_tax;
		}
        $item = array();
        $item['image'] = $product['thumb']['thumb_url'];
        $item['title'] = $product['name'];
        $item['description'] = $product['model'];

        $item['info_url'] = $product['href'];
        $item['buy_url'] = $product['add'];
	    if(!$display_price){
		    $item['price'] = '';
	    }
	    
	    $review = $button_write;
?>
		  <li class="col-xs-12">
			<a href="<?php echo $item['info_url']?>">
				<img alt="<?php echo $item['title']?>" class="thumbnail_small" src="<?php echo $item['image']?>"/>
			</a>
			<a class="productname" href="<?php echo $item['info_url']?>"><?php echo $item['title']?></a>
			<?php if ($review_status) { ?>
			<span class="procategory"><?php echo $item['rating']?></span>
			<?php } ?>
	<?php if ($display_price) { ?>
				<div class="price">
				<?php  if ($product['special']) { ?>
					<span class="pricenew"><?php echo $product['special'] . $tax_message?></span>
					<span class="priceold"><?php echo $product['price']?></span>
				<?php } else { ?>
					<span class="oneprice"><?php echo $product['price'] . $tax_message?></span>
				<?php } ?>
				</div>
	<?php } ?>
		  </li>
<?php
	}
}
?>
		</ul>
</div>