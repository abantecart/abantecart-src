<h1 class="heading1">
	<span class="maintext"><?php echo $heading_title; ?></span>
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
	<div class="alert alert-error">
		<strong><?php echo $error; ?></strong>
	</div>
	<?php
	}
}
echo $form['form_open'];
?>
<div class="cart-info product-list container-fluid">
	<table class="table table-striped table-bordered">
		<tr>
			<th align="center"><?php echo $column_remove; ?></th>
			<th align="center"><?php echo $column_image; ?></th>
			<th align="left"><?php echo $column_name; ?></th>
			<th align="left"><?php echo $column_model; ?></th>
			<th align="center"><?php echo $column_quantity; ?></th>
			<th align="right"><?php echo $column_price; ?></th>
			<th align="right"><?php echo $column_total; ?></th>
		</tr>
		<?php $class = 'odd'; ?>
		<?php foreach ($products as $product) { ?>
		<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
		<tr class="<?php echo $class; ?>">
			<td align="center"><?php echo $product['remove']; ?></td>
			<td align="center"><a
					href="<?php echo $product['href']; ?>"><?php echo $product['thumb']['thumb_html']; ?></a></td>
			<td align="left"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
				<?php if (!$product['stock']) { ?>
					<span style="color: #FF0000; font-weight: bold;">***</span>
					<?php } ?>
				<div>
					<?php foreach ($product['option'] as $option) { ?>
					-
					<small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small><br/>
					<?php } ?>
				</div>
			</td>
			<td align="left"><?php echo $product['model']; ?></td>
			<td align="center"><?php echo $product['quantity']; ?></td>
			<td align="right"><?php echo $product['price']; ?></td>
			<td align="right"><?php echo $product['total']; ?></td>
		</tr>
		<?php } ?>
		<?php echo $this->getHookVar('list_more_product_last'); ?>
	</table>
</div>
<div class="container">
	<div class=" pull-right mb20">
		<?php echo $this->getHookVar('pre_top_cart_buttons'); ?>
		<?php if ($form['checkout']) { ?>

		<a href="<?php echo $checkout; ?>" id="cart_checkout" class="btn btn-orange pull-right"
		   title="<?php echo $button_checkout; ?>">
			<i class="icon-shopping-cart icon-white"></i>
			<?php echo $button_checkout; ?>
		</a>
		<?php } ?>
		<button title="<?php echo $button_update; ?>" class="btn pull-right mr10" id="cart_update"
				value="<?php echo $form['update']->form ?>" type="submit">
			<i class="icon-refresh"></i>
			<?php echo $button_update; ?>
		</button>
		<?php echo $this->getHookVar('post_top_cart_buttons'); ?>
	</div>
</div>
</form>

<?php if ($estimates_enabled || $coupon_status) { ?>
<div class="cart-info coupon-estimate container-fluid row-fluid">
	<?php if ($coupon_status) { ?>
		<div class=" pull-left coupon">
			<table class="table table-striped "><tr>
					<?php if ($coupon_status) { ?>
								<th align="center"><?php echo $text_coupon_codes ?></th>
								<?php } ?>
			</tr><tr>
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
	<?php }
	if ($estimates_enabled) { ?>
			<div class="estimate">
				<table class="table table-striped"><tr>
			<th align="center"><?php echo $text_estimate_shipping_tax ?></th></tr><tr>
			<td>
				<div class="registerbox">
					<?php echo $form_estimate['form_open']; ?>
					<div class="control-group">
						<label class="control-label"><?php echo $text_estimate_country; ?></label>

						<div class="controls">
							<?php echo $form_estimate['country_zones']; ?>
						</div>
					</div>
					<div class="form-inline">
						<label class="checkbox"><?php echo $text_estimate_postcode; ?></label>
						<?php echo $form_estimate['postcode']; ?>
						<button title="<?php echo $form_estimate['submit']->name; ?>" class="btn mr10"
								value="<?php echo $form_estimate['submit']->form ?>" type="submit">
							<i class="icon-check"></i>
							<?php echo $form_estimate['submit']->name; ?>
						</button>
					</div>
					<div class="form-inline shippings-offered mt20">
						<label class="control-label"><?php echo $text_estimate_shipments; ?></label>
						<label class="shipments"><?php echo $form_estimate['shippings']; ?></label>
					</div>
					</form>
				</div>
			</td>
			<?php } ?>
			</tr></table>
		</div>

</div>
<?php } ?>

