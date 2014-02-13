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
			</div>
		</div>
	</div>
	<div class="cbox_cl">
		<div class="cbox_cr">
			<div id="frame_wrapper" style=" display: none;">
				<div class="alert alert-info alert-dismissable">
				  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				  <strong>Back to Extensions List</strong>
				</div>
				<iframe
						id="remote_store"
						src=""
						frameBorder="0"
						height="100"
						width="100%"></iframe>
			</div>
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
					<td class="pull-left">
						<ul class="product-list pull-left">
							<?php
							foreach ($content['products']['rows'] as $product) {
								$item = array();

								$item['image'] = $product['cell']['thumb'];
								$item['title'] = $product['cell']['name'];
								$item['description'] = html_entity_decode($product['cell']['description'], ENT_QUOTES);

								$item['rating'] = ((int)$product['cell']['rating']) ? "<img src='" . $this->templateResource('/image/stars_' . (int)$product['cell']['rating'] . '.png') . "' alt='" . $product['stars'] . "' />" : '';

								$item['info_url'] = $product['href'];
								$item['buy_url'] = $product['add'];

								$item['price'] = $product['cell']['price'];
								$review = $button_write;
								if ($item['rating']) {
									$review = $item['rating'];
								}

								?>
								<li data-product-id="<?php echo $product['id'] ?>"
									class="product-item thumbnail pull-left">

									<div class="preview pull-left">
										<img src="<?php echo $item['image'] ?>" style="width: 120px;">
									</div>
									<div class="description pull-left">
										<a class="product-name" href="<?php echo $item['info_url'] ?>">
											<?php echo $item['title'] ?> <?php echo $product['model'] ? "(" . $product['model'] . ")" : '' ?>
										</a>

										<div class="product-description"><?php echo $item['description'] ?></div>
										<div>
											<div class="pull-left rating"><?php echo $item['rating'] ?></div>
											<div class="pull-right pricetag"><?php echo $product['cell']['addtocart']; ?></div>
										</div>
									</div>

								</li>
							<?php } ?>
						</ul>
						<div class="pull-left sorting"><?php echo $sorting; ?></div>
						<div class="pull-right pagination"><?php echo $pagination_bootstrap; ?></div>
					</td>
				</tr>
			</table>
		</div>
	</div>

<div class="cbox_bl">
	<div class="cbox_br">
		<div class="cbox_bc"></div>
	</div>
</div>
</div>
<script type="text/javascript">
	$("#sorting").change(function () {
		location = '<?php echo $listing_url?>&' + $(this).val();
	});

	$('li.product-item, .pricetag a.btn_standard').click(function () {
		var product_id = $(this).attr('data-product-id');
		var ifrwp = $('#frame_wrapper');
		var ifr = $('#remote_store');
		var hh = $('.extension-store-list').outerHeight();
		var ww = $('.extension-store-list').outerWidth();
		ifr.attr('height',hh).attr('width',ww);
		ifrwp.slideDown(1000);
		$('.extension-store-list').hide();
		ifr.attr('src','<?php echo $remote_store_product_url;?>&product_id=' + product_id);
		var ending_right     = ($(window).width() - (ifrwp.offset().left + ifrwp.outerWidth()));
		ifrwp.css('height',hh).css('width',ww).find('.alert').css('right',ending_right);
	});

	$('.pricetag a.btn_standard').click(function(){
		$(this).parents('li.product-item').click();
		return false;
	});

	$('#frame_wrapper .alert').click(function(){
		$('.extension-store-list').show();
		$('#frame_wrapper').slideUp(1000);
		$('#remote_store').attr('src','');
	});


</script>