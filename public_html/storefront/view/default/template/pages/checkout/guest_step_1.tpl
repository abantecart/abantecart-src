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
	<?php echo $form['form_open']; ?>

	<h4 class="heading4"><?php echo $text_your_details; ?></h4>
	<div class="registerbox form-horizontal">
		<fieldset>
		<?php
		foreach ($form['fields']['general'] as $field_name=>$field) { ?>
			<div class="form-group <?php if (${'error_'.$field_name}) echo 'has-error'; ?>">
				<label class="control-label col-md-4"><?php echo ${'entry_'.$field_name}; ?></label>
				<div class="input-group col-md-6">
				    <?php echo $field; ?>
				</div>
				<span class="help-block"><?php echo ${'error_'.$field_name}; ?></span>
			</div>		
		<?php }
		echo $this->getHookVar('guest_details_attributes'); ?>
		</fieldset>
	</div>	

	<h4 class="heading4"><?php echo $text_your_address; ?></h4>
	<div class="registerbox form-horizontal">
		<fieldset>
		<?php
			$field_list = array('company' => 'company', 
								'address_1' => 'address_1', 
								'address_2' => 'address_2', 
								'city' => 'city',
								'zone' => 'zone_id',
								'postcode' => 'postcode',
								'country' => 'country_id', 
								);
			
			foreach ($form['fields']['address'] as $field_name=>$field) {?>
			<div class="form-group <?php if (${'error_'.$field_name}) echo 'has-error'; ?>">
				<label class="control-label col-md-4"><?php echo ${'entry_'.$field_name}; ?></label>
				<div class="input-group col-md-6">
				    <?php echo $field; ?>
				</div>
				<span class="help-block"><?php echo ${'error_'.$field_name}; ?></span>
			</div>		
		<?php }
			echo $this->getHookVar('address_entry_section'); ?>

			<div class="form-group">
				<label class="control-label col-md-4"></label>
				<div class="input-group col-md-6">
				    <?php echo $form['shipping_indicator']; ?>
				</div>
			</div>		
		</fieldset>
	</div>
			
	<!-- start shipping address -->
	<div id="shipping_details" style="<?php echo ($shipping_addr) ? 'display:block;' : 'display:none;' ?>">
	<h4 class="heading4"><?php echo $text_shipping_address; ?></h4>
		<div class="registerbox form-horizontal">
		<fieldset>
		<?php
			foreach ($form['fields']['shipping'] as $field_name=>$field) {
				$entry_text = str_replace('shipping_','', $field_name);
				?>
			<div class="form-group <?php if (${'error_'.$field_name}) echo 'has-error'; ?>">
				<label class="control-label col-md-4"><?php echo ${'entry_'.$entry_text}; ?></label>
				<div class="input-group col-md-6">
					<?php
				   		echo $field;
				   	?>
				</div>
				<span class="help-block"><?php echo ${'error_'.$field_name}; ?></span>
			</div>		
		<?php
			}
		?>	
		</fieldset>
		</div>      
	</div>
	<!-- end shipping address -->     
      
	<div class="form-group">
	    <div class="col-md-12 mt20">
	    	<button class="btn btn-orange pull-right lock-on-click" title="<?php echo $form['continue']->name ?>" type="submit">
	    	    <i class="fa fa-arrow-right"></i>
	    	    <?php echo $form['continue']->name ?>
	    	</button>
	    	<a href="<?php echo $back; ?>" class="btn btn-default mr10" title="<?php echo $form['back']->text ?>">
	    	    <i class="fa fa-arrow-left"></i>
	    	    <?php echo $form['back']->text ?>
	    	</a>
	    </div>
	</div>
	</form>
</div>

<script type="text/javascript">

$('#guestFrm_shipping_indicator').change( function(){
	(this.checked) ? $('#shipping_details').show() : $('#shipping_details').hide();
});
<?php $cz_url = $this->html->getURL('common/zone', '&zone_id='. $zone_id); ?>
$('#guestFrm_country_id').change(function() {
    $('select[name=\'zone_id\']').load('<?php echo $cz_url;?>&country_id=' + $(this).val());
});
$('select[name=\'zone_id\']').load('<?php echo $cz_url;?>&country_id='+$('#guestFrm_country_id').val());

<?php $cz_url = $this->html->getURL('common/zone', '&zone_id='. $shipping_zone_id); ?>
$('#guestFrm_shipping_country_id').change(function() {
	$('select[name=\'shipping_zone_id\']').load('<?php echo $cz_url;?>&country_id=' + $(this).val());
});
$('select[name=\'shipping_zone_id\']').load('<?php echo $cz_url;?>&country_id='+$('#guestFrm_shipping_country_id').val());

</script>