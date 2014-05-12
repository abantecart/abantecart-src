<?php if ($error_warning) { ?>
	<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="extensionStoreBox">
	<div class="cbox_tl">
		<div class="cbox_tr">
			<div class="cbox_tc">
				<div class="heading"><?php echo $heading_title; ?></div>
				<div class="toolbar">
					<div class="buttons">
						<div class="flt_left align_left">
							<?php echo $btn_my_extensions; ?>
							<?php echo $btn_my_account; ?>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="cbox_cl">
		<div class="cbox_cr">
			<?php if($content){	?>
			<table class="extension-store-list cbox_cc">
				<tr>
					<td id="menu" class="span3 pull-left">
						<ul class="store-menu nav nav-pills nav-stacked">
							<li class="search-tab">
								<?php echo $form['form_open']; ?>
								<?php echo $form['input']; ?>
								<?php echo $form['submit']; ?>
								</form>
							</li>
							<?php
							foreach ($content['categories']['subcategories'] as $category) {
								?>
								<li class="<?php echo $category['active'] ? 'selected' : '' ?>">
									<a href="<?php echo $category['href'] ?>"
									   title="<?php echo trim($category['description']) ?>"><?php echo $category['name'] ?></a>
								</li>
							<?php } ?>
						</ul>
					</td>
					<td class="pull-left" style="text-align: center; width: 100%;">
								
						<div class="container-fluid">					
						<ul class="thumbnails">
							<?php
							if ($content['products']['rows']) {
								foreach ($content['products']['rows'] as $product) {
								
									$item = array();
									$item['image'] = $product['cell']['thumb'];
									$item['main_image'] = $product['cell']['main_image'];
									$item['title'] = $product['cell']['name'];
									$item['description'] = $product['cell']['model'];
									$item['rating'] = "<img src='" . $this->templateResource('/image/stars_' . (int)$product['cell']['rating'] . '.png') . "' alt='" . (int)$product['stars'] . "' />";
						
									$item['price'] = $product['cell']['price'];
									if ( substr( $product['cell']['price'],1) == '0.00' ) {
										$item['price'] = 'FREE';
									}
						
									if ($item['rating']) {
										$review = $item['rating'];
									}
						
									?>
									<li class="product-item row span3" data-product-id="<?php echo $product['id'] ?>">
										<div class="ext_thumbnail">
											<a class="product_thumb" title='' data-html="true" rel="tooltip">
											<img width="57" alt="" src="<?php echo $item['image'] ?>">
											</a>
											<div class="tooltip-data hidden" style="display: none;">
											<div class="product_data">
												<span class="prdocut_title" title="<?php echo $item['title'] ?>"><?php echo $item['title'] ?></span>
												<span class="review"><?php echo $review ?></span>
												<span class="price">
												    <span class="oneprice"><?php echo $item['price'] ?></span>
												</span>	
											</div>			
											<div class="product_image">	
												<img src="<?php echo $this->templateResource('/image/loading_row.gif'); ?>" class="load_ondemand" data-src="<?php echo $item['main_image'] ?>">
											</div>	
											</div>			
										</div>
										<div class="ext_details">
											<div class="ext_name">
												<div class="text_zoom">
												<a title="<?php echo $item['title']; ?>"><?php echo $item['title'] ?></a>
												</div>
											</div>
						
											<div class="ext_more">
												<div class="ext_review"><a class="compare"><?php echo $review ?></a></div>
												<div class="ext_price">
												    <div class="oneprice"><?php echo $item['price'] ?></div>
												</div>
						
												<div class="ext_icons">
													<a class="productcart" data-id="<?php echo $product['product_id'] ?>">
													<i class="icon-shopping-cart"></i>
													</a>
												</div>
											</div>
										</div>				
									</li>
								<?php
								}
							}
							?>
						</ul>					
						</div>	
						
						<?php if( $sorting && $pagination_bootstrap ) { ?>
						<div class="pagination" >
							<div class="sorting" style=""><?php echo $sorting; ?></div>
							<div class="pages"  style="display: inline-block; text-align: left;"><?php echo $pagination_bootstrap; ?></div>
						</div>
						<?php }?>
					</td>
				</tr>
			</table>
			<?php }else{ ?>
				<div class="cbox_cc" style="overflow: hidden;">
				<div class="warning alert-warning">Sorry, can not to connect with AbanteCart MarketPlace for now. Please check later or ask your service provider support.</div>
				</div>
			<?php }?>
		</div>
	</div>

<div class="cbox_bl">
	<div class="cbox_br">
		<div class="cbox_bc"></div>
	</div>
</div>
</div>
<script type="text/javascript">
	$('#frame_wrapper .alert').click(function(){
		$('.extension-store-list').show();
		$('#frame_wrapper').slideUp(1000);
		$('#remote_store').attr('src','');
	});

	$("#sorting").change(function () {
		location = '<?php echo $listing_url?>&' + $(this).val();
	});

	$('a.productcart, a.product_thumb, .ext_name a').click(function(){
		var product_id = $(this).parents('li.product-item').attr('data-product-id');
		if(!product_id) return false;
		window.open('<?php echo $remote_store_product_url;?>&rt=product/product&product_id=' + product_id, 'MPside');
		return false;
	});

	$('a.productcart').click(function(){
		var product_id = $(this).parents('li.product-item').attr('data-product-id');
		if(!product_id) return false;
		window.open('<?php echo $remote_store_product_url;?>&rt=checkout/cart&product_id=' + product_id, 'MPside');
		return false;
	});

	$('.ext_review a').click(function () {
		var product_id = $(this).parents('li.product-item').attr('data-product-id');
		if(!product_id) return false;
		window.open('<?php echo $remote_store_product_url;?>&rt=product/product/reviews&product_id=' + product_id, 'MPside');
		return false;
	});


	//tooltip for products
	$('.ext_thumbnail').tooltip({
  		selector: "a[rel=tooltip]",
  		//placement: 'auto', //Only bootstrap 3
  		animation: false,
        title: function() {
          var tooltipdata = $(this).parent().find('.tooltip-data');
          var img_src = tooltipdata.find('.load_ondemand').attr('data-src');
          tooltipdata.find('.load_ondemand').attr('src',img_src);
          return tooltipdata.html();
        }
	})
	
</script>