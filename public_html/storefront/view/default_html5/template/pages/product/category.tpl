<h1 class="heading1">
  <span class="maintext"><?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="container-fluid">

	<?php if ($description) { ?>
	<div style="margin-bottom: 15px;"><?php echo $description; ?></div>
	<?php } ?>
	<?php if (!$categories && !$products) { ?>
	<div class="content"><?php echo $text_error; ?></div>
	<?php } ?>
	<?php if ($categories) { ?>
	<ul class="thumbnails row">
	    <?php for ($i = 0; $i < sizeof($categories); $i++) { ?>
	     <li class="span2">
	    	<a href="<?php echo $categories[ $i ][ 'href' ]; ?>">
	    		<?php echo $categories[ $i ][ 'thumb' ][ 'thumb_html' ]; ?>
	    	</a>
	    	<div class="span2">
	    	<a href="<?php echo $categories[ $i ][ 'href' ]; ?>"><?php echo $categories[ $i ][ 'name' ]; ?></a>
	    	</div>
	    </li>
	    <?php } ?>
	</ul>
	<?php } ?>

	<?php if ($products) { ?>
	<!-- Sorting + pagination-->
	<div class="sorting well">
	  <form class=" form-inline pull-left">
	    <?php echo $text_sort; ?> : <?php echo $sorting; ?>
	  </form>
	  <div class="btn-group pull-right">
	    <button class="btn" id="list"><i class="icon-th-list"></i>
	    </button>
	    <button class="btn btn-orange" id="grid"><i class="icon-th icon-white"></i></button>
	  </div>
	</div>
	<!-- end sorting-->

	<ul class="thumbnails grid row">
<?php		
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
?>
	</ul>

	<ul class="thumbnails list row">
<?php		
    foreach ($products as $product) {
        $item = array();
        $item['image'] = $product['thumb']['thumb_html'];
        $item['title'] = $product['name'];
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
        <li>
          <div class="thumbnail">
	          <div class="row">
	          	 <div class="span4">
		          <?php  if ($product['special']) { ?>
		            <span class="sale tooltip-test"><?php $text_sale_label; ?></span>
		          <?php } ?>  
		          <?php  if ($product['new_product']) { ?>
		          	<span class="new tooltip-test" ><?php $text_new_label; ?></span>
		          <?php } ?>            
		            <a href="<?php echo $item['info_url']?>"><?php echo $item['image']?></a>
	          	 </div>
	          	 <div class="span8">
	          	 	<a class="prdocutname" href="<?php echo $item['info_url']?>"><?php echo $item['title']?> (<?php echo $product['model']?>)</a>
					<div class="productdiscrption"><?php echo $product['description']?></div>
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
	          	
	          </div>
          </div>
        </li>
<?php
	}
?>

	</ul>
		
	<!-- Sorting + pagination-->
	<div class="sorting well">
		<div class="pagination"><?php echo $pagination; ?></div>	
		<div class="btn-group pull-right">
		</div>
	</div>
	<!-- end sorting-->
				
	
	
<?php } ?>		

		
</div>

<script type="text/javascript"><!--

$('#sort').change(function () {
	Resort();
});

function Resort() {
	url = '<?php echo $url; ?>';
	url += '&sort=' + $('#sort').val();
	url += '&limit=' + $('#limit').val();
	location = url;
}
//--></script>