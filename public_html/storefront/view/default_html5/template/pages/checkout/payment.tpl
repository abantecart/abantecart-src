<h1 class="heading1">
  <span class="maintext"><i class="icon-credit-card"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<?php if ($success) { ?>
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $success; ?>
</div>
<?php } ?>

<?php if ($error_warning) { ?>
<div class="alert alert-error">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $error_warning; ?>
</div>
<?php } ?>

<div class="container-fluid">

	<h4 class="heading4"><?php echo $text_payment_address; ?></h4>
	<div class="registerbox">
		<table class="table table-striped table-bordered">
		<?php echo $this->getHookVar('payment_extensions_pre_address_hook'); ?>
		<tr>
			<td><?php echo $address; ?></td>
			<td>
			<div class="control-group">
				<label class="control-label"><?php echo $text_payment_to; ?></label>
				<div class="controls">
					<a href="<?php echo $change_address_href; ?>" class="btn mr10" title="<?php echo $button_change_address ?>">
					    <i class="icon-edit"></i>
					    <?php echo $button_change_address ?>
					</a>				
				</div>
			</div>									
			</td>
		</tr>
		<?php echo $this->getHookVar('payment_extensions_post_address_hook'); ?>
		</table>		
	</div>

    <?php 
	if ($coupon_status) {
		echo $coupon_form;
	}
    if ($balance) { ?>
		<h4 class="heading4"><?php echo $text_balance; ?></h4>
    	<div class="registerbox">
			<table class="table table-striped table-bordered">
				<tr><td><?php echo $balance;?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $apply_balance_button; ?></td></tr>
			</table>
		</div>

    <?php } ?>

	<?php echo $this->getHookVar('payment_extensions_pre_hook'); ?>

	<?php echo $form['form_open'];?>	

	<?php if( $payment_methods ) { ?>			
	<h4 class="heading4"><?php echo $text_payment_method; ?></h4>	
	<p><?php echo $text_payment_methods; ?></p>		
	<div class="registerbox">		
        <table class="table table-striped table-bordered">
			<?php echo $this->getHookVar('payment_extensions_pre_payments_hook'); ?>
			<?php foreach ($payment_methods as $payment_method) { ?>
			<tr>
			  <td style="width:1px;"><?php echo $payment_method['radio']; ?></td>
			  <td><label for="payment_payment_method<?php echo $payment_method['id']; ?>" style="cursor: pointer;">
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
			<?php echo $this->getHookVar('payment_extensions_post_payments_hook'); ?>
		</table>
	</div>
	<?php } ?>
	
	<?php echo $this->getHookVar('payment_extensions_hook'); ?>
	
	<?php echo $this->getHookVar('order_attributes'); ?>
	
	<h4 class="heading4"><?php echo $text_comments; ?></h4>	
	<div class="registerbox">		
		<div class="content">
			<?php echo $form['comment']; ?>
     	</div>
		
		<div class="control-group">
			<div class="controls">
    			<div class="form-inline mt20 mb20">
    			<?php echo $this->getHookVar('buttons_pre'); ?>
				<?php echo $buttons; ?>
				<?php echo $this->getHookVar('buttons_post'); ?>
    			</div>	
    		</div>
		</div>			
	</div>
	
	</form>
		
</div>
