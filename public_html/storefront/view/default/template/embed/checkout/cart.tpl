<?php echo $head; ?>
<h1 class="heading1">
	<span class="maintext"><i class="fa fa-shopping-cart"></i> <?php echo $heading_title; ?></span>
	<?php if ($weight) { ?>
		<span class="subtext">(<?php echo $weight; ?>)</span>
	<?php } ?>
</h1>

<?php if ($success) { ?>
	<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<?php echo $success; ?>
	</div>
<?php } ?>

<?php if (count($error_warning) > 0) {
	foreach ($error_warning as $error) {
		?>
		<div class="alert alert-error alert-danger">
			<strong><?php echo $error; ?></strong>
		</div>
	<?php
	}
}
echo $form['form_open'];
?>
<div class="contentpanel">
	<div class="container-fluid cart-info product-list">
		<table class="table table-striped table-bordered">
			<tr>
				<th class="align_center"><?php echo $column_image; ?></th>
				<th class="align_left"><?php echo $column_name; ?></th>
				<th class="align_left"><?php echo $column_model; ?></th>
				<th class="align_right"><?php echo $column_price; ?></th>
				<th class="align_center"><?php echo $column_quantity; ?></th>
				<th class="align_right"><?php echo $column_total; ?></th>
				<th class="align_center"><?php echo $column_remove; ?></th>
			</tr>
			<?php foreach ($products as $product) { ?>
				<tr>
					<td class="align_center">
						<a href="<?php echo $product['href']; ?>"><?php echo $product['thumb']['thumb_html']; ?></a>
					</td>
					<td class="align_left"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
						<?php if (!$product['stock']) { ?>
							<span style="color: #FF0000; font-weight: bold;">***</span>
						<?php } ?>
						<div>
							<?php foreach ($product['option'] as $option) { ?>
								-
								<small title="<?php echo $option['title']?>"><?php echo $option['name']; ?> <?php echo $option['value']; ?></small><br/>
							<?php } ?>
						</div>
					</td>
					<td class="align_left"><?php echo $product['model']; ?></td>
					<td class="align_right"><?php echo $product['price']; ?></td>
					<td class="align_center">
						<div class="input-group input-group-sm"><?php echo $product['quantity']; ?></div>
					</td>
					<td class="align_right"><?php echo $product['total']; ?></td>
					<td class="align_center">
						<a href="<?php echo $product['remove_url']; ?>" class="btn btn-sm btn-default"><i class="fa fa-trash-o fa-fw"></i></a>
					</td>
				</tr>
			<?php } ?>
			<?php echo $this->getHookVar('list_more_product_last'); ?>
		</table>

		<div class="pull-right mb20">
			<?php echo $this->getHookVar('pre_top_cart_buttons'); ?>
			<?php if ($form['checkout']) { ?>
				<a href="#" onclick="save_and_checkout('<?php echo $checkout_rt; ?>'); return false;" id="cart_checkout1" class="btn btn-orange pull-right" title="<?php echo $button_checkout; ?>">
					<i class="fa fa-shopping-cart"></i>
					<?php echo $button_checkout; ?>
				</a>
			<?php } ?>
			<button title="<?php echo $button_update; ?>" class="btn btn-default pull-right mr10" id="cart_update"
					value="<?php echo $form['update']->form ?>" type="submit">
				<i class="fa fa-refresh"></i>
				<?php echo $button_update; ?>
			</button>
			<?php echo $this->getHookVar('post_top_cart_buttons'); ?>
		</div>
	</div>
	</form>