<div class="container-fluid cart_total">
	<div class="row-fluid">
		<div class="span5 offset7 cart-info totals pull-right">
			<table class="table table-striped table-bordered">
				<?php foreach ($totals as $total) { ?>
				<tr>
					<td><span
							class="extra bold <?php if ($total[id] == 'total') echo 'totalamout'; ?>"><?php echo $total['title']; ?></span>
					</td>
					<td><span
							class="bold <?php if ($total[id] == 'total') echo 'totalamout'; ?>"><?php echo $total['text']; ?></span>
					</td>
				</tr>
				<?php } ?>
			</table>
			<?php echo $this->getHookVar('pre_cart_buttons'); ?>
			<?php if ($form['checkout']) { ?>
			<a href="<?php echo $checkout; ?>" id="cart_checkout" class="btn btn-orange pull-right"
			   title="<?php echo $button_checkout; ?>">
				<i class="icon-shopping-cart icon-white"></i>
				<?php echo $button_checkout; ?>
			</a>
			<?php } ?>

			<a href="<?php echo $continue; ?>" class="btn mr10" title="">
				<i class="icon-arrow-right"></i>
				<?php echo $text_continue_shopping ?>
			</a>
			<?php echo $this->getHookVar('post_cart_buttons'); ?>
		</div>
	</div>
</div>
<?php if ($estimates_enabled) { ?>
<script type="text/javascript"><!--

jQuery(function ($) {

	display_shippings();


	$('#estimate_country_zones').change(function () {
		//zone is changed, need to reset poscode
		$("#estimate input[name=\'postcode\']").val('')
		display_shippings();
	})

	$('#shippings').live("change", function () {
		display_totals();
	})

	$('#estimate').submit(function () {
		display_shippings();
		return false;
	});


});

function display_shippings() {
	var postcode = encodeURIComponent($("#estimate input[name=\'postcode\']").val());
	var country_id = encodeURIComponent($('#estimate_country').val());
	var zone_id = $('#estimate_country_zones').val();

	var replace_obj = $('.shippings-offered label.shipments');
	replace_obj;
	$.ajax({
		type:'POST',
		url:'index.php?rt=r/checkout/cart/shipping_methods',
		dataType:'json',
		data:'country_id=' + country_id + '&zone_id=' + zone_id + '&postcode=' + postcode,
		beforeSend:function () {
			$(replace_obj).html('<div class="progress progress-striped active" style="width: 170px;"><div class="bar" style="width: 100%;"></div></div>');
		},
		complete:function () {
		},
		success:function (data) {
			$(replace_obj).html('');
			if (data && data.selectbox) {
				$(replace_obj).show();
				$(replace_obj).css('visibility', 'visible');
				$(replace_obj).html(data.selectbox);
			}
			display_totals();
		}
	});

}

function display_totals() {
	var shipping_method = '';
	var coupon = encodeURIComponent($("#coupon input[name=\'coupon\']").val());
	shipping_method = encodeURIComponent($('#shippings :selected').val());

	$.ajax({
		type:'POST',
		url:'index.php?rt=r/checkout/cart/recalc_totals',
		dataType:'json',
		data:'shipping_method=' + shipping_method + '&coupon=' + coupon,
		beforeSend:function () {
			//$('.cart-info.totals table').html('<div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>');
		},
		complete:function () {
		},
		success:function (data) {
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
				$('.cart-info.totals table').html(html);
			}
		}
	});
}

function show_error(parent_element, message) {
	var html = '<div class="alert alert-error">' + message + '</div>';
	$(parent_element).before(html);
}

//--></script>
<?php } ?>