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
					$button_invoice->style = 'btn btn-info';
					echo $button_invoice;
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
					<?php if ($order_product['product_status']) { ?>
					<a class="edit_product btn btn-xs btn-info-alt tooltips"
					   data-original-title="<?php echo $text_edit; ?>"
					   data-order-product-id="<?php echo $order_product['order_product_id']; ?>">
						<i class="fa fa-pencil"></i>
					</a>
					<?php } ?>
				</td>
				<td class="left">
					<a target="_blank" href="<?php echo $order_product['href']; ?>"><?php echo $order_product['name']; ?>
						(<?php echo $order_product['model']; ?>)</a>
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
						<dt><small title="<?php echo $option['title']?>">- <?php echo $option['name']; ?></small></dt><dd><small title="<?php echo $option['title']?>"><?php echo $option['value']; ?></small></dd>
					<?php }?>
						</dl>
					<?php } ?></td>
				<td class="right">
						<input class="afield no-save" type="text"
						<?php if (!$order_product['product_status']) { ?>
							readonly
						<?php } ?>
							name="product[<?php echo $order_product_row; ?>][quantity]"
							value="<?php echo $order_product['quantity']; ?>"
							size="4"/></td>
				<td><input class="no-save pull-right" type="text"
				           readonly
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
				<td colspan="4" class="right">
				<span class="pull-right">
					<?php echo $total_row['title']; ?>
					<?php if (!in_array($total_row['type'] , array('subtotal','total'))) { ?>
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
					<?php if (!in_array($total_row['type'] , array('total'))) { ?>
						<input type="text" class="col-sm-2 col-xs-12 no-save <?php echo $total_row['type']; ?>"
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
			<a class="btn btn-primary on_save_close">
				<i class="fa fa-save"></i> <?php echo $button_save_and_close; ?>
			</a>&nbsp;
			<button class="btn btn-primary">
				<i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
			</button>
			&nbsp;
			<a class="btn btn-default" data-dismiss="modal" href="<?php echo $cancel; ?>">
				<i class="fa fa-refresh"></i> <?php echo $form['cancel']->text; ?>
			</a>
		</div>
	</div>
	</form>

</div>