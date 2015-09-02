<?php echo $head; ?>
<h1 class="heading1">
	<span class="maintext"><i class="fa fa-file"></i> <?php echo $heading_title; ?></span>
	<span class="subtext"></span>
</h1>

<div class="contentpanel">

	<div class="container-fluid table-responsive">
		<table class="table table-striped table-bordered">
			<tr>
				<td><?php if ($invoice_id) { ?>
						<b><?php echo $text_invoice_id; ?></b><br/>
						<?php echo $invoice_id; ?><br/>
						<br/>
					<?php } ?>
					<b><?php echo $text_order_id; ?></b><br/>
					#<?php echo $order_id; ?><br/>
					<br/>
					<b><?php echo $column_status; ?></b><br/>
					<?php echo $status; ?><br/>
					<br/>
					<b><?php echo $text_email; ?></b><br/>
					<?php echo $email; ?><br/>
					<br/>
					<?php if ($telephone) { ?>
						<b><?php echo $text_telephone; ?></b><br/>
						<?php echo $telephone; ?><br/>
						<br/>
					<?php } ?>
					<?php if ($fax) { ?>
						<b><?php echo $text_fax; ?></b><br/>
						<?php echo $fax; ?><br/>
						<br/>
					<?php } ?>
					<?php if ($shipping_method) { ?>
						<b><?php echo $text_shipping_method; ?></b><br/>
						<?php echo $shipping_method; ?><br/>
						<br/>
					<?php } ?>
					<b><?php echo $text_payment_method; ?></b><br/>
					<?php echo $payment_method; ?></td>
				<td><?php if ($shipping_address) { ?>
						<b><?php echo $text_shipping_address; ?></b><br/>
						<address><?php echo $shipping_address; ?></address>
					<?php } ?>
				</td>
				<td><b><?php echo $text_payment_address; ?></b><br/>
					<address><?php echo $payment_address; ?></address>
				</td>
			</tr>
		</table>
	</div>
	<div class="container-fluid invoice_products table-responsive">
		<table class="table table-striped table-bordered">
			<tr>
				<th class="align_left"><?php echo $text_image; ?></th>
				<th class="align_left"><?php echo $text_product; ?></th>
				<th class="align_left"><?php echo $text_model; ?></th>
				<th class="align_right"><?php echo $text_quantity; ?></th>
				<th class="align_right"><?php echo $text_price; ?></th>
				<th class="align_right"><?php echo $text_total; ?></th>
			</tr>
			<?php foreach ($products as $product) { ?>
				<tr>
					<td align="left" valign="top"><?php echo $product['thumbnail']['thumb_html']; ?></td>
					<td class="align_left  valign_top"><a
								href="<?php echo str_replace('%ID%', $product['id'], $product_link) ?>"><?php echo $product['name']; ?></a>
						<?php foreach ($product['option'] as $option) { ?>
							<br/>
							&nbsp;
							<small title="<?php echo $option['title']?>"> - <?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
						<?php } ?></td>
					<td class="align_left valign_top"><?php echo $product['model']; ?></td>
					<td class="align_right valign_top"><?php echo $product['quantity']; ?></td>
					<td class="align_right valign_top"><?php echo $product['price']; ?></td>
					<td class="align_right valign_top"><?php echo $product['total']; ?></td>
				</tr>
			<?php } ?>
			<?php echo $this->getHookVar('list_more_product_last'); ?>
		</table>

		<div class="col-md-4 col-xs-6 pull-right">
			<table class="table table-striped table-bordered">
				<?php foreach ($totals as $total) { ?>
					<tr>
						<td class="align_right"><?php echo $total['title']; ?></td>
						<td class="align_right"><?php echo $total['text']; ?></td>
					</tr>
				<?php } ?>
			</table>
		</div>
	</div>

	<?php if ($comment) { ?>
		<div class="container-fluid">
			<h4 class="heading4"><?php echo $text_comment; ?></h4>

			<div class="content"><?php echo $comment; ?></div>
		</div>
	<?php } ?>

	<?php echo $this->getHookVar('order_attributes'); ?>

	<?php if ($historys) { ?>
		<div class="container-fluid">
			<h4 class="heading4"><?php echo $text_order_history; ?></h4>
			<table class="table table-striped table-bordered">
				<tr>
					<th class="align_left"><?php echo $column_date_added; ?></th>
					<th class="align_left"><?php echo $column_status; ?></th>
					<th class="align_left"><?php echo $column_comment; ?></th>
				</tr>
				<?php foreach ($historys as $history) { ?>
					<tr>
						<td class="valign_top"><?php echo $history['date_added']; ?></td>
						<td class="valign_top"><?php echo $history['status']; ?></td>
						<td class="valign_top"><?php echo $history['comment']; ?></td>
					</tr>
				<?php } ?>
			</table>
		</div>
	<?php } ?>

	<div class="form-group">
		<div class="mt20 mb40">
		    <a href="<?php echo $continue; ?>" class="btn btn-default mr10" title="<?php echo $button_back; ?>">
		    	<i class="fa fa-arrow-left"></i>
		    	<?php echo $button_back ?>
		    </a>
		    <?php echo $this->getHookVar('hk_additional_buttons'); ?>
		    <a href="javascript:window.print();" class="btn btn-orange mr10 pull-right"
		       title="<?php echo $button_print->text ?>">
		    	<i class="<?php echo $button_print->{'icon'}; ?>"></i>
		    	<?php echo $button_print->text ?>
		    </a>
		</div>
	</div>

	</form>
</div>
<?php echo $footer; ?>