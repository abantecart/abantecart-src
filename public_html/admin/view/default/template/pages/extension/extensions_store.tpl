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
			<div id="frame_wrapper" class="cbox_cc" style=" display: none;">
				<div class="alert alert-info alert-dismissable">
				  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				  <strong>Back to Extensions List</strong>
				</div>
				<iframe
						id="remote_store"
						name="remote_store"
						src=""
						frameBorder="0"
						height="100"
						width="100%"></iframe>
			</div>
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
						<ul class="product-list">
							<?php
							foreach ($content['products']['rows'] as $product) {
								$item = array();

								$item['image'] = $product['cell']['main_image'];
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
								<li data-trigger="hover" data-placement="right" data-product-id="<?php echo $product['id'] ?>" class="product-item thumbnail pull-left">
									<div class="preview pull-left">
										<img src="<?php echo $item['image'] ?>" style="width: 100px;">
									</div>
									<div class="popover_content" style="display: none;" data-title="<?php echo $item['title'] ?>">
										<div class="preview pull-left">
											<img src="<?php echo $item['image'] ?>" style="width: 200px;">
										</div>
										<div class="description pull-left">
											<a class="product-name" href="<?php echo $item['info_url'] ?>">
												<?php echo $item['title'] ?> <?php echo $product['model'] ? "(" . $product['model'] . ")" : '' ?>
											</a>
											<div class="product-description"><?php echo $item['description'] ?></div>
										</div>
										<div class="summary">
											<div class="pull-left rating"><?php echo $item['rating'] ?></div>
											<?php if( $product['cell']['review_count'] ){ ?>
											<div class="pull-left reviews"><?php echo $product['cell']['review_count'] ?> review(s)</div>
											<?php } ?>
											<div class="pull-right pricetag"><?php echo $product['cell']['price']; ?></div>
										</div>
									</div>

								</li>
							<?php } ?>
						</ul>
						<div class="pagination" >
							<div class="sorting" style=""><?php echo $sorting; ?></div>
							<div class="pages"  style="display: inline-block; text-align: left;"><?php echo $pagination_bootstrap; ?></div>
						</div>
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
	$("#sorting").change(function () {
		location = '<?php echo $listing_url?>&' + $(this).val();
	});

	$('li.product-item, .pricetag a.btn_standard').click(function () {
		var product_id = $(this).attr('data-product-id');
		if(!product_id) return false;

		window.open('<?php echo $remote_store_product_url;?>&product_id=' + product_id,
					'MPside');
		//,					'width='+($(window).width()-100)+', height='+($(window).height()-100)+', toolbars=no, resizable=yes, scrollbars=yes');
	});

	//show popover

	$('li.product-item').popover({
		html: true,
		content: function(){
			return $(this).children('.popover_content').html();
		},
		title: 	function(){ return $(this).children('.popover_content').attr('data-title'); },
		delay: {'hide': 700}
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