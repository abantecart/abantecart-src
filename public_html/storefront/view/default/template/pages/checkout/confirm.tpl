<div id="content">
<div class="checkout_confirm">


  <div class="top">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center">
      <h1><?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="middle">
    <?php if ($success) { ?>
    <div class="success alert alert-success"><?php echo $success; ?></div>
    <?php } ?>
    <?php if ($error_warning) { ?>
    <div class="warning alert alert-error"><?php echo $error_warning; ?></div>
    <?php } ?>
	<?php echo $text_accept_agree ?><a class="thickbox" href="<?php echo $text_accept_agree_href; ?>"><b><?php echo $text_accept_agree_href_link; ?></b></a>
	<div class="gray_separator"></div>

	<?php if ($shipping_method) { ?>
	<table width="100%">
        <tr>
          <td align="left" valign="top" class="checkout_heading"><?php echo $text_shipping; ?></td>
          <td align="right" valign="top"><a href="<?php echo $checkout_shipping_edit; ?>"><?php echo $text_edit_shipping; ?></a></td>
	    </tr>
		<tr>
          <td colspan="2" align="left" valign="top">
	         <table width="100%" cellpadding="10">
				<tr>
				  <td align="left" valign="top"><?php echo $shipping_firstname . ' ' . $shipping_lastname; ?><br/><?php echo $telephone; ?></td>
				  <td align="left" valign="top">
					<?php echo $shipping_address_1 .' '.$shipping_address_2; ?><br/>
					<?php echo $shipping_city .' '.$shipping_zone.' '.$shipping_postcode; ?><br/>
					<?php echo $shipping_country ?>
				  </td>
				  <td align="left" valign="top" width="20%"><?php echo $shipping_method; ?></td>
				</tr>
		     </table>
          </td>
	    </tr>
	</table>
	<div class="gray_separator"></div>
	<?php } ?>
<?php  if ($payment_method || $balance || $this->getHookVar('payment_method')) { ?>
	<table width="100%">
		<?php if($payment_method){?>
        <tr>
          <td align="left" valign="top" class="checkout_heading"><?php echo $text_payment; ?></td>
          <td align="right" valign="top"><a href="<?php echo $checkout_payment_edit; ?>"><?php echo $text_edit_payment; ?></a></td>
	    </tr>
		<tr>
          <td colspan="2" align="left" valign="top">
	         <table width="100%" cellpadding="10">
				<tr>
				  <td align="left" valign="top"><?php echo $payment_firstname . ' ' . $payment_lastname; ?><br/><?php echo $telephone; ?></td>
				  <td align="left" valign="top">
					<?php echo $payment_address_1 .' '.$payment_address_2; ?><br/>
					<?php echo $payment_city .' '.$payment_zone.' '.$payment_postcode; ?><br/>
					<?php echo $payment_country ?>
				  </td>
				  <td align="left" valign="top" width="20%"><?php echo $payment_method; ?></td>
				</tr>
		     </table>
          </td>
	    </tr>
		<?php }
		if($balance){?>

			<tr>
				<td align="left"><?php echo $balance;?></td>
				<td align="right">
					<?php if($disapply_balance){ ?>
					<a class="btn btn-mini" href="<?php echo $disapply_balance['href']; ?>">
						<?php echo $disapply_balance['text']; ?>
					</a>
					<?php }?>
				</td>
			</tr>

		<?php }

		if($this->getHookVar('payment_method')){?>
			<tr>
				<td align="left"><?php echo $this->getHookVar('payment_method_title');?></td>
				<td align="left">&nbsp;</td>
				<td align="right"><?php echo $this->getHookVar('payment_method'); ?></td>
			</tr>
		<?php }	?>

	</table>
	<div class="gray_separator"></div>
	  <?php } ?>
	<table width="100%">
        <tr>
          <td align="left" valign="top" class="checkout_heading"><?php echo $text_cart_items; ?></td>
          <td align="right" valign="top"><a href="<?php echo $cart; ?>"><?php echo $text_edit_basket; ?></a>&nbsp;<img src="<?php echo $this->templateResource('/image/icon_cart.png'); ?>" align="absmiddle" /></td>
	    </tr>
		<tr>
          <td colspan="2" align="left" valign="top">

				<table width="100%">
					<?php foreach ($products as $product) { ?>
					<tr>
					  <td align="left" valign="top"><a href="<?php echo $product['href']; ?>"><?php echo $product['thumb']['thumb_html']; ?></a></td>
					  <td align="left" valign="top"><a href="<?php echo $product['href']; ?>" class="checkout_heading"><?php echo $product['name']; ?></a>
						<?php foreach ($product['option'] as $option) { ?>
						<br />
						&nbsp;<small> - <?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
						<?php } ?></td>
					  <td align="right" valign="top"><?php echo $product['price']; ?></td>
					  <td align="right" valign="top"><?php echo $product['quantity']; ?></td>
					  <td align="right" valign="top" class="checkout_heading"><?php echo $product['total']; ?></td>
					</tr>
					<tr><td colspan="5"><div class="gray_separator"></div></td></tr>
					<?php } ?>
					<?php echo $this->getHookVar('list_more_product_last'); ?>
				</table>
          </td>
	    </tr>
	</table>
    <table cellpadding="0" cellspacing="0" width="100%">
         <?php foreach ($totals as $total) { ?>
          <tr class="checkout_heading">
              <td align="right"><?php echo $total['title']; ?></td>
              <td align="right"><?php echo $total['text']; ?></td>
          </tr>
          <?php } ?>
      </table>



    <?php if ($comment) { ?>
    <b style="margin-bottom: 2px; display: block;"><?php echo $text_comment; ?></b>
    <div class="content"><?php echo $comment; ?></div>
    <?php } ?>

	<?php echo $this->getHookVar('order_attributes'); ?>

    <?php echo $this->getHookVar('payment_pre'); ?>
    <div id="payment"><?php echo $payment; ?></div>
    <?php echo $this->getHookVar('payment_post'); ?>

  </div>
  <div class="bottom">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center"></div>
  </div>

</div>
</div>