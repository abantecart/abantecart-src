<?php if (!empty($error['warning'])) { ?>
	<div class="warning alert alert-error alert-danger"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<?php echo $summary_form; ?>

<?php echo $order_tabs ?>
<div class="tab-content">

<div class="panel-heading">

	<div class="pull-right">
		<div class="btn-group mr10 toolbar">
			<a class="btn btn-white tooltips" target="_invoice" href="<?php echo $invoice_url; ?>" data-toggle="tooltip"
			   title="<?php echo $text_invoice; ?>" data-original-title="<?php echo $text_invoice; ?>">
				<i class="fa fa-file-text"></i>
			</a>
			<?php if (!empty ($help_url)) : ?>
				<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip"
				   title="" data-original-title="Help">
					<i class="fa fa-question-circle"></i>
				</a>
			<?php endif; ?>
		</div>

		<?php echo $form_language_switch; ?>
	</div>

</div>

<?php echo $form['form_open']; ?>
<div class="panel-body panel-body-nopadding">
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
			<div class="input-group afield col-sm-7">
				<?php if ($invoice_id) {
					echo $invoice_id;
				} else col-sm-5 ?>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_customer; ?></label>
			<div class="input-group afield col-sm-7">
				<p class="form-control-static">
				<?php if ($customer_url) { ?>
					<a href="<?php echo $customer_url; ?>"><?php echo $firstname; ?> <?php echo $lastname; ?></a>
				<?php
				} else {
					echo $firstname; ?> <?php echo $lastname;
				} ?>
				</p>
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
				<div class="input-group afield col-sm-7"><?php echo $form['fields']['fax']; ?></div>
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

<div class="panel-body panel-body-nopadding">
	<label class="h4 heading"><?php echo $form_title; ?></label>

	<table id="products" class="table ">
		<thead>
		<tr>
			<td></td>
			<td class="left"><?php echo $column_product; ?></td>
			<td class="right"><?php echo $column_quantity; ?></td>
			<td class="right"><?php echo $column_price; ?></td>
			<td class="right"><?php echo $column_total; ?></td>
		</tr>
		</thead>

		<?php $order_product_row = 0; ?>
		<?php foreach ($order_products as $order_product) { ?>
			<tbody id="product_<?php echo $order_product_row; ?>">
			<tr>
				<td>
					<a class="remove btn btn-xs btn-danger-alt tooltips"
					   data-original-title="<?php echo $text_remove; ?>"
					   data-order-product-row="<?php echo $order_product_row; ?>">
						<i class="fa fa-minus-circle"></i>
					</a>
				</td>
				<td class="left">
					<a href="<?php echo $order_product['href']; ?>"><?php echo $order_product['name']; ?>
						(<?php echo $order_product['model']; ?>)</a>
					<input type="hidden"
						   name="product[<?php echo $order_product_row; ?>][order_product_id]"
						   value="<?php echo $order_product['order_product_id']; ?>"/>
					<input type="hidden"
						   name="product[<?php echo $order_product_row; ?>][product_id]"
						   value="<?php echo $order_product['product_id']; ?>"/>
					<?php foreach ($order_product['option'] as $option) { ?>
						<br/>
						&nbsp;
						<small>
							- <?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
					<?php } ?></td>
				<td class="right"><input class="no-save" type="text"
										 name="product[<?php echo $order_product_row; ?>][quantity]"
										 value="<?php echo $order_product['quantity']; ?>"
										 size="4"/></td>
				<td><input class="no-save pull-right" type="text"
						   name="product[<?php echo $order_product_row; ?>][price]"
						   value="<?php echo $order_product['price']; ?>"/></td>
				<td><input readonly class="no-save pull-right" type="text"
						   name="product[<?php echo $order_product_row; ?>][total]"
						   value="<?php echo $order_product['total']; ?>"/></td>
			</tr>
			</tbody>
			<?php $order_product_row++ ?>
		<?php } ?>

		<?php echo $this->getHookVar('list_more_product_last'); ?>

		<tbody id="totals">
		<?php $order_total_row = 0;
		$count = 0;
		$total = count($totals); ?>
		<?php foreach ($totals as $total_row) { ?>
			<tr>
				<td colspan="4" class="right"><span class="pull-right"><?php echo $total_row['title']; ?></span></td>
				<td>
					<?php if ($count == 0 || $count == ($total - 1)) { ?>
						<b rel="totals[<?php echo $total_row['order_total_id']; ?>]"><?php echo $total_row['text']; ?></b>
						<div class="input-group input-group-sm afield">
							<input type="hidden" class="col-sm-2 col-xs-12"
								   name="totals[<?php echo $total_row['order_total_id']; ?>]"
								   value="<?php echo $total_row['text']; ?>"/>
						</div>
					<?php } else { ?>
						<div class="input-group input-group-sm afield">
							<input type="text" class="col-sm-2 col-xs-12 no-save"
								   name="totals[<?php echo $total_row['order_total_id']; ?>]"
								   value="<?php echo $total_row['text']; ?>"/>
						</div>
					<?php } ?>
					<?php $count++; ?>
				</td>
			</tr>
			<?php $order_total_row++ ?>
		<?php } ?>
		</tbody>
	</table>

	<div class="container-fluid form-inline">
		<div class="list-inline col-sm-12"><?php echo $entry_add_product; ?></div>
		<div class="list-inline input-group afield col-sm-7 col-xs-9">
			<select multiple id="add_product" class="form-control no-save" data-placeholder="<?php echo $text_select_product; ?>">
				<?php
				foreach ($products as $product) {
					?>
					<option id="product_id_<?php echo $product['product_id']; ?>"
							value="<?php echo $product['product_id'] ?>"
							data-price="<?php echo $product['price'] ?>"
							><?php echo $product['name'] ?> <?php echo $product['model'] ? "(" . $product['model'] . ")" : '' ?>
						- <?php echo $product['price'] ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="list-inline input-group afield col-sm-offset-1 col-sm-3 col-xs-1">
			<a class="add btn btn-success tooltips" data-original-title="<?php echo $text_add; ?>"><i
						class="fa fa-plus-circle"></i></a>
		</div>
	</div>
</div>
<div class="panel-footer">
	<div class="row center">
		<div class="col-sm-6 col-sm-offset-3">
			<button class="btn btn-primary">
				<i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
			</button>
			&nbsp;
			<a class="btn btn-default" href="<?php echo $cancel; ?>">
				<i class="fa fa-refresh"></i> <?php echo $form['cancel']->text; ?>
			</a>
		</div>
	</div>
</div>

</form>

</div><!-- <div class="tab-content"> -->


<script type="text/javascript">

	<?php if ($currency['symbol_left']) { ?>
	var currency_symbol = '<?php echo $currency['symbol_left']; ?>';
	var currency_location = 'left';
	<?php } else { ?>
	var currency_symbol = '<?php echo $currency['symbol_right']; ?>';
	var currency_location = 'right';
	<?php }?>

	$(function () {

		$('#add_product').chosen({'width': '100%', 'white-space': 'nowrap'});

		$("#products input").aform({        triggerChanged: false        });
		$('#products input[type*="text"]').each(function () {
			$.aform.styleGridForm(this);
		});


		$('#products a.remove').click(function () {
			var id = $(this).attr('data-order-product-row');
			$('#product_' + id).remove();
			recalculate();
			return false;
		});

		$('a.add').click(function () {
			addProduct();
			return false;
		});


		$('#products input[name*=quantity]').on('keyup', function () {
			recalculate();
		});


	});

	function get_currency_str(num) {
		var str;
		if (currency_location == 'left') {
			str = currency_symbol + num.toFixed(2);
		} else {
			str = num.toFixed(2) + currency_symbol;
		}
		return str;
	}

	function get_currency_num(str) {
		return parseFloat(str.replace(currency_symbol, ''));
	}

	function recalculate() {

		var qty, price, total, total_str;
		var subtotal = 0;

		//update products
		$('#products tbody[id^="product"]').each(function (i, v) {
			qty = $('input[name$="quantity]"]', v).val();
			price = get_currency_num($('input[name$="price]"]', v).val());
			total = qty * price;
			$('input[name$="total]"]', v).val(get_currency_str(total));
			subtotal += total;
		});

		var subtotal_name = $('input[name^="total"]').first().attr('name');
		var total_name = $('input[name^="total"]').last().attr('name');

		//update first total - subtotal
		$('input[name^="total"]').first().val(get_currency_str(subtotal));
		$('b[rel="' + subtotal_name + '"]').html(get_currency_str(subtotal));
		//update last - total
		$('input[name^="total"]').last().val(0);

		var total = 0;
		$('input[name^="total"]').each(function (i, v) {
			total += get_currency_num($(v).val());
		});

		//update last - total
		$('input[name^="total"]').last().val(get_currency_str(total));
		$('b[rel="' + total_name + '"]').html(get_currency_str(total));

	}

	$('#generate_button').click(function () {
		var that = $(this).parent();
		$.ajax({
			url: '<?php echo $invoice_generate; ?>&order_id=<?php echo $order_id; ?>',
			dataType: 'json',
			beforeSend: function () {
				$('#generate_button').attr('disabled', 'disabled');
			},
			complete: function () {
				$('#generate_button').attr('disabled', '');
			},
			success: function (data) {
				if (data.hasOwnProperty('invoice_id')) {
					$('#generate_button').fadeOut('slow', function(){
						that.html(data.invoice_id).fadeIn();
					});
				}
			}
		});
		return false;
	});


	var order_product_row = <?php echo $order_product_row; ?>;

	function addProduct() {
		var vals = $("#add_product").chosen().val();
		for (var k in vals) {
			var product = $('#product_id_' + vals[k]);
			html = '<tbody id="product_' + order_product_row + '"><tr>';
			html += '<td><a class="remove btn btn-xs btn-danger-alt tooltips" data-original-title="<?php echo $text_remove;?>"  "data-order-product-row="' + order_product_row + '"><i class="fa fa-minus-circle"></i></a></td>';
			html += '<td class="left">';
			html += '<input type="hidden" name="product[' + order_product_row + '][product_id]" value="' + vals[k] + '">';
			html += '<a href="<?php echo $product_update . '&product_id='; ?>' + vals[k] + '&token=<?php echo $token; ?>">' + product.text() + '</a>';
			html += '</td>';
			html += '<td class="right"><input type="text" name="product[' + order_product_row + '][quantity]" value="1" size="4" /></td>';
			html += '<td class="right"><input type="text" name="product[' + order_product_row + '][price]" value="' + product.attr('data-price') + '" /></td>';
			html += '<td class="right"><input type="text" name="product[' + order_product_row + '][total]" value="' + product.attr('data-price') + '" /></td>';
			html += '</tr></tbody>';

			$('#totals').before(html);

			$("input, textarea, select, .scrollbox", '#product_' + order_product_row).aform({        triggerChanged: false        });
			$('#product_' + order_product_row + ' input[type*="text"]').each(function () {
				$.aform.styleGridForm(this);
			});

			order_product_row++;
		}
		$("#add_product").chosen().val('').trigger("chosen:updated");
		recalculate();
	}

</script>