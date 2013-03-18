<section id="speacial" class="row mt40">
    <div class="container">
<?php if ( $block_framed ) { ?>
      <h1 class="heading1"><span class="maintext"><?php echo $heading_title; ?></span><span class="subtext"><?php echo $heading_subtitle; ?></span></h1>
<?php } ?>
      <ul class="thumbnails">
<?php
if ($products) {
    foreach ($products as $product) {
        $item = array();
        $item['image'] = $product['thumb']['thumb_html'];
        $item['title'] = $product['name'];
        $item['description'] = $product['model'];
        $item['rating'] = ($product['rating']) ? "<img src='". $this->templateResource('/image/stars_'.$product['rating'].'.png') ."' alt='".$product['stars']."' />" : '';
                
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
        <li class="span3">
          <a class="prdocutname" href="<?php echo $item['info_url']?>"><?php echo $item['title']?></a>
          <div class="thumbnail">
          <?php  if ($product['special']) { ?>
            <span class="sale tooltip-test"><?php $text_sale_label; ?></span>
          <?php } ?>  
          <?php  if ($product['new_product']) { ?>
          	<span class="new tooltip-test" ><?php $text_new_label; ?></span>
          <?php } ?>            
            <a href="<?php echo $item['info_url']?>"><?php echo $item['image']?></a>
            <div class="shortlinks">
              <a class="details" href="<?php echo $item['info_url']?>"><?php echo $button_view ?></a>
              <a class="compare" href="<?php echo $item['info_url']?>"><?php echo $review ?></a>
            </div>
            <div class="pricetag">
              <span class="spiral"></span><a id="<?php echo $product['product_id']?>" href="<?php echo $item['buy_url']?>" class="productcart"><?php echo $button_add_to_cart?></a>
              <div class="price">
        <?php  if ($product['special']) { ?>
            <div class="pricenew"><?php echo $product['special']?></div>
        	<div class="priceold"><?php echo $product['price']?></div>
        <?php } else { ?>
            <div class="pricenew"><?php echo $product['price']?></div>
  		<?php } ?>
              </div>
            </div>
          </div>
        </li>
<?php
	}
}
?>      
      </ul>
      
<?php if ( $block_framed ) { ?>
<?php } ?>
	</div>
</section>