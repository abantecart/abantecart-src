<h1 class="heading1">
  <span class="maintext"><?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="container-fluid">

	<h4 class="heading4"><?php echo $text_critea; ?></h4>
	<form class="form-inline">
		<fieldset>
			<div class="control-group">
				<div class="controls">
				    <?php echo $keyword . $category; ?>&nbsp;
				    <?php echo $description; ?>&nbsp;
				    <?php echo $model; ?>&nbsp;
				    <?php echo $submit; ?>
				</div>
			</div>		
		</fieldset>
	</form>
			
	<h4 class="heading4"><?php echo $text_search; ?></h4>
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
				
	
<?php } else { ?>
		<div>
			<?php echo $text_empty; ?>
		</div>
<?php } ?>		

		
</div>





<div id="content">
	<div class="top">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center">
			<h1><?php echo $heading_title; ?></h1>
		</div>
	</div>
	<div class="middle"><b><?php echo $text_critea; ?></b>

		<div id="content_search">
			<table>
				<tr>
					<td><?php echo $entry_search; ?></td>
					<td><?php echo $keyword . $category; ?></td>
				</tr>
				<tr>
					<td colspan="2"><?php echo $description; ?></td>
				</tr>
				<tr>
					<td colspan="2"><?php echo $model; ?></td>
				</tr>
			</table>
		</div>
		<div class="buttons">
			<table>
				<tr>
					<td align="right"><?php echo $submit; ?></td>
				</tr>
			</table>
		</div>
		<div class="heading"><?php echo $text_search; ?></div>
		<?php if (isset($products)) { ?>
			<div class="sort">
				<div class="div1"><?php echo $sorting; ?></div>
				<div class="div2"><?php echo $text_sort; ?></div>
			</div>
			<table class="list">
				<?php for ($i = 0; $i < sizeof($products); $i = $i + 4) { ?>
				<tr>
					<?php for ($j = $i; $j < ($i + 4); $j++) { ?>
					<td class="list_product"><?php if (isset($products[$j])) { ?>
						<a href="<?php echo $products[$j]['href']; ?>"><?php echo $products[$j]['thumb']['thumb_html']; ?></a>
						<br/>
						<a href="<?php echo $products[$j]['href']; ?>"><?php echo $products[$j]['name']; ?></a><br/>
						<span class="model"><?php echo $products[$j]['model']; ?></span><br/>
						<div class="price-add">
						<?php if ($display_price) { ?>
							<?php if (!$products[$j]['special']) { ?>
								<span class="price"><?php echo $products[$j]['price']; ?></span>
								<?php } else { ?>
								<span class="regular-price"><?php echo $products[$j]['price']; ?></span> <span
									class="special-price"><?php echo $products[$j]['special']; ?></span>
								<?php } ?>
							<?php } ?>
						<a class="info" href="<?php echo $products[$j]['href']; ?>"></a>
						<a class="buy" id="<?php echo $products[$j]['product_id']?>"
						   href="<?php echo $products[$j]['add']; ?>" title="<?php echo $button_add_to_cart; ?>"></a>
						</div>
						<br/>
						<?php echo $products[$j]['buttons']; ?>
						<?php if ($products[$j]['rating']) { ?>
							<img
								src="<?php echo $this->templateResource('/image/stars_' . $products[$j]['rating'] . '.png'); ?>"
								alt="<?php echo $products[$j]['stars']; ?>"/>
							<?php } ?>
						<?php } ?></td>
					<?php } ?>
				</tr>
				<?php } ?>
			</table>
			<div class="pagination"><?php echo $pagination; ?></div>
			<?php } else { ?>
			<div
				style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-top: 3px; margin-bottom: 15px;"><?php echo $text_empty; ?></div>
			<?php }?>
	</div>
	<div class="bottom">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center"></div>
	</div>
</div>
<script type="text/javascript"><!--
$('#content_search input').keydown(function (e) {
	if (e.keyCode == 13) {
		contentSearch();
	}
});
$('#search_button').click(function (e) {
	contentSearch();
});

$('#sort').change(function () {
	contentSearch();
});

function contentSearch() {
	url = 'index.php?rt=product/search&limit=<?php echo $limit; ?>';

	var keyword = $('#keyword').attr('value');

	if (keyword) {
		url += '&keyword=' + encodeURIComponent(keyword);
	}

	var category_id = $('#category_id').attr('value');

	if (category_id) {
		url += '&category_id=' + encodeURIComponent(category_id);
	}

	if ($('#description').is(':checked')) {
		url += '&description=1';
	}

	if ($('#model').is(':checked')) {
		url += '&model=1';
	}
	url += '&sort=' + $('#sort').val();

	location = url;
}
//--></script>