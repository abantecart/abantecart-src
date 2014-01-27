<?php if (!empty($error['warning'])) { ?>
	<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
<div class="cbox_tl">
	<div class="cbox_tr">
		<div class="cbox_tc">
			<div class="heading icon_title_order"><?php echo $heading_title; ?></div>
			<div class="heading-tabs">
				<?php
				foreach ($tabs as $tab) {
					echo '<a href="' . $tab['href'] . '" ' . ($tab['active'] ? 'class="active"' : '') . '><span>' . $tab['text'] . '</span></a>';
				}
				?>
			</div>
			<div class="toolbar">
				<?php if (!empty ($help_url)) : ?>
					<div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img
									src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
				<?php endif; ?>
				<div class="buttons">
					<a href="<?php echo $invoice ?>" class="btn_standard"
					   target="_invoice"><?php echo $button_invoice ?></a>
				</div>
				<?php echo $form_language_switch; ?>
			</div>
		</div>
	</div>
</div>
<div class="cbox_cl">
	<div class="cbox_cr">
		<div class="cbox_cc">

			<?php echo $summary_form; ?>
			<?php echo $form['form_open']; ?>
			<div class="fieldset">
				<div class="heading"><?php echo $form_title; ?></div>
				<div class="top_left">
					<div class="top_right">
						<div class="top_mid"></div>
					</div>
				</div>
				<div class="cont_left">
					<div class="cont_right">
						<div class="cont_mid">

							<table class="form">
								<tr>
									<td><?php echo $entry_order_id; ?></td>
									<td>#<?php echo $order_id; ?></td>
								</tr>
								<tr>
									<td><?php echo $entry_invoice_id; ?></td>
									<td id="invoice"><?php if ($invoice_id) { ?>
											<?php echo $invoice_id; ?>
										<?php } else { ?>
											<a id="generate_button"
											   class="button"><span><?php echo $button_generate; ?></span></a>
										<?php } ?></td>
								</tr>
								<?php if ($customer) { ?>
									<tr>
										<td><?php echo $entry_customer; ?></td>
										<td>
											<a href="<?php echo $customer; ?>"><?php echo $firstname; ?> <?php echo $lastname; ?></a>
										</td>
									</tr>
								<?php } else { ?>
									<tr>
										<td><?php echo $entry_customer; ?></td>
										<td><?php echo $firstname; ?> <?php echo $lastname; ?></td>
									</tr>
								<?php } ?>
								<?php if ($customer_group) { ?>
									<tr>
										<td><?php echo $entry_customer_group; ?></td>
										<td><?php echo $customer_group; ?></td>
									</tr>
								<?php } ?>
								<tr>
									<td><?php echo $entry_email; ?></td>
									<td><?php echo $email; ?></td>
								</tr>
								<tr>
									<td><?php echo $entry_telephone; ?></td>
									<td><?php echo $telephone; ?></td>
								</tr>
								<?php if ($fax) { ?>
									<tr>
										<td><?php echo $entry_fax; ?></td>
										<td><input type="text" name="fax" value="<?php echo $fax; ?>"/></td>
									</tr>
								<?php } ?>
								<tr>
									<td><?php echo $entry_ip; ?></td>
									<td><?php echo $ip; ?></td>
								</tr>
								<tr>
									<td><?php echo $entry_store_name; ?></td>
									<td><?php echo $store_name; ?></td>
								</tr>
								<tr>
									<td><?php echo $entry_store_url; ?></td>
									<td>
										<a onclick="window.open('<?php echo $store_url; ?>');"><u><?php echo $store_url; ?></u></a>
									</td>
								</tr>
								<tr>
									<td><?php echo $entry_date_added; ?></td>
									<td><?php echo $date_added; ?></td>
								</tr>
								<tr>
									<td><?php echo $entry_shipping_method; ?></td>
									<td><?php echo $form['fields']['shipping_method']; ?></td>
								</tr>
								<tr>
									<td><?php echo $entry_payment_method; ?></td>
									<td><?php echo $form['fields']['payment_method']; ?></td>
								</tr>
								<tr>
									<td><?php echo $entry_total; ?></td>
									<td><?php echo $total; ?></td>
								</tr>
								<tr>
									<td><?php echo $entry_order_status; ?></td>
									<td id="order_status"><a
												href="<?php echo $history; ?>"><?php echo $order_status; ?></a></td>
								</tr>
								<?php if ($comment) { ?>
									<tr>
										<td><?php echo $entry_comment; ?></td>
										<td><?php echo $comment; ?></td>
									</tr>
								<?php } ?>
								<?php echo $this->getHookVar('order_details'); ?>
							</table>

							<table id="products" class="list">
								<thead>
								<tr>
									<td class="left"><?php echo $column_product; ?></td>
									<td class="right"><?php echo $column_quantity; ?></td>
									<td class="right"><?php echo $column_price; ?></td>
									<td class="right" width="1"><?php echo $column_total; ?></td>
								</tr>
								</thead>

								<?php $order_product_row = 0; ?>
								<?php foreach ($order_products as $order_product) { ?>
									<tbody id="product_<?php echo $order_product_row; ?>">
									<tr>
										<td class="left"><span class="remove"
															   onclick="$('#product_<?php echo $order_product_row; ?>').remove();recalculate();">&nbsp;</span>&nbsp;<a
													href="<?php echo $order_product['href']; ?>"><?php echo $order_product['name']; ?>
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
										<td class="right"><input class="no-save" type="text"
																 name="product[<?php echo $order_product_row; ?>][price]"
																 value="<?php echo $order_product['price']; ?>"/></td>
										<td class="right"><input class="no-save" type="text"
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
										<td colspan="3" class="right"><span
													style="text-align:right;"><?php echo $total_row['title']; ?></span>
										</td>
										<td class="right">
											<?php if ($count == 0 || $count == ($total - 1)) { ?>
												<b rel="totals[<?php echo $total_row['order_total_id']; ?>]"><?php echo $total_row['text']; ?></b>
												<input type="hidden"
													   name="totals[<?php echo $total_row['order_total_id']; ?>]"
													   value="<?php echo $total_row['text']; ?>"/>
											<?php } else { ?>
												<input class="no-save" type="text"
													   name="totals[<?php echo $total_row['order_total_id']; ?>]"
													   value="<?php echo $total_row['text']; ?>"/>
											<?php } ?>
											<?php $count++; ?>
										</td>
									</tr>
									<?php $order_total_row++ ?>
								<?php } ?>
								</tbody>
							</table>
							<table style="margin-top: 30px;">
								<tr>
									<td><br/><?php echo $entry_add_product; ?><br/>
										<table>
											<tr>
												<td style="padding: 0;" colspan="3"><select class="no-save"
																							id="category"
																							style="margin-bottom: 5px;"
																							onchange="getProducts();">
														<?php foreach ($categories as $category) { ?>
															<option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
														<?php } ?>
													</select></td>
											</tr>
											<tr>
												<td style="padding: 15px 0 0 0;">
													<select class="no-save" multiple="multiple" id="product" size="10"
															style="width: 450px;">
													</select>
												</td>
												<td style="vertical-align: middle;"><span class="add"
																						  onclick="addProduct();">&nbsp;</span>
												</td>
											</tr>
										</table>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="bottom_left">
					<div class="bottom_right">
						<div class="bottom_mid"></div>
					</div>
				</div>
			</div>
			<!-- <div class="fieldset"> -->
			<div class="buttons align_center">
				<button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
				<a class="btn_standard" href="<?php echo $cancel; ?>"><?php echo $form['cancel']; ?></a>
			</div>
			</form>

		</div>
	</div>
</div>
<div class="cbox_bl">
	<div class="cbox_br">
		<div class="cbox_bc"></div>
	</div>
</div>
</div>
<script type="text/javascript">

	<?php if ($currency['symbol_left']) { ?>
	var currency_symbol = '<?php echo $currency['symbol_left']; ?>';
	var currency_location = 'left';
	<?php } else { ?>
	var currency_symbol = '<?php echo $currency['symbol_right']; ?>';
	var currency_location = 'right';
	<?php }?>

	$(function () {
		$('#products input').live('keyup', function () {
			recalculate()
		});
		$.aform.styleGridForm('#product');
		$.aform.styleGridForm('#category');
		$('#products input[type*="text"]').each(function () {
			$.aform.styleGridForm(this);
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
				if (data.invoice_id) {
					$('#generate_button').fadeOut('slow', function () {
						$('#invoice').html(data.invoice_id);
					});
				}
			}
		});
	});

	function getProducts() {
		$('#product option').remove();

		$.ajax({
			url: '<?php echo $category_products; ?>&category_id=' + $('#category').attr('value') + '&currency=<?php echo $order_info['currency'] ?>&value=<?php echo $order_info['value'] ?>&customer_group_id=<?php echo $order_info['customer_group_id'] ?>',
			dataType: 'json',
			success: function (data) {
				for (i = 0; i < data.length; i++) {
					$('#product').append('<option value="' + data[i]['product_id'] + '" price="' + data[i]['price'] + '" >' + data[i]['name'] + ' (' + data[i]['model'] + ') - ' + data[i]['price'] + ' </option>');
				}
			}
		});
	}

	getProducts();

	var order_product_row = <?php echo $order_product_row; ?>;

	function addProduct() {

		$('#product :selected').each(function () {

			html = '<tbody id="product_' + order_product_row + '">';
			html += '<tr>';
			html += '<td class="left">';
			html += '<input type="hidden" name="product[' + order_product_row + '][product_id]" value="' + $(this).attr('value') + '">';
			html += '<span onclick="$(\'#product_' + order_product_row + '\').remove(); recalculate();" class="remove">&nbsp;</span>';
			html += '<a href="<?php echo $product_update . '&product_id='; ?>' + $(this).attr('value') + '&token=<?php echo $token; ?>">' + $(this).text() + '</a>';
			html += '</td>';
			html += '<td class="right"><input type="text" name="product[' + order_product_row + '][quantity]" value="1" size="4" /></td>';
			html += '<td class="right"><input type="text" name="product[' + order_product_row + '][price]" value="' + $(this).attr('price') + '" /></td>';
			html += '<td class="right"><input type="text" name="product[' + order_product_row + '][total]" value="' + $(this).attr('price') + '" /></td>';
			html += '</tr>';
			html += '</tbody>';

			$('#totals').before(html);

			$("input, textarea, select, .scrollbox", '#product_' + order_product_row).aform({        triggerChanged: false        });
			$('#product_' + order_product_row + ' input[type*="text"]').each(function () {
				$.aform.styleGridForm(this);
			});

			order_product_row++;
		});

		recalculate();
	}

</script>