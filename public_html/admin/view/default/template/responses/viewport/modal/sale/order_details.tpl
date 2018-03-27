<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<a aria-hidden="true" class="btn btn-default" type="button" href="" target="_new"><i class="fa fa-arrow-right fa-fw"></i><?php echo $text_more_new; ?></a>
	<a aria-hidden="true" class="btn btn-default" type="button" href=""><i class="fa fa-arrow-down fa-fw"></i><?php echo $text_more_current; ?></a>
	<h4 class="modal-title"><?php echo $heading_title; ?></h4>
</div>

<div id="content" class="panel panel-default">
	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
	<label class="h4 heading"><?php echo $form_title; ?></label>

	<div class="container-fluid">
	<div class="col-sm-6 col-xs-12">
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_order_id; ?></label>
			<div class="input-group afield col-sm-7">
			<p class="form-control-static"><?php echo $order_id; ?></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_invoice_id; ?></label>
			<div class="input-group afield col-sm-7"><p class="form-control-static">
				<?php if ($invoice_id) {
					echo $invoice_id;
				} else {
					echo '--';
				} ?>
			</p></div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_customer; ?></label>
			<div class="input-group afield col-sm-7">
				<p class="form-control-static"><?php echo $firstname.' '.$lastname; ?></p>
			</div>
		</div>
		<?php if ($customer_group) { ?>
			<div class="form-group">
				<label class="control-label col-sm-5"><?php echo $entry_customer_group; ?></label>
				<div class="input-group afield col-sm-7">
				<p class="form-control-static"><?php echo $customer_group; ?></p>
				</div>
			</div>
		<?php } ?>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_email; ?></label>
			<div class="input-group afield col-sm-7"><?php echo $email; ?></div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_telephone; ?></label>
			<div class="input-group afield col-sm-7"><?php echo $telephone; ?></div>
		</div>
		<?php if ($fax) { ?>
			<div class="form-group">
				<label class="control-label col-sm-5"><?php echo $entry_fax; ?></label>
				<div class="input-group afield col-sm-7"><?php echo $fax; ?></div>
			</div>
		<?php }
		if ($im) { ?>
			<div class="form-group">
				<label class="control-label col-sm-5"><?php echo $entry_im; ?></label>
				<div class="input-group afield col-sm-7">
					<p class="form-control-static"><?php
						foreach($im as $protocol=>$uri){
							switch($protocol){
								case 'sms':
									$icon = 'fa-mobile';
									break;
								default :
									$icon = 'fa-'.$protocol;
							}
							?>
							<i class="fa <?php echo $icon;?>"></i> <?php echo $uri;?>
						<?php }
					?></p>
				</div>
			</div>
		<?php } ?>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_ip; ?></label>
			<div class="input-group afield col-sm-7">
			<p class="form-control-static"><?php echo $ip; ?></p>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xs-12">
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_store_name; ?></label>
			<div class="input-group afield col-sm-7">
			<p class="form-control-static"><?php echo $store_name; ?></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_store_url; ?></label>
			<div class="input-group afield col-sm-7">
			<p class="form-control-static"><a href="<?php echo $store_url; ?>" target="_blank"><?php echo $store_url; ?></a></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_date_added; ?></label>
			<div class="input-group afield col-sm-7">
			<p class="form-control-static"><?php echo $date_added; ?></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_shipping_method; ?></label>
			<div class="input-group afield col-sm-7">
			<p class="form-control-static"><?php echo $form['fields']['shipping_method']; ?></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_payment_method; ?></label>
			<div class="input-group afield col-sm-7">
			<p class="form-control-static"><?php echo $form['fields']['payment_method']; ?></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_total; ?></label>
			<div class="input-group afield col-sm-7">
			<p class="form-control-static"><?php echo $total; ?></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_order_status; ?></label>
			<div class="input-group afield col-sm-7" id="order_status">
			<p class="form-control-static"><a target="_blank" href="<?php echo $history; ?>"><?php echo $order_status; ?></a></p>
			</div>
		</div>
	</div>
	</div>
	
	<?php if ($comment) { ?>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_comment; ?></label>
			<div class="input-group afield col-sm-7">
			<p class="form-control-static"><?php echo $comment; ?></p>
			
			</div>
		</div>
	<?php } ?>
	
	<?php echo $this->getHookVar('order_details'); ?>
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
	<label class="h4 heading"><?php echo $form_title; ?></label>

	<table id="products" class="table ">
		<thead>
		<tr>
			<td class="left"><?php echo $column_product; ?></td>
			<td class="right"><?php echo $column_quantity; ?></td>
			<td class="right"><?php echo $column_price; ?></td>
			<td class="right"><?php echo $column_total; ?></td>
		</tr>
		</thead>

		<?php $order_product_row = 0; ?>
		<?php foreach ($order_products as $order_product) { ?>
			<tbody id="product_<?php echo $order_product_row; ?>">
			<tr <?php if (!$order_product['product_status']) { ?>class="alert alert-warning"<?php } ?>>
				<td class="left">
					<a target="_blank" href="<?php echo $order_product['href']; ?>"><?php echo $order_product['name']; ?>
						(<?php echo $order_product['model']; ?>)</a>
					<?php
					if($order_product['option']){ ?>
						<dl class="dl-horizontal product-options-list-sm">
					<?php
					foreach ($order_product['option'] as $option) { ?>
						<dt><small title="<?php echo $option['title']?>">- <?php echo $option['name']; ?></small></dt><dd><small title="<?php echo $option['title']?>"><?php echo $option['value']; ?></small></dd>
					<?php }?>
						</dl>
					<?php } ?>
				</td>
				<td class="right"><?php echo $order_product['quantity']; ?></td>
				<td><?php echo $order_product['price']; ?></td>
				<td><?php echo $order_product['total']; ?></td>
			</tr>
			</tbody>
			<?php $order_product_row++ ?>
		<?php } ?>

		<?php echo $this->getHookVar('list_more_product_last'); ?>

		<tbody id="totals">
		<?php $order_total_row = 0;
		$count = 0;
		$total = sizeof((array)$totals); ?>
		<?php foreach ($totals as $total_row) { ?>
			<tr>
				<td colspan="3" class="right">
				<span class="pull-right">
					<?php echo $total_row['title']; ?>
				</span>
				</td>
				<td>
					<?php if (!in_array($total_row['type'] , array('total'))) { ?>
						<?php echo $total_row['text']; ?>
					<?php } else { ?>
						<b class="<?php echo $total_row['type']; ?>" rel="totals[<?php echo $total_row['order_total_id']; ?>]">
							<?php echo $total_row['text']; ?>
						</b>	
					<?php } ?>
					<?php $count++; ?>
				</td>
			</tr>
			<?php $order_total_row++ ?>
		<?php } ?>
		</tbody>
	</table>

	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<a class="btn btn-primary on_save_close">
				<i class="fa fa-save"></i> <?php echo $button_save_and_close; ?>
			</a>&nbsp;
			<a class="btn btn-default" data-dismiss="modal" href="<?php echo $cancel; ?>">
				<i class="fa fa-close"></i> <?php echo $button_close; ?>
			</a>
		</div>
	</div>
	</form>

</div>

<script language="JavaScript" type="application/javascript">

	$('#<?php echo $form['form_open']->name; ?>').submit(function () {
		save_changes();
		return false;
	});
	//save and close modal
	$('.on_save_close').on('click', function () {
		var $btn = $(this);
		save_changes();
		$btn.closest('.modal').modal('hide');
		return false;
	});

	function save_changes() {
		$.ajax({
			url: '<?php echo $update; ?>',
			type: 'POST',
			data: $('#<?php echo $form['form_open']->name; ?>').serializeArray(),
			dataType: 'json',
			success: function (data) {
				success_alert(<?php js_echo($text_saved); ?>, true);
			}
		});
	}
</script>