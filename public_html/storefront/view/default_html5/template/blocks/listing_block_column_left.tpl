<div class="side_block">
<?php if ( $block_framed ) { ?>
	<h2><?php echo $heading_title; ?></h2>
<?php } ?>

	<ul class="thumbnails">
<?php
if ($content) {
foreach ($content as $product) {
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
   <li class="span3" >
     <div class="fixed_wrapper">
     	<div class="fixed">
     	<a class="prdocutname" href="<?php echo $item['info_url']?>" title="<?php echo $item['title']?>"><?php echo $item['title']?></a>
     	</div>
</div>
     <div class="thumbnail">
     <?php  if ($product['special']) { ?>
       <span class="tooltip-test"><?php $text_sale_label; ?></span>
     <?php } ?>
     <?php  if ($product['new_product']) { ?>
     	<span class="tooltip-test" ><?php $text_new_label; ?></span>
     <?php } ?>
       <a href="<?php echo $item['info_url']?>"><?php echo $item['image']?></a>
       <div class="shortlinks">
         <a class="details" href="<?php echo $item['info_url']?>"><?php echo $button_view ?></a>
         <a class="compare" href="<?php echo $item['info_url']?>"><?php echo $review ?></a>
       </div>
       <div class="pricetag">
         <span class="spiral"></span><a id="<?php echo $product['product_id']?>" href="<?php echo $item['buy_url']?>" class="productcart"><?php echo $button_add_to_cart?></a>
         <div class="price">

       <div class="pricenew"><?php echo $product['price']?></div>

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