<?php if ($estimates_enabled || $coupon_status) {
	$pull_side = 'pull-right';
	if ($estimates_enabled) {
		$pull_side = 'pull-left';
	}
	?>
	<div class="cart-info coupon-estimate container-fluid">
		<?php if ($coupon_status) { ?>
			<div class="<?php echo $pull_side; ?> coupon">
				<table class="table table-striped ">
					<tr>
						<?php if ($coupon_status) { ?>
							<th class="align_center"><?php echo $text_coupon_codes ?></th>
						<?php } ?>
					</tr>
					<tr>
						<td>
							<?php
							if ($coupon_status) {
								echo $coupon_form;
							}
							?>
						</td>
					</tr>
				</table>
			</div>
		<?php
		}
		if ($estimates_enabled) { ?>
			<div class="pull-right estimate">
				<table class="table table-striped">
					<tr>
						<th class="align_center"><?php echo $text_estimate_shipping_tax ?></th>
					</tr>
					<tr>
						<td>
							<div class="registerbox form-horizontal">
								<?php echo $form_estimate['form_open']; ?>
								<div class="form-group">
									<label class="control-label col-sm-4"><?php echo $text_estimate_country; ?></label>
									<div class="input-group col-sm-8">
									<?php echo $form_estimate['country_zones']; ?>
									</div>
								</div>																			
								
								<div class="form-group">
									<label class="checkbox col-sm-4"><?php echo $text_estimate_postcode; ?></label>
						    		<div class="input-group col-sm-6">
						    		<?php echo $form_estimate['postcode']; ?>
						    		<span class="input-group-btn">
						    			<button title="<?php echo $form_estimate['submit']->name; ?>" class="btn btn-default mr10"
						    				value="<?php echo $form_estimate['submit']->form ?>" type="submit">
						    			<i class="fa fa-check"></i>
						    			<?php echo $form_estimate['submit']->name; ?>
						    			</button>
						    		</span>
						    		</div>
						    	</div>	
								
								<div class="shippings-offered form-group">
									<label class="control-label col-sm-4"><?php echo $text_estimate_shipments; ?></label>
									<div class="shipments input-group col-sm-8">
										<?php echo $form_estimate['shippings']; ?>
									</div>
								</div>
								</form>
							</div>
						</td>
					</tr>
				</table>
			</div>
		<?php } ?>

	</div>
<?php } ?>

	<div class="container-fluid cart_total">
	    <div class="col-md-6 cart-info totals pull-right table-responsive">
	    	<table id="totals_table" class="table table-striped table-bordered">
	    		<?php /* Total now loaded with ajax. ?>
	    		<?php foreach ($totals as $total) { ?>
	    			<tr>
	    				<td><span class="extra bold <?php if ($total['id'] == 'total') echo 'totalamout'; ?>"><?php echo $total['title']; ?></span></td>
	    				<td><span class="bold <?php if ($total['id'] == 'total') echo 'totalamout'; ?>"><?php echo $total['text']; ?></span></td>
	    			</tr>
	    		<?php } ?>
	    		<?php */ ?>
	    	</table>
	    	
	    	<?php echo $this->getHookVar('pre_cart_buttons'); ?>

	    	<a href="<?php echo $continue; ?>" class="btn btn-default mr10  mb10" title="">
	    		<i class="fa fa-arrow-right"></i>
	    		<?php echo $text_continue_shopping ?>
	    	</a>

	    	<?php if ($form['checkout']) { ?>
	    		<a href="#" onclick="save_and_checkout('<?php echo $checkout_rt; ?>'); return false;" id="cart_checkout2" class="btn btn-orange pull-right" title="<?php echo $button_checkout; ?>">
	    			<i class="fa fa-shopping-cart"></i>
	    			<?php echo $button_checkout; ?>
	    		</a>
	    	<?php } ?>
	
	    	<?php echo $this->getHookVar('post_cart_buttons'); ?>
	    </div>
	</div>
</div>
<script type="text/javascript"><!--

		jQuery(function ($) {

			display_shippings();

			$(document).on("change", '#estimate_country_zones', function () {
				//zone is changed, need to reset poscode
				$("#estimate input[name=\'postcode\']").val('')
				display_shippings();
			})

			$(document).on("change", '#shippings', function () {
				display_totals();
			})

			$('#estimate').submit(function () {
				display_shippings();
				return false;
			});
			
		});

		var save_and_checkout = function(url) { 
			//first update cart and then follow the next step
			var input = $("<input>").attr("type", "hidden").attr("name", "next_step").val(url);
			$('#cart').append($(input));
			$('#cart').submit();
		}		
		
		var  display_shippings = function() {
			var postcode = encodeURIComponent($("#estimate input[name=\'postcode\']").val());
			var country_id = encodeURIComponent($('#estimate_country').val());
			var zone_id = $('#estimate_country_zones').val();

			var replace_obj = $('.shippings-offered .shipments');
			replace_obj;
			$.ajax({
				type: 'POST',
				url: '<?php echo $this->html->getURL('r/checkout/cart/change_zone_get_shipping_methods'); ?>',
				dataType: 'json',
				data: 'country_id=' + country_id + '&zone_id=' + zone_id + '&postcode=' + postcode,
				beforeSend: function () {
					$(replace_obj).html('<div class="progress progress-striped active" style="width: 170px;"><div class="bar" style="width: 100%;"></div></div>');
				},
				complete: function () {
				},
				success: function (data) {
					$(replace_obj).html('');
					$('.shippings-offered label.control-label').hide();
					if (data && data.selectbox) {
						if (data.selectbox != '') {
							$(replace_obj).show();
							$('.shippings-offered label.control-label').show();
							$(replace_obj).css('visibility', 'visible');
							$(replace_obj).html(data.selectbox);
						}
					}
					display_totals();
				}
			});

		}

		//load total with AJAX call
		var display_totals = function () {
			var shipping_method = '';
			var coupon = encodeURIComponent($("#coupon input[name=\'coupon\']").val());
			shipping_method = encodeURIComponent($('#shippings :selected').val());
			if (shipping_method == 'undefined') {
				shipping_method = '';
			}
			$.ajax({
				type: 'POST',
				url: '<?php echo $this->html->getURL('r/checkout/cart/recalc_totals');?>',
				dataType: 'json',
				data: 'shipping_method=' + shipping_method + '&coupon=' + coupon,
				beforeSend: function () {
					var html = '';
					html += '<tr>';
					html += '<td><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div></td>';
					html += '</tr>';
					$('.cart-info.totals table#totals_table').html(html);
				},
				complete: function () {
				},
				success: function (data) {
					if (data && data.totals.length) {
						var html = '';
						for (var i = 0; i < data.totals.length; i++) {
							var grand_total = '';
							if (data.totals[i].id == 'total') {
								grand_total = 'totalamout';
							}
							html += '<tr>';
							html += '<td><span class="extra bold ' + grand_total + '">' + data.totals[i].title + '</span></td>';
							html += '<td><span class="bold ' + grand_total + '">' + data.totals[i].text + '</span></td>';
							html += '</tr>';
						}
						$('.cart-info.totals table#totals_table').html(html);
					}
				}
			});
		}

		var  show_error = function(parent_element, message) {
			var html = '<div class="alert alert-error alert-danger">' + message + '</div>';
			$(parent_element).before(html);
		}

//--></script>
<?php echo $footer; ?>