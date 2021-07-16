<?php include($tpl_common_dir . 'action_confirm.tpl');
echo $summary_form;
echo $order_tabs;
?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">

			<?php if (!empty ($list_url)) { ?>
			<div class="btn-group">
				<a class="btn btn-white tooltips" href="<?php echo $list_url; ?>" data-toggle="tooltip" data-original-title="<?php echo $text_back_to_list; ?>">
					<i class="fa fa-arrow-left fa-lg"></i>
				</a>
			</div>
			<?php } ?>

			<div class="btn-group mr10 toolbar">
			<a class="btn btn-white tooltips" target="_invoice" href="<?php echo $invoice_url; ?>" data-toggle="tooltip"
			   title="<?php echo $text_invoice; ?>" data-original-title="<?php echo $text_invoice; ?>">
				<i class="fa fa-file-text"></i>
			</a>
			</div>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
	<label class="h4 heading"><?php echo $form_title; ?></label>

	<div class="container-fluid">
	<div class="col-sm-6 col-xs-12">
		<?php echo $this->getHookVar('order_details_left_pre'); ?>
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
					$button_invoice->style = 'btn btn-info';
					echo $button_invoice;
				} ?>
			</p></div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $entry_customer; ?></label>
			<div class="input-group afield col-sm-7">
				<p class="form-control-static">
				<?php if ($customer_href) { ?>
					<a class="btn btn-default"
					   data-toggle="modal"
					   data-target="#viewport_modal"
					   href="<?php echo $customer_vhref; ?>"
					   data-fullmode-href="<?php echo $customer_href ?>">
						<i class="fa fa-eye"></i>
						<?php echo $firstname.' '.$lastname; ?></a>
				<?php
				} else {
					echo $firstname.' '.$lastname;
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
				<div class="input-group afield col-sm-7"><?php echo $fax; ?></div>
			</div>
		<?php } ?>
		<?php echo $this->getHookVar('order_details_left_attributes'); ?>
		<?php if ($im) { ?>
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
		<?php echo $this->getHookVar('order_details_left_post'); ?>
	</div>
	<div class="col-sm-6 col-xs-12">
		<?php echo $this->getHookVar('order_details_right_pre'); ?>
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
		<?php echo $this->getHookVar('order_details_right_attributes'); ?>
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
		<?php echo $this->getHookVar('order_details_right_post'); ?>
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
			<tr <?php if (!$order_product['product_status']) { ?>class="alert alert-warning"<?php } ?>>
				<td>
					<a class="remove btn btn-xs btn-danger-alt tooltips"
					   data-original-title="<?php echo $button_delete; ?>"
					   data-order-product-row="<?php echo $order_product_row; ?>">
						<i class="fa fa-minus-circle"></i>
					</a>
				</td>
				<td class="left">
					<a target="_blank" href="<?php echo $order_product['href']; ?>"><?php echo $order_product['name']; ?>
						<?php echo $order_product['model'] ? '('.$order_product['model'].')' : ''; ?></a>
					<input type="hidden"
						   name="product[<?php echo $order_product_row; ?>][order_product_id]"
						   value="<?php echo $order_product['order_product_id']; ?>"/>
					<input type="hidden"
						   name="product[<?php echo $order_product_row; ?>][product_id]"
						   value="<?php echo $order_product['product_id']; ?>"/>
					<?php
					if($order_product['option']){ ?>
						<dl class="dl-horizontal product-options-list-sm">
					<?php
					foreach ($order_product['option'] as $option) { ?>
						<dt><small title="<?php echo $option['title']?>">- <?php echo $option['name']; ?></small></dt><dd><small class="product_option_value" title="<?php echo $option['title']?>"><?php echo $option['value']; ?></small></dd>
							<?php echo $this->getHookVar('order_details_option_'.$option['name'].'_additional_info'); ?>
					<?php }?>
						</dl>
					<?php } ?>
					<?php echo $this->getHookVar('order_details_product_'.$order_product['product_id'].'_additional_info_1'); ?>
				</td>
				<td class="right">
					<?php
					$readonly = !$order_product['product_status'] ? 'readonly' : '';
					if( !$order_product['stock_quantities'] ) { ?>
						<input class="afield no-save" type="text"
						<?php echo  $readonly ?>
							name="product[<?php echo $order_product_row; ?>][quantity]"
							value="<?php echo $order_product['quantity']; ?>"
							size="4"/>
					<?php }else{ ?>
						<table class="table table-striped">
					<?php	foreach($order_product['stock_quantities'] as $item){
						$readonly_stock = $readonly;
						if($item['available'] == 'absent'){
							$readonly_stock = 'disabled';
						}
						?>
							<tr>
								<td><?php echo $item['name']?> (<?php echo (int)$item['available']?>):&nbsp;</td>
								<td>
									<input class="afield no-save" type="text"
									<?php echo $readonly_stock ?>
										name="product[<?php echo $order_product_row; ?>][stock_quantity][<?php echo $item['location_id']?>]"
										value="<?php echo $item['quantity']; ?>"
										size="4"/>
								</td>
							</tr>
						<?php } ?>
						</table>
					<?php }

					?>
				</td>
				<td>
					<input class="no-save pull-right" type="text"
				           readonly
						   name="product[<?php echo $order_product_row; ?>][price]"
						   value="<?php echo $order_product['price']; ?>"/>
				</td>
				<td>
					<input readonly class="no-save pull-right" type="text"
						   name="product[<?php echo $order_product_row; ?>][total]"
						   value="<?php echo $order_product['total']; ?>"/>
				</td>
			<?php echo $this->getHookVar('order_details_product_'.$order_product['product_id'].'_additional_info_2'); ?>
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
				<td colspan="4" class="right">
				<span class="pull-right">
					<?php echo $total_row['title']; ?>
					<?php if (!in_array($total_row['type'] , ['subtotal', 'total'])) { ?>
						<?php if (!$total_row['unavailable']) { ?>
						<a class="reculc_total btn btn-xs btn-info-alt tooltips"
						   	data-original-title="<?php echo $text_recalc; ?>"
					   		data-order-total-id="<?php echo $total_row['order_total_id']; ?>">
					    	<i class="fa fa-refresh"></i>
						</a>
						<?php } ?>
						<?php if ($total_key_count[$total_row['key']] == 1 ) { // do not alloe delete of duplicate keys?>
						<a class="remove btn btn-xs btn-danger-alt tooltips"
						   data-original-title="<?php echo $button_delete; ?>"
						   data-confirmation="delete" onclick="deleteTotal('<?php echo $total_row['order_total_id']; ?>');">
							<i class="fa fa-minus-circle"></i>
						</a>
						<?php } ?>
					<?php } ?>
				</span>
				</td>
				<td>
					<?php if (!in_array($total_row['type'] , ['total'])) { ?>
						<input type="text"
                                class="col-sm-2 col-xs-12 no-save <?php echo $total_row['type']; ?>"
                                name="totals[<?php echo $total_row['order_total_id']; ?>]"
                                value="<?php echo $total_row['text']; ?>"/>
					<?php } else { ?>
					<b class="<?php echo $total_row['type']; ?>" rel="totals[<?php echo $total_row['order_total_id']; ?>]"><?php echo $total_row['text']; ?>
					</b>
					<input type="hidden" class="hidden_<?php echo $total_row['type']; ?>" name="totals[<?php echo $total_row['order_total_id']; ?>]" value="<?php echo $total_row['text']; ?>"/>
					<?php } ?>

					<?php $count++; ?>
				</td>
			</tr>
			<?php $order_total_row++ ?>
		<?php } ?>
		<?php if ($totals_add) {?>
			<tr>
				<td colspan="4" class="right"><span class="pull-right"><?php echo $text_add; ?></span></td>
				<td>
					<b rel="totals[<?php echo $total_row['order_total_id']; ?>]">
					<a class="add_totals btn btn-xs btn-info-alt tooltips"
					   data-original-title="<?php echo $text_add; ?>"
					   data-order-id="<?php echo $order_id; ?>">
					    <i class="fa fa-plus-circle"></i>

                        <?php foreach ($totals_add as $total_row) { ?>
                        <div class="hidden <?php echo $total_row['key']; ?>">
                            <div class="row">
                            <input type="hidden" name="key" value="<?php echo $total_row['key']; ?>"/>
                            <input type="hidden" name="type" value="<?php echo $total_row['type']; ?>"/>
                            <input type="hidden" name="sort_order" value="<?php echo $total_row['sort_order']; ?>"/>
                            <div class="col-sm-3 col-xs-12">
                                <span class="pull-right"><?php echo $text_order_total_title; ?></span>
                            </div>
                            <div class="col-sm-4 col-xs-12">
                                <input type="text" class="col-sm-2 col-xs-12 no-save"
                                   name="title" value="<?php echo $total_row['title']; ?>"/>
                            </div>
                            <div class="col-sm-2 col-xs-12">
                                <span class="pull-right"><?php echo $text_order_total_amount; ?></span>
                            </div>
                            <div class="col-sm-3 col-xs-12">
                                <input type="text" class="col-sm-2 col-xs-12 no-save"
                                   name="text" value="<?php echo $total_row['text']; ?>"/>
                            </div>
                            </div>
                        </div>
                        <?php } ?>
					</a>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<?php if($add_product){?>
    <div class="container-fluid form-inline">
        <div class="list-inline col-sm-12"><?php echo $entry_add_product; ?></div>
        <div class="list-inline input-group afield col-sm-7 col-xs-9">
            <?php echo $add_product;?>
        </div>
        <div class="list-inline input-group afield col-sm-offset-0 col-sm-3 col-xs-1">
            <a class="add btn btn-success tooltips"
               data-original-title="<?php echo $text_add; ?>">
                <i class="fa fa-plus-circle fa-lg"></i></a>
        </div>
    </div>
	<?php } ?>
	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			<i class="fa fa-save fa-fw"></i> <?php echo $button_save; ?>
			</button>
			<?php if (!$no_recalc_allowed) { ?>
			<a class="btn btn-default save_and_recalc" href="#">
			<i class="fa fa-save fa-fw"></i><i class="fa fa-refresh fa-fw"></i> <?php echo $button_save.' & '.$text_recalc.' '.$text_all; ?>
			</a>
			<?php } ?>
			<a class="btn btn-default" href="<?php echo $cancel; ?>">
			<i class="fa fa-arrow-left fa-fw"></i> <?php echo $form['cancel']->text; ?>
			</a>
		</div>
	</div>

</form>

</div><!-- <div class="tab-content"> -->

<?php echo $this->html->buildElement(
    [
        'type'        => 'modal',
        'id'          => 'add_product_modal',
        'modal_type'  => 'lg',
        'data_source' => 'ajax'
    ]
);
?>

<?php echo $this->html->buildElement(
    [
        'type'       => 'modal',
        'id'         => 'add_order_total',
        'modal_type' => 'md',
        'title'      => $text_order_total_add,
        'content'    => '
            <form class="aform form-horizontal" enctype="multipart/form-data" method="post" class="add_order_total" action="'.$edit_order_total.'">
            <div class="mb20">' . $new_total . '
            </div>
            <div class="content container-fluid mb20">
            </div>
            <div class="text-center mb20">
                <button class="btn btn-primary lock-on-click">
                <i class="fa fa-save fa-fw"></i>'. $button_save . '
                </button>
                <button class="btn btn-default" type="button" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-arrow-left fa-fw"></i> '. $button_cancel . '
                </button>
            </div>
            </form>'
    ]
);
?>

<script type="text/javascript">

	var decimal_point = '<?php echo $decimal_point; ?>';
	var decimal_place = '<?php echo $currency['decimal_place']; ?>';
	var thousand_point = '<?php echo $thousand_point; ?>';
	<?php if ($currency['symbol_left']) { ?>
	var currency_symbol = '<?php echo $currency['symbol_left']; ?>';
	var currency_location = 'left';
	<?php } else { ?>
	var currency_symbol = '<?php echo $currency['symbol_right']; ?>';
	var currency_location = 'right';
	<?php }?>

	$(function () {

		$('#add_product').chosen({'width': '100%', 'white-space': 'nowrap'});
		$('#add_product').on('change', addProduct);

		$("#products input").aform({        triggerChanged: false        });
		$('#products input[type*="text"]').each(function () {
			$.aform.styleGridForm(this);
		});


		$(document).on('click', '#products a.remove', function () {
			var id = $(this).attr('data-order-product-row');
			$('#product_' + id).remove();
			recalculate();
			return false;
		});

		$('a.add').click(function () {
			addProduct();
			return false;
		});


		$('a.edit_product').click(function () {
			addProduct($(this).attr('data-order-product-id'));
			return false;
		});

		$(document).on('keyup', '#products input', function () {
			recalculate();
		});


	});

	function formatMoney(num, c, d, t) {
		c = isNaN(c = Math.abs(c)) ? 2 : c,
				d = d === undefined ? "." : d,
				t = t === undefined ? "," : t,
				s = num < 0 ? "-" : "",
				i = parseInt(num = Math.abs(+num || 0).toFixed(c)) + "",
				j = (j = i.length) > 3 ? j % 3 : 0;
		return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(num - i).toFixed(c).slice(2) : "");
	}

	function get_currency_str(num) {
		var str;
		if (currency_location === 'left') {
			str = currency_symbol + formatMoney(num, decimal_place, decimal_point, thousand_point);
		} else {
			str = formatMoney(num, decimal_place, decimal_point, thousand_point) + currency_symbol;
		}
		return str;
	}

	function get_currency_num(str) {
		str = str === undefined || str.length === 0 ? '0' : str;
		var final_number = str.replace(thousand_point, '');
		final_number = final_number.replace(currency_symbol, '');
		final_number = final_number.replace(decimal_point, '.');
		final_number = parseFloat(final_number.replace(/[^0-9\-\.]/g, ''));

		return final_number;
	}

	function recalculate() {

		var qty, price, total, total_str;
		var subtotal = 0;

		//update products
		$('#products tbody[id^="product"]').each(function (i, v) {
			if($('input[name*="stock_quantity"]', v).length>0){
				qty = 0;
				$('input[name*="stock_quantity"]', v).each(
					function(){
						if($(this).val() == ''){ return false;}
						var qq = parseInt($(this).val(), 10);
						qty += (qq == NaN || qq ==='') ? 0 : qq;
					}
				);
			}
			else if($('input[name*="quantity"]',v).length > 0) {
				qty = $('input[name*="quantity"]',v).val();
			}

			price = get_currency_num($('input[name$="price]"]', v).val());
			total = qty * price;
			$('input[name$="total]"]', v).val(get_currency_str(total));
			subtotal += total;
		});

		//update first total - subtotal
		$('#products .subtotal').val(get_currency_str(subtotal));

		total = 0;
		$('input[name^="totals"]').each(function (i, v) {
			//skip grand total
			var n = get_currency_num($(v).val());
			if (!$(v).hasClass('hidden_total') && $.isNumeric(n)) {
				total += n;
			}
		});

		//update last - total
		$('#products .total').html(get_currency_str(total));
		$('#products .hidden_total').val(get_currency_str(total));

	}

	$('#generate_invoice').click(function () {
		var that = $(this).parents('p');
		$.ajax({
			url: '<?php echo $invoice_generate; ?>&order_id=<?php echo $order_id; ?>',
			dataType: 'json',
			beforeSend: function () {
				$('#generate_invoice').attr('disabled', 'disabled');
			},
			complete: function () {
				$('#generate_invoice').attr('disabled', '');
			},
			success: function (data) {
				if (data.hasOwnProperty('invoice_id')) {
					$('#generate_invoice').fadeOut('slow', function(){
						that.html(data.invoice_id).fadeIn();
					});
				}
			}
		});
		return false;
	});


	var order_product_row = <?php echo $order_product_row; ?>;

	function addProduct(order_product_id) {
		var id = '';
		if(order_product_id > 0){
			id = '&order_product_id='+order_product_id;
		}else{
			var vals = $("#add_product").chosen().val();
			$("#add_product").val('').trigger("chosen:updated");;
			if(vals){
				id = '&product_id='+vals[0];
			}
		}

		if(id.length>0){
			$('#add_product_modal')
					.modal({ keyboard: false})
					.find('.modal-content')
					.load('<?php echo $add_product_url; ?>'+id, function () {
					formOnExit();
					bindCustomEvents('#orderProductFrm');
					spanHelp2Toggles();
				});
		}
	}

	$('a.reculc_total').click(function () {
		$(this).append('<input type="hidden" name="force_recalc_single" value="1">');
	    var total_id = $(this).attr('data-order-total-id');
	    $('input[name="totals['+total_id+']"]').val('');
	    $('#orderFrm').submit();
	    return false;
	});

	$('a.save_and_recalc').click(function () {
		$(this).append('<input type="hidden" name="force_recalc" value="1">');
	    $('#orderFrm').submit();
	    return false;
	});

	$('a.add_totals').click(function () {
		addTotal();
	    return false;
	});

	$('#orderFrm_new_total').change(function () {
		addTotalSelect( $("#orderFrm_new_total option:selected").text() );
	});

	function addTotal() {
		$('#add_order_total').modal({ keyboard: false});
		addTotalSelect( $("#orderFrm_new_total option:selected").text() );
	}

	function addTotalSelect(key) {
		var html = $('.add_totals .hidden.'+key).html();
		$('#add_order_total form .content').html(html);
	}

	function deleteTotal(order_total_id) {
		location = '<?php echo $delete_order_total; ?>&order_total_id=' + order_total_id;
	}

	$('input[name*="quantity]"]').keyup(function () {
		var v = $(this).val().replace(/[^0-9]/,'');
		//v = v==='' ? 0 : v;
		$(this).val(v);
	});

	$(document).ready(() => {
		let index = 0;
		$('.product_option_value').each(function () {
			if ($(this).text().length < $(this).attr('title').length) {
				 $(this).append('&nbsp;&nbsp;&nbsp;<a id="product_option_value_copy'+index+'" ' +
						 'data-copy="'+$(this).attr('title')+'"'+
					 ' onClick="copyToClipboard(\'#product_option_value_copy'+index+'\', this)" title="Copy"><i class="fa fa-copy"></i></a>')
			}
			index ++;
		})
	})

</script>
