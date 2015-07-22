<h1 class="heading1">
  <span class="maintext"><?php echo $heading_title; ?></span>
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

<div class="contentpanel">

    <?php if ($coupon_status) { ?>
    <h4 class="heading4"><?php echo $text_coupon; ?></h4>
	<div class="registerbox">
		<?php echo $form0['form_open']; ?>
		<div class="form-inline">
			<label class="checkbox"><?php echo $entry_coupon; ?></label>
		    <?php echo $form0['coupon']; ?>
		    <?php echo $form0['submit']; ?>
		</div>
		</form>
	</div>
    <?php } ?>

	<?php echo $form['form_open']; ?>

	<?php if( $shipping_methods ) { ?>			
	<div id="active_shippings">
	<h4 class="heading4"><?php echo $text_shipping_method; ?></h4>	
	<p><?php echo $text_shipping_methods; ?></p>		
	<div class="registerbox">		
        <table class="table table-striped table-bordered">
        <?php
	      foreach ($shipping_methods as $shipping_method) { ?>
          <tr>
            <td colspan="3"><b><?php echo $shipping_method['title']; ?></b></td>
          </tr>
          <?php if (!$shipping_method['error']) { ?>
          <?php foreach ($shipping_method['quote'] as $quote) { ?>
			  <tr>
				<td width="5%"><label for="shipping_shipping_method<?php echo $quote['id']; ?>"><?php echo $quote['radio']; ?></label></td>
				<td><label for="shipping_shipping_method<?php echo $quote['id']; ?>" title="<?php echo has_value($quote['description']) ? $quote['description'] : ''; ?>" style="cursor: pointer;">
				<?php $icon = $shipping_method['icon'];
				if ( count ($icon) ) {  ?>
				<?php if ( is_file(DIR_RESOURCE . $icon['image']) ) { ?>
					<span class="shipping_icon mr10"><img src="resources/<?php echo $icon['image']; ?>" title="<?php echo $icon['title']; ?>" /></span>
					<?php } else if (!empty( $icon['resource_code'] )) { ?>
					<span class="shipping_icon mr10"><?php echo $icon['resource_code']; ?></span>
				<?php } } ?>												
				<?php echo $quote['title']; ?>
				</label></td>
				<td class="align_right"><label for="<?php echo $quote['id']; ?>" style="cursor: pointer;"><?php echo $quote['text']; ?></label></td>
			  </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td colspan="3"><div class="alert alert-danger"><i class="fa fa-exclamation"></i> <?php echo $shipping_method['error']; ?></div></td>
          </tr>
          <?php } ?>
        <?php } ?>
        </table>
	</div>
	</div>
	<?php } ?>
	<?php echo $this->getHookVar('shipping_extensions_hook'); ?>

	<?php echo $this->getHookVar('payment_extensions_pre_hook'); ?>
	<?php if( $payment_methods ) { ?>			
	<div style="display: none;" id="active_payments">
		<h4 class="heading4"><?php echo $text_payment_method; ?></h4>	
		<p><?php echo $text_payment_methods; ?></p>		
		<div class="registerbox payment_palce_holder"></div>	
	</div>
	<div style="display: none;" id="hidden_payments">
	        <?php if($payment_methods) { 
	        	foreach ($payment_methods as $ship_name => $payment_methods_per_shipping) { ?>
	        <div class="payment_group <?php echo $ship_name ?>">
	        <table class="table table-striped table-bordered">
	          <?php foreach ($payment_methods_per_shipping as $payment_method) { ?>
	          <tr>
	            <td width="1"><?php echo $payment_method['radio']; ?></td>
	            <td><label for="guest_payment_method<?php echo $payment_method['id']; ?>" style="cursor: pointer;">
				<?php $icon = $payment_method['icon'];
				if ( count ($icon) ) {  ?>
				<?php if ( is_file(DIR_RESOURCE . $icon['image']) ) { ?>
					<span class="payment_icon mr10"><img src="resources/<?php echo $icon['image']; ?>" title="<?php echo $icon['title']; ?>" /></span>
					<?php } else if (!empty( $icon['resource_code'] )) { ?>
					<span class="payment_icon mr10"><?php echo $icon['resource_code']; ?></span>
				<?php } } ?>									            
	            <?php echo $payment_method['title']; ?>
	            </label></td>
	          </tr>
	          <?php } ?>
	        </table>
	        </div>
	        <?php } } ?>		          
	</div>	
	<?php } ?>

	<?php echo $this->getHookVar('payment_extensions_hook'); ?>
	<?php echo $this->getHookVar('order_attributes'); ?>

	<h4 class="heading4"><?php echo $text_comments; ?></h4>	
	<div class="registerbox">		
		<div class="content">
			<?php echo $form['comment']; ?>
     	</div>
		
		<div class="form-group">
			<div class="col-md-12 mt20">
    			<?php echo $this->getHookVar('buttons_pre'); ?>
				<?php echo $buttons; ?>
				<?php echo $this->getHookVar('buttons_post'); ?>
    		</div>
		</div>			
	</div>
	
	</form>
</div>

<script type="text/javascript">		
	if ($("input[name=shipping_method]:checked").length > 0) {
		var shp_name = '';
		shp_name = $("input[name=shipping_method]:checked").val().split('.');
		shp_name = shp_name[0];
		show_payment(shp_name);	

	} else if ( $('#active_shippings').length == 0 ) {
		//no shipping at all show all payments
		show_payment('no_shipping');
	}
		
	$('input[name=shipping_method]').click( function(){
		var selection = $(this).val().split('.');
		//hide and unselect other methods. 
		show_payment(selection[0]);
	} );	
	
	function show_payment( shp_name ) {
		$('#active_payments').show();
		$('.payment_palce_holder').html('');
		$('.payment_palce_holder').html( $('#hidden_payments .'+shp_name).html() );
	}
</script>