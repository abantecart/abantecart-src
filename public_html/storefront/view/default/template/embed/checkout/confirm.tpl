<?php echo $head; ?>
<h1 class="heading1">
	<span class="maintext"><i class="fa fa-pushpin"></i> <?php echo $heading_title; ?></span>
	<span class="subtext"></span>
</h1>

<?php if ($success) { ?>
	<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<?php echo $success; ?>
	</div>
<?php } ?>

<?php if ($error_warning) { ?>
	<div class="alert alert-error alert-danger">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<?php echo $error_warning; ?>
	</div>
<?php } ?>

<div id="returnPolicyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="returnPolicyModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="returnPolicyModalLabel"><?php echo $text_accept_agree_href_link; ?></h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_close; ?></button>
	</div>
</div>
</div>  
</div>

<div class="contentpanel">
	<?php echo $form['form_open']; ?>
	<p><?php echo $text_accept_agree ?><a onclick="openModalRemote('#returnPolicyModal', '<?php echo $text_accept_agree_href; ?>'); return false;"
				href="<?php echo $text_accept_agree_href; ?>"><b><?php echo $text_accept_agree_href_link; ?></b></a></p>


	<?php if ($shipping_method) { ?>
		<h4 class="heading4"><?php echo $text_shipping; ?></h4>
		<table class="table confirm_shippment_options">
			<tr>
				<td class="align_left"><?php echo $shipping_firstname . ' ' . $shipping_lastname; ?>
					<br/><?php echo $telephone; ?></td>
				<td class="align_left">
					<address>
					<?php echo $shipping_address_1 . ' ' . $shipping_address_2; ?><br/>
					<?php echo $shipping_city . ' ' . $shipping_zone . ' ' . $shipping_postcode; ?><br/>
					<?php echo $shipping_country ?>
					</address>
				</td>
				<td class="align_left"><?php echo $shipping_method; ?></td>
				<td class="align_right">
					<a class="btn btn-default btn-xs" href="<?php echo $checkout_shipping_edit; ?>">
						<i class="fa fa-edit"></i>
						<?php echo $text_edit_shipping; ?>
					</a>
				</td>
			</tr>
		</table>
	<?php } ?>

	<?php
	if ($payment_method || $balance || $this->getHookVar('payment_method')) { ?>

		<h4 class="heading4"><?php echo $text_payment; ?></h4>

		<table class="table confirm_payment_options">

			<?php if ($payment_method) { ?>
				<tr>
					<td class="align_left"><?php echo $payment_firstname . ' ' . $payment_lastname; ?>
						<br/><?php echo $telephone; ?></td>
					<td class="align_left">
						<address>
						<?php echo $payment_address_1 . ' ' . $payment_address_2; ?><br/>
						<?php echo $payment_city . ' ' . $payment_zone . ' ' . $payment_postcode; ?><br/>
						<?php echo $payment_country ?>
						</address>
					</td>
					<td class="align_left"><?php echo $payment_method; ?></td>
					<td class="align_right">
						<a class="btn btn-default btn-xs" href="<?php echo $checkout_payment_edit; ?>">
							<i class="fa fa-edit"></i>
							<?php echo $text_edit_payment; ?>
						</a>
						<a class="btn btn-default btn-xs" href="<?php echo $checkout_payment_edit; ?>">
							<i class="fa fa-check-square-o"></i>
							<?php echo $text_add_coupon; ?>
						</a>
					</td>
				</tr>
			<?php }
			if($balance){?>
				<tr>
					<td class="align_left"><?php echo $balance;?></td>
					<td class="align_left">&nbsp;</td>
					<td class="align_left">&nbsp;</td>
					<td class="align_right">
						<?php if($disapply_balance){ ?>
						<a class="btn btn-default btn-xs" href="<?php echo $disapply_balance['href']; ?>">
							<i class="fa fa-edit"></i>
							<?php echo $disapply_balance['text']; ?>
						</a>
						<?php }?>
					</td>
				</tr>

			<?php }
			if($this->getHookVar('payment_method')){?>
				<tr>
					<td class="align_left"><?php echo $this->getHookVar('payment_method_title');?></td>
					<td class="align_left">&nbsp;</td>
					<td class="align_left">&nbsp;</td>
					<td class="align_right"><?php echo $this->getHookVar('payment_method'); ?></td>
				</tr>
			<?php }	?>

		</table>
	<?php } ?>

	<h4 class="heading4"><?php echo $text_cart_items; ?>
		<a class="pull-right mr10 btn btn-default btn-xs" href="<?php echo $cart; ?>">
			<i class="fa fa-shopping-cart"></i>
			<?php echo $text_edit_basket; ?>
		</a>
	</h4>
	<table class="table confirm_products">
		<?php foreach ($products as $product) { ?>
			<tr>
				<td><a href="<?php echo $product['href']; ?>"><?php echo $product['thumb']['thumb_html']; ?></a></td>
				<td><a href="<?php echo $product['href']; ?>"
					   class="checkout_heading"><?php echo $product['name']; ?></a>
					<?php foreach ($product['option'] as $option) { ?>
						<br/>
						&nbsp;
						<small title="<?php echo $option['title']?>"> - <?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
					<?php } ?></td>
				<td><?php echo $product['price']; ?></td>
				<td><?php echo $product['quantity']; ?></td>
				<td class="checkout_heading"><?php echo $product['total']; ?></td>
			</tr>
		<?php } ?>
		<?php echo $this->getHookVar('list_more_product_last'); ?>
	</table>

	<?php if ($comment) { ?>
		<h4 class="heading4"><?php echo $text_comment; ?></h4>
		<div class="container mb10"><?php echo $comment; ?></div>
	<?php } ?>

	<?php echo $this->getHookVar('order_attributes'); ?>

	<div class="confirm_total">
	
		<div class="cart-info col-md-5">
			<table class="table table-striped table-bordered">
			    <?php
			    foreach ($totals as $total) { ?>
			    	<tr>
			    		<td>
			    			<span class="extra bold <?php if ($total['id'] == 'total') echo 'totalamout'; ?>"><?php echo $total['title']; ?></span>
			    		</td>
			    		<td>
			    			<span class="bold <?php if ($total['id'] == 'total') echo 'totalamout'; ?>"><?php echo $total['text']; ?></span>
			    		</td>
			    	</tr>
			    <?php } ?>
			</table>
		</div>
		
		<div class="col-md-6 col-md-offset-1 payment_confirmation mt10">
			<?php echo $this->getHookVar('payment_pre'); ?>
			<div id="payment"><?php echo $payment; ?></div>
			<?php echo $this->getHookVar('payment_post'); ?>	
		</div>
		
	</div>

</div>
<?php echo $footer; ?>