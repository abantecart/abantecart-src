<div class="sidewidt">
	<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>
		<ul class="side_prd_list">
<?php
if ($products) {
    foreach ($products as $product) {
        $item = array();
        $item['image'] = $product['thumb']['thumb_url'];
        $item['title'] = $product['name'];
        $item['description'] = $product['model'];
        //$item['rating'] = ($product['rating']) ? "<img src='". $this->templateResource('/image/stars_'.$product['rating'].'.png') ."' alt='".$product['stars']."' />" : '';
                
        $item['info_url'] = $product['href'];
        $item['buy_url'] = $product['add'];
	    if(!$display_price){
		    $item['price'] = '';
	    }
	    
	    $review = $button_write;
	  /*  if ($item['rating']) {
	    	$review = $item['rating'];
	    }*/
	    
?>      
              <li class="col-xs-12">
              	<a href="<?php echo $item['info_url']?>"><img class="thumbnail_small" src="<?php echo $item['image']?>" alt=""/></a>
              	<a class="productname" href="<?php echo $item['info_url']?>"><?php echo $item['title']?></a>
              	<?php if ($review_status) { ?>
                <span class="procategory"><?php echo $item['rating']?></span>
                <?php } ?>
        <?php if ($display_price) { ?>        
	                <div class="price">
			        <?php  if ($product['special']) { ?>
			            <span class="pricenew"><?php echo $product['special']?></span>
			        	<span class="priceold"><?php echo $product['price']?></span>
			        <?php } else { ?>
			            <span class="oneprice"><?php echo $product['price']?></span>
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