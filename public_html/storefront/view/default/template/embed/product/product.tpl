<?php echo $head; ?>

<?php if ($error) { ?>
	<div class="alert alert-error alert-danger">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong><?php echo is_array($error) ? implode('<br>', $error) : $error; ?></strong>
	</div>
<?php } ?>

<div id="product_details" class="container-fluid">
		<!-- Product Details-->
		<div class="row">
			<!-- Left Image-->
			<div class="col-md-6 col-sm-6 text-center">
			<ul class="thumbnails mainimage smallimage">
			<?php if (sizeof($images) > 1) {
			    foreach ($images as $image) { ?>
			    <li class="producthtumb">
			        <?php
			        if ($image['origin'] != 'external') {
			        ?>
				    	<a href="<?php echo $image['main_url']; ?>" data-standard="<?php echo $image['thumb2_url']; ?>">
			        	<img src="<?php echo $image['thumb_url']; ?>" alt="<?php echo $image['title']; ?>" title="<?php echo $image['title']; ?>" />
				    	</a>
			        <?php } ?>
			    </li>
			    <?php
			    }
			} ?>
			</ul>

			<div class="mainimage bigimage easyzoom easyzoom--overlay easyzoom--with-thumbnails">
			<?php if (sizeof($images) > 0) {
				//NOTE: ZOOM is not supported for embeded image tags
				if ($image_main['origin'] == 'external') {
				?>
				    <a class="html_with_image">
				    <?php echo $image_main['main_html'];	?>								
				    </a>
				<?php
				} else {
				    $image_url = $image_main['main_url'];
				    $thumb_url = $image_main['thumb_url'];
				?>
				    <a class="local_image" href="<?php echo $image_url; ?>" target="_image" title="<?php echo $image_main['title']; ?>">
				    	<img src="<?php echo $thumb_url; ?>" alt="<?php echo $image['title']; ?>" title="<?php echo $image['title']; ?>" />
				    <i class="fa fa-arrows"></i>
				    </a>
				<?php } ?>
				<?php
				} 
				?>
			</div>
			</div>
			
			<!-- Right Details-->
			<div class="col-md-6 col-sm-6">
				<div class="row">
					<div class="col-sm-12">
						<h1 class="productname"><span class="bgnone"><?php echo $heading_title; ?></span></h1>

						<div class="productprice">
							<?php

							if ($display_price) { ?>
								<div class="productpageprice jumbotron">
									<?php if ($special) { ?>
										<div class="productfilneprice">
											<span class="spiral"></span><?php echo $special; ?></div>
										<span class="productpageoldprice"><?php echo $price; ?></span>
									<?php } else { ?>
										<span class="productfilneprice"></span><span
												class="spiral"></span><?php echo $price; ?>
									<?php } ?>
								</div>
							<?php }

							if ($average) { ?>
								<ul class="rate">
									<?php
									#Show stars based on avarage rating
									for ($i = 1; $i <= 5; $i++) {
										if ($i <= $average) {
											echo '<li class="on"></li>';
										} else {
											echo '<li class="off"></li>';
										}
									}
									?>
								</ul>
							<?php } ?>
						</div>

						<div class="quantitybox">
							<?php if ($display_price) { ?>
								<?php echo $form['form_open']; ?>
								<fieldset>
									<?php if ($options) { ?>
										<?php foreach ($options as $option) { ?>
											<div class="form-group">
												<?php if ($option['html']->type != 'hidden') { ?>
												<label class="control-label"><?php echo $option['name']; ?></label>
												<?php } ?>
												<div class="input-group col-sm-10">
													<?php echo $option['html']; ?>
												</div>
											</div>
										<?php } ?>
									<?php } ?>

									<?php echo $this->getHookVar('extended_product_options'); ?>

									<?php if ($discounts) { ?>
										<div class="form-group">
											<label class="control-label"><?php echo $text_discount; ?></label>
											<table class="table table-striped">
												<thead>
													<th><?php echo $text_order_quantity; ?></th>
													<th><?php echo $text_price_per_item; ?></th>
												</thead>
											<?php foreach ($discounts as $discount) { ?>
												<tr>
													<td><?php echo $discount['quantity']; ?></td>
													<td><?php echo $discount['price']; ?></td>
												</tr>
											<?php } ?>
											</table>
										</div>
									<?php } ?>
									<?php if(!$product_info['call_to_order']){ ?>
									<div class="form-group mt20">
										<div class="input-group col-sm-4">
											<span class="input-group-addon"><?php echo $text_qty; ?></span>
											<?php echo $form['minimum']; ?>
										</div>
										<?php if ($minimum > 1) { ?>
											<div class="input-group "><?php echo $text_minimum; ?></div>
										<?php } ?>
										<?php if ($maximum > 0) { ?>
											<div class="input-group "><?php echo $text_maximum; ?></div>
										<?php } ?>
									</div>

									<div class="form-group mt20 mb10 total-price-holder">
										<label class="control-label"><?php echo $text_total_price; ?>
											<span class="total-price"></span>
										</label>
									</div>
									<?php }?>

									<?php if($product_info['free_shipping'] && $product_info['shipping_price'] <= 0 ) { ?>
									<div class="alert alert-info mt10 mb10 free-shipping-holder">
										<label class="control-label"><?php echo $text_free_shipping; ?></label>
									</div>
									<?php } ?>

									<div>
										<?php echo $form['product_id'] . $form['redirect']; ?>
									</div>

									<div class="mt20 ">
										<?php if(!$product_info['call_to_order']){ ?>
										<?php if ($track_stock && !$in_stock) { ?>
										<ul class="productpagecart">
											<li><span class="nostock"><?php echo $stock; ?></span></li>
										</ul>
										<?php } else { ?>
										<ul class="productpagecart">
											<li><a href="#" onclick="$(this).closest('form').submit(); return false;"
												   class="cart"><?php echo $button_add_to_cart; ?></a></li>
										</ul>
										<?php } ?>
										<?php } else { ?>
											<ul class="productpagecart call_to_order">
												<li><a href="#" class="call_to_order"><i class="fa fa-phone"></i>&nbsp;&nbsp;<?php echo $text_call_to_order; ?></a></li>
											</ul>
										<?php } ?>										
										<a class="productprint btn btn-large" href="javascript:window.print();">
											<i class="fa fa-print"></i> <?php echo $button_print; ?>
										</a>
										<?php echo $this->getHookVar('buttons'); ?>
									</div>

									<?php 
										if ($in_wishlist) { 
											$whislist = ' style="display: none;" ';
											$nowhislist = '';
										} else {
											$nowhislist = ' style="display: none;" ';
											$whislist = '';
										} 
									?>
									<?php if ($is_customer) { ?>
									<div class="wishlist">
										<a class="wishlist_remove btn btn-large" href="#" onclick="wishlist_remove(); return false;" <?php echo $nowhislist; ?>>
											<i class="fa fa-trash-o"></i> <?php echo $button_remove_wishlist; ?>
										</a>
										<a class="wishlist_add btn btn-large" href="#" onclick="wishlist_add(); return false;" <?php echo $whislist; ?>>
											<i class="fa fa-plus-square"></i> <?php echo $button_add_wishlist; ?>
										</a>
									</div>
									<?php } ?>
								</fieldset>
								</form>
							<?php } elseif(!$product_info['call_to_order']) { ?>
								<div class="form-group">
									<label class="control-label">
										<?php echo $text_login_view_price; ?>
									</label>
								</div>
							<?php } ?>

						</div>
					</div>
				</div>
			</div>
		</div>
</div>

<!-- Product Description tab & comments-->
<div id="productdesc" class="container-fluid">
	<div class="row">
		<div class="col-md-12 productdesc">
			<ul class="nav nav-tabs" id="myTab">
				<li class="active"><a href="#description"><?php echo $tab_description; ?></a></li>
				<?php if ($review_status) { ?>
					<li><a href="#review"><?php echo $tab_review; ?></a></li>
				<?php } ?>
				<?php if ($tags) { ?>
					<li><a href="#producttag"><?php echo $text_tags; ?></a></li>
				<?php } ?>
				<?php if ($related_products) { ?>
					<li><a href="#relatedproducts"><?php echo $tab_related; ?> (<?php echo count($related_products); ?>)</a></li>
				<?php } ?>
				<?php if ($downloads) { ?>
					<li><a href="#productdownloads"><?php echo $tab_downloads; ?></a></li>
				<?php } ?>
				<?php echo $this->getHookVar('product_features_tab'); ?>
			</ul>
			<div class="tab-content">

				<div class="tab-pane active" id="description">
					<?php echo $description; ?>

					<ul class="productinfo">
						<?php if ($stock) { ?>
							<li>
								<span class="productinfoleft"><?php echo $text_availability; ?></span> <?php echo $stock; ?>
							</li>
						<?php } ?>
						<?php if ($model) { ?>
							<li><span class="productinfoleft"><?php echo $text_model; ?></span> <?php echo $model; ?>
							</li>
						<?php } ?>
						<?php if ($manufacturer) { ?>
							<li>
								<span class="productinfoleft"><?php echo $text_manufacturer; ?></span>
								<a href="<?php echo $manufacturers; ?>">
									<?php if ($manufacturer_icon) { ?>
										<img alt="<?php echo $manufacturer; ?>"
										     src="<?php echo $manufacturer_icon; ?>"
											 title="<?php echo $manufacturer; ?>"
											 style="width: <?php echo $this->config->get('config_image_grid_width');?>px;"/>
									<?php
									} else {
										echo $manufacturer;
									}  ?>
								</a>
							</li>
						<?php } ?>
					</ul>

				</div>

				<?php if ($review_status) { ?>
					<div class="tab-pane" id="review">
						<div id="current_reviews" class="mb20"></div>
						<div class="heading" id="review_title"><h4><?php echo $text_write; ?></h4></div>
						<div class="content">
							<fieldset>
								<div class="form-group">
									<div class="form-inline">
										<label class="control-label col-md-3 pull-left"><?php echo $entry_rating; ?> <span
													class="red">*</span></label>
										<?php echo $rating_element; ?>
									</div>
								</div>
								<div class="form-group mt40">
									<div class="form-inline">
										<label class="control-label col-md-3"><?php echo $entry_name; ?> <span class="red">*</span></label>
										<?php echo $review_name; ?>
									</div>
								</div>
								<div class="form-group">
									<div class="form-inline">
										<label class="control-label col-md-3"><?php echo $entry_review; ?> <span
													class="red">*</span></label>
										<?php echo $review_text; ?>
									</div>
									<div class="input-group"><?php echo $text_note; ?></div>
								</div>
								<?php if ($review_recaptcha) { ?>
								<div class="clear form-group">
								    <div class="form-inline col-md-6 col-md-offset-1 col-sm-6">
								    	<?php echo $review_recaptcha; ?>
								    </div>	
								    <div class="form-inline col-md-5 col-sm-6">								    
								    	<?php echo $review_button; ?>
								    </div>
								</div>
								<?php } else { ?>
								<div class="clear form-group">
									<label class="control-label"><?php echo $entry_captcha; ?> <span
							    				class="red">*</span></label>
	
							    	<div class="form-inline">
							    		<label class="control-label col-md-3">
							    			<img src="<?php echo $captcha_url;?>" id="captcha_img" alt=""/>
							    		</label>
							    		<?php echo $review_captcha; ?>
							    		&nbsp;&nbsp;<?php echo $review_button; ?>
							    	</div>
								</div>
								<?php } ?>
							</fieldset>
						</div>
					</div>
				<?php } ?>

				<?php if ($tags) { ?>
					<div class="tab-pane" id="producttag">
						<ul class="tags">
							<?php foreach ($tags as $tag) { ?>
							<li><a href="<?php echo $tag['href']; ?>"><i class="fa fa-tag"></i><?php echo $tag['tag']; ?></a></li>
								<?php } ?>
						</ul>
					</div>
				<?php } ?>

				<?php if ($related_products) { ?>
					<div class="tab-pane" id="relatedproducts">
						<ul class="row side_prd_list">
						<?php foreach ($related_products as $related_product) {
								$item['rating'] = ($related_product['rating']) ? "<img src='" . $this->templateResource('/image/stars_' . $related_product['rating'] . '.png') . "' alt='" . $related_product['stars'] . "' />" : '';
								if (!$display_price) {
									$related_product['price'] = $related_product['special'] = '';
								}
							?>
								<li class="col-md-3 col-sm-4 col-xs-6 related_product">
									<a href="<?php echo $related_product['href']; ?>"><?php echo $related_product['image']['thumb_html'] ?></a>
									<a class="productname"
									   href="<?php echo $related_product['href']; ?>"><?php echo $related_product['name']; ?></a>
									<span class="procategory"><?php echo $item['rating'] ?></span>

									<div class="price">
										<?php if ($related_product['special']) { ?>
											<span class="pricenew"><?php echo $related_product['special'] ?></span>
											<span class="priceold"><?php echo $related_product['price'] ?></span>
										<?php } else { ?>
											<span class="oneprice"><?php echo $related_product['price'] ?></span>
										<?php } ?>
									</div>
								</li>
						<?php } ?>
						</ul>
					</div>
				<?php } ?>

				<?php if ($downloads) { ?>
					<div class="tab-pane" id="productdownloads">
						<ul class="list-group">
							<?php foreach ($downloads as $download) { ?>
							<li class="list-group-item">
								<a class="pull-right btn btn-default" href="<?php echo $download['button']->href; ?>"><i class="fa fa-download"></i> <?php echo $download['button']->text; ?></a>
								<div><?php echo $download['name']; ?><div class="download-list-attributes">
								<?php foreach($download['attributes'] as $name=>$value){
									echo '<small>- '.$name.': '.(is_array($value) ? implode(' ',$value) : $value).'</small>';
									}?></div>
								</div>
							</li>
							<?php } ?>
						</ul>
					</div>
				<?php } ?>

				<?php echo $this->getHookVar('product_features'); ?>

			</div>
		</div>
	</div>
</div>

<script type="text/javascript"><!--

	var orig_imgs = $('ul.bigimage').html();
	var orig_thumbs = $('ul.smallimage').html();

	jQuery(function ($) {

		start_easyzoom();

		//if have product options, load select option images 
		var $select = $('input[name^=\'option\'], select[name^=\'option\']'); 
		if ($select.length) {
			//if no images for options are present, main product images will be used. 
			//if atleast one image is present in the option, main images will be replaced.
			load_option_images($select.val());
		}

		display_total_price();

		$('#current_reviews .pagination a').on('click', function () {
			$('#current_reviews').slideUp('slow');
			$('#current_reviews').load(this.href);
			$('#current_reviews').slideDown('slow');
			return false;
		});

		reload_review('<?php echo $product_review_url; ?>');
	});

	$('#product_add_to_cart').click(function () {
		$('#product').submit();
	});
	$('#review_submit').click(function () {
		review();
	})
	
	//process clicks in review pagination
	$('#current_reviews').on('click', '.pagination a', function () {
		reload_review($(this).attr('href'));
		return false;
	})

	/* Process images for product options */
	$('input[name^=\'option\'], select[name^=\'option\']').change(function () {
		load_option_images($(this).val());
		display_total_price();
	});

	$('input[name=quantity]').keyup(function () {
		display_total_price();
	});

	function start_easyzoom() {
		// Instantiate EasyZoom instances
		var $easyzoom = $('.easyzoom').easyZoom();
		
		// Get an instance API
		var api1 = $easyzoom.filter('.easyzoom--with-thumbnails').data('easyZoom');
		//clean and reload esisting events 
		api1.teardown();
		api1._init();
		
		// Setup thumbnails
		$('.thumbnails').on('click', 'a', function(e) {
		   var $this = $(this);
		   e.preventDefault();
		   // Use EasyZoom's `swap` method
		   api1.swap($this.data('standard'), $this.attr('href'));
		});
	}

	function load_option_images( attribute_value_id ) {
		$.ajax({
			type: 'POST',
			url: '<?php echo $option_resources_url; ?>&attribute_value_id=' + attribute_value_id,
			dataType: 'json',
			success: function (data) {
				var html1 = '';
				var html2 = '';
				
				if (data.main) {
					if (data.main.origin == 'external') {
						html1 = '<a class="html_with_image">';
						html1 += data.main.main_html + '</a>';						
					} else {
				    	html1 = '<a href="' + data.main.main_url + '">';
				    	html1 += '<img src="' + data.main.thumb_url + '" />';
				    	html1 += '<i class="fa fa-arrows"></i></a>';
				    }			
				}				
				if (data.images) {
					for (img in data.images) {
						html2 += '<li class="producthtumb">';
						var img_url = data.images[img].main_url;
						var tmb_url = data.images[img].thumb_url;
						var tmb2_url = data.images[img].thumb2_url;
						if (data.images[img].origin != 'external') {
							html2 += '<a href="'+img_url+'" data-standard="'+tmb2_url+'"><img src="' + tmb_url + '" alt="' + data.images[img].title + '" title="' + data.images[img].title + '" /></a>';
						}
						html2 += '</li>';
					}
				} else {
					html1 = orig_imgs;
					html2 = orig_thumbs;
				}
				$('div.bigimage').html(html1);
				$('ul.smallimage').html(html2);
				start_easyzoom();
			}
		});
	}

	function display_total_price() {

		$.ajax({
			type: 'POST',
			url: '<?php echo $calc_total_url;?>',
			dataType: 'json',
			data: $("#product").serialize(),

			success: function (data) {
				if (data.total) {
					$('.total-price-holder').show();
					$('.total-price-holder').css('visibility', 'visible');
					$('.total-price').html(data.total);
				}
			}
		});

	}

	function reload_review( url) {
		$('#current_reviews').load(url);
	}

	function review() {
		var dismiss = '<button type="button" class="close" data-dismiss="alert">&times;</button>';

		<?php if ($review_recaptcha) { ?>
		var captcha = '&g-recaptcha-response=' + encodeURIComponent($('textarea[name=\'g-recaptcha-response\']').val());
		<?php } else { ?>
		var captcha = '&captcha=' + encodeURIComponent($('input[name=\'captcha\']').val());
		<?php } ?>

		$.ajax({
			type: 'POST',
			url: '<?php echo $product_review_write_url;?>',
			dataType: 'json',
			data: 'name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&text=' + encodeURIComponent($('textarea[name=\'text\']').val()) + '&rating=' + encodeURIComponent($('input[name=\'rating\']:checked').val() ? $('input[name=\'rating\']:checked').val() : '') + captcha,
			beforeSend: function () {
				$('.success, .warning').remove();
				$('#review_button').attr('disabled', 'disabled');
				$('#review_title').after('<div class="wait"><i class="fa fa-spinner fa-spin"></i> <?php echo $text_wait; ?></div>');
			},
			complete: function () {
				$('#review_button').attr('disabled', '');
				$('.wait').remove();
				<?php if ($review_recaptcha) { ?>
    				grecaptcha.reset();
    			<?php } ?>
			},
            error: function (jqXHR, exception) {
            	var text = jqXHR.statusText + ": " + jqXHR.responseText;
				$('#review .alert').remove();
				$('#review_title').after('<div class="alert alert-error alert-danger">' + dismiss + text + '</div>');
			},
			success: function (data) {
				if (data.error) {
					$('#review .alert').remove();
					$('#review_title').after('<div class="alert alert-error alert-danger">' + dismiss + data.error + '</div>');
				} else {
					$('#review .alert').remove();
					$('#review_title').after('<div class="alert alert-success">' + dismiss + data.success + '</div>');

					$('input[name=\'name\']').val('');
					$('textarea[name=\'text\']').val('');
					$('input[name=\'rating\']:checked').attr('checked', '');
					$('input[name=\'captcha\']').val('');
				}
				$('img#captcha_img').attr('src', $('img#captcha_img').attr('src') + '&' + Math.random());
			}
		});
	}

	function wishlist_add() {
		var dismiss = '<button type="button" class="close" data-dismiss="alert">&times;</button>';
		$.ajax({
			type: 'POST',
			url: '<?php echo $product_wishlist_add_url; ?>',
			dataType: 'json',
			beforeSend: function () {
				$('.success, .warning').remove();
				$('.wishlist_add').hide();
				$('.wishlist').after('<div class="wait"><i class="fa fa-spinner fa-spin"></i> <?php echo $text_wait; ?></div>');
			},
			complete: function () {
				$('.wait').remove();
			},
            error: function (jqXHR, exception) {
            	var text = jqXHR.statusText + ": " + jqXHR.responseText;
				$('.wishlist .alert').remove();
				$('.wishlist').after('<div class="alert alert-error alert-danger">' + dismiss + text + '</div>');
				$('.wishlist_add').show();
			},
			success: function (data) {
				if (data.error) {
					$('.wishlist .alert').remove();
					$('.wishlist').after('<div class="alert alert-error alert-danger">' + dismiss + data.error + '</div>');
					$('.wishlist_add').show();
				} else {
					$('.wishlist .alert').remove();
					//$('.wishlist').after('<div class="alert alert-success">' + dismiss + data.success + '</div>');
					$('.wishlist_remove').show();
				}
			}
		});
	}

	function wishlist_remove() {
		var dismiss = '<button type="button" class="close" data-dismiss="alert">&times;</button>';
		$.ajax({
			type: 'POST',
			url: '<?php echo $product_wishlist_remove_url; ?>',
			dataType: 'json',
			beforeSend: function () {
				$('.success, .warning').remove();
				$('.wishlist_remove').hide();
				$('.wishlist').after('<div class="wait"><i class="fa fa-spinner fa-spin"></i> <?php echo $text_wait; ?></div>');
			},
			complete: function () {
				$('.wait').remove();
			},
            error: function (jqXHR, exception) {
            	var text = jqXHR.statusText + ": " + jqXHR.responseText;
				$('.wishlist .alert').remove();
				$('.wishlist').after('<div class="alert alert-error alert-danger">' + dismiss + text + '</div>');
				$('.wishlist_remove').show();
			},
			success: function (data) {
				if (data.error) {
					$('.wishlist .alert').remove();
					$('.wishlist').after('<div class="alert alert-error alert-danger">' + dismiss + data.error + '</div>');
					$('.wishlist_remove').show();
				} else {
					$('.wishlist .alert').remove();
					//$('.wishlist').after('<div class="alert alert-success">' + dismiss + data.success + '</div>');
					$('.wishlist_add').show();
				}
			}
		});
	}

//--></script>
<?php echo $footer; ?>	