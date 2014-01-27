<div id="content">
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
    <?php if ($coupon_status) { ?>
    <div class="content">
	  <?php echo $form0['form_open']; ?>
        <div style="float:left;"><p><?php echo $text_coupon; ?></p></div>
        <div style="text-align: right;"><?php echo $entry_coupon; ?>&nbsp;
        <?php echo $form0['coupon'].' &nbsp;'. $form0['submit']; ?></div>
      </form>
    </div>
    <?php }
	  echo $form['form_open'];
	  ?>
      <?php if ($shipping_methods) { ?>
      <div id="active_shippings">
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_shipping_method; ?></b>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
        <p><?php echo $text_shipping_methods; ?></p>
        <table width="536" cellpadding="3">
          <?php foreach ($shipping_methods as $shipping_method) { ?>
          <tr>
            <td colspan="3"><b><?php echo $shipping_method['title']; ?></b></td>
          </tr>
          <?php if (!$shipping_method['error']) { ?>
           <?php foreach ($shipping_method['quote'] as $quote) { ?>
			  <tr>
				<td width="1"><label for="guest_shipping_method<?php echo $quote['id']; ?>"><?php echo $quote['radio']; ?></label></td>
				<td width="534"><label for="guest_shipping_method<?php echo $quote['id']; ?>" title="<?php echo has_value($quote['description']) ? $quote['description'] : ''; ?>" style="cursor: pointer;">
				<?php $icon = $shipping_method['icon'];
				if ( count ($icon) ) {  ?>
				<?php if ( is_file(DIR_RESOURCE . $icon['image']) ) { ?>
					<span class="shipping_icon mr10"><img src="resources/<?php echo $icon['image']; ?>" title="<?php echo $icon['title']; ?>" />&nbsp;&nbsp;</span>
					<?php } else if (!empty( $icon['resource_code'] )) { ?>
					<span class="shipping_icon mr10"><?php echo $icon['resource_code']; ?>&nbsp;&nbsp;</span>
				<?php } } ?>												
				<?php echo $quote['title']; ?></label></td>
				<td width="1" align="right"><label for="<?php echo $quote['id']; ?>" style="cursor: pointer;"><?php echo $quote['text']; ?></label></td>
			  </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td colspan="3"><div class="error"><?php echo $shipping_method['error']; ?></div></td>
          </tr>
          <?php } ?>
          <?php } ?>
        </table>
      </div>
      </div>
      <?php } ?>
	  <?php echo $this->getHookVar('payment_extensions_pre_hook'); ?>
      <?php if ($payment_methods) { ?>
      <div style="display: none;" id="active_payments">
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_payment_method; ?></b>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
        <p><?php echo $text_payment_methods; ?></p>
		<div class="payment_palce_holder"></div>
      </div>
      </div>
      <?php } ?>

	  <?php echo $this->getHookVar('payment_extensions_hook'); ?>
	  <?php echo $this->getHookVar('order_attributes'); ?>

      <b style="margin-bottom: 2px; display: block;"><?php echo $text_comments; ?></b>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
        <?php echo $form['comment']; ?>
      </div>
      <div class="buttons">
        <table>
          <tr>
            <td align="left"><?php echo $form[ 'back' ]; ?></td>
			<?php if ($text_agree) { ?>
				<td align="right" style="padding-right: 5px;"><?php echo $text_agree; ?><a class="thickbox" href="<?php echo $text_agree_href; ?>"><b><?php echo $text_agree_href_text; ?></b></a></td>
				<td width="5" style="padding-right: 10px;"><?php echo $form[ 'agree' ]; ?></td>
			<?php } ?>
			<td align="right" width="5"><?php echo $form[ 'continue' ]; ?></td>
          </tr>
        </table>
      </div>
    </form>
  </div>
  <div class="bottom">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center"></div>
  </div>
</div>

<div style="display: none;" id="hidden_payments">
        <?php if($payment_methods) { foreach ($payment_methods as $ship_name => $payment_methods_per_shipping) { ?>
        <div class="payment_group <?php echo $ship_name ?>">
        <table width="536" cellpadding="3">
          <?php foreach ($payment_methods_per_shipping as $payment_method) { ?>
          <tr>
            <td width="1"><?php echo $payment_method['radio']; ?></td>
            <td><label for="guest_payment_method<?php echo $payment_method['id']; ?>" style="cursor: pointer;">
				<?php $icon = $payment_method['icon'];
				if ( count ($icon) ) {  ?>
				<?php if ( is_file(DIR_RESOURCE . $icon['image']) ) { ?>
					<span class="payment_icon mr10"><img src="resources/<?php echo $icon['image']; ?>" title="<?php echo $icon['title']; ?>" />&nbsp;&nbsp;</span>
					<?php } else if (!empty( $icon['resource_code'] )) { ?>
					<span class="payment_icon mr10"><?php echo $icon['resource_code']; ?>&nbsp;&nbsp;</span>
				<?php } } ?>									                        
            <?php echo $payment_method['title']; ?></label></td>
          </tr>
          <?php } ?>
        </table>
        </div>
          <?php } } ?>
</div>

<script type="text/javascript">
	$('#guest_back').click( function(){
		location = '<?php echo $back; ?>';
	} );
		
	if ($("input[name=shipping_method]:checked").length > 0) {
		var shp_name = '';
		shp_name = $("input[name=shipping_method]:checked").val().split('.');
		shp_name = shp_name[0];
		show_payment(shp_name);	

	} else if ( $('#active_shippings').length == 0 ) {
		//no shipping at all show all payments
		show_payment('no_shipping');
	}
		
	$('.radio_element input:[name=shipping_method]').click( function(){
		var selection = $(this).val().split('.');
		//hide and unselect other methods. 
		show_payment(selection[0]);
	} );	
	
	function show_payment( shp_name ) {
		if(<?php echo $order_totals['total'];?><=0){return;}
		$('#active_payments').show();
		$('.payment_palce_holder').html('');
		$('.payment_palce_holder').html( $('#hidden_payments .'+shp_name).html() );
	}
</script>