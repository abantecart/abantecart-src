<div class="sidewidt">

<?php
	if ( $block_framed ) { ?>
	<div class="block_frame block_frame_<?php echo $block_details['block_txt_id'];?>"
				 id="block_frame_<?php echo $block_details['block_txt_id'].'_'.$block_details['instance_id'] ?>">
	<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>
<?php } ?>
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
        $item['rating'] = ($product['rating']) ? "<img class=\"rating\" src='". $this->templateResource('/image/stars_'.$product['rating'].'.png') ."' alt='".$product['stars']."' width='64' height='12' />" : '';
                
        $item['info_url'] = $product['href'];
        $item['buy_url'] = $product['add'];
	    if(!$display_price){
		    $item['price'] = '';
	    }
	    
	    $review = $button_write;
	    if ($item['rating']) {
	    	$review = $item['rating'];
	    }
	    
?>      
              <li class="col-xs-12">
              	<a href="<?php echo $item['info_url']?>">
	                <img alt="<?php echo $item['title']?>" class="thumbnail_small" src="<?php echo $item['image']?>"/></a>
              	<a class="productname" href="<?php echo $item['info_url']?>"><?php echo $item['title']?></a>
              	<?php if ($review_status) { ?>
                <span class="procategory"><?php echo $item['rating']?></span>
                <?php } ?>
                <span class="price">
		        <?php  if ($product['special']) { ?>
		            <span class="pricenew"><?php echo $product['special'] . $tax_message?></span>
		        	<span class="priceold"><?php echo $product['price']?></span>
		        <?php } else { ?>
		            <span class="oneprice"><?php echo $product['price'] . $tax_message?></span>
		  		<?php } ?>
                </span>
              </li>
<?php
	}
}
?>
		</ul>
<?php if ( $block_framed ) { ?>
	</div>
<?php } ?>
</div>