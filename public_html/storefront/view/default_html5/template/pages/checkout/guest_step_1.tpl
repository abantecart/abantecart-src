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
<div class="alert alert-error">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $error_warning; ?>
</div>
<?php } ?>

<div class="container-fluid">
	<?php echo $form['form_open']; ?>

	<h4 class="heading4"><?php echo $text_your_details; ?></h4>
	<div class="registerbox form-horizontal">
		<fieldset>
		<?php
			$field_list = array();
			array_push($field_list, 'firstname', 'lastname', 'email', 'telephone', 'fax');
			
			foreach ($field_list as $field_name) {
		?>
			<div class="control-group <?php if (${'error_'.$field_name}) echo 'error'; ?>">
				<label class="control-label"><?php echo ${'entry_'.$field_name}; ?></label>
				<div class="controls">
				    <?php echo $form[$field_name]; ?>
					<span class="help-inline"><?php echo ${'error_'.$field_name}; ?></span>
				</div>
			</div>		
		<?php
			}
		?>	
		
		<?php echo $this->getHookVar('guest_details_attributes'); ?>
	
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
								'postcode' => 'postcode',
								'country' => 'country_id', 
								'zone' => 'zone_id',
								);
			
			foreach ($field_list as $field_name => $field_id) {
		?>
			<div class="control-group <?php if (${'error_'.$field_name}) echo 'error'; ?>">
				<label class="control-label"><?php echo ${'entry_'.$field_name}; ?></label>
				<div class="controls">
				    <?php echo $form[$field_id]; ?>
					<span class="help-inline"><?php echo ${'error_'.$field_name}; ?></span>
				</div>
			</div>		
		<?php
			}
		?>	
	
			<?php echo $this->getHookVar('address_entry_section'); ?>

			<div class="control-group">
				<div class="controls">
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
			$field_list = array('firstname' => 'shipping_firstname',
								'lastname' => 'shipping_lastname',
								'company' => 'shipping_company', 
								'address_1' => 'shipping_address_1', 
								'address_2' => 'shipping_address_2', 
								'city' => 'shipping_city',
								'postcode' => 'shipping_postcode',
								'country' => 'shipping_country', 
								'zone' => 'shipping_zone',
								);
			
			foreach ($field_list as $field_name => $field_id) {
		?>
			<div class="control-group <?php if (${'error_'.$field_id}) echo 'error'; ?>">
				<label class="control-label"><?php echo ${'entry_'.$field_name}; ?></label>
				<div class="controls">
					<?php if ($field_name == 'country' || $field_name == 'zone') {
						echo $form[$field_id."_id"]; 
					} else { 
				   		echo $form[$field_id]; 
				   	}
				   	?>
					<span class="help-inline"><?php echo ${'error_'.$field_id}; ?></span>
				</div>
			</div>		
		<?php
			}
		?>	
		</fieldset>
		</div>      
	</div>
	<!-- end shipping address -->     
      
	<div class="control-group">
	    <div class="controls">
	    	<div class="mt20 mb20">
	    		<button class="btn btn-orange pull-right" title="<?php echo $form['continue']->name ?>" type="submit">
	    		    <i class="icon-arrow-right icon-white"></i>
	    		    <?php echo $form['continue']->name ?>
	    		</button>
	    		<a href="<?php echo $back; ?>" class="btn mr10" title="<?php echo $form['back']->text ?>">
	    		    <i class="icon-arrow-left"></i>
	    		    <?php echo $form['back']->text ?>
	    		</a>
	    	</div>	
	    </div>
	</div>
	</form>
</div>

<script type="text/javascript"><!--

$('#guestFrm_shipping_indicator').change( function(){
	(this.checked) ? $('#shipping_details').show() : $('#shipping_details').hide();
});

$('#guestFrm_country_id').change(function() {
    $('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id=' + $(this).val() + '&zone_id=<?php echo $zone_id; ?>');
});
$('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id='+$('#guestFrm_country_id').val()+'&zone_id=<?php echo $zone_id; ?>');

$('#guestFrm_shipping_country_id').change(function() {
	$('select[name=\'shipping_zone_id\']').load('index.php?rt=common/zone&country_id=' + $(this).val() + '&zone_id=<?php echo $shipping_zone_id; ?>');
});
$('select[name=\'shipping_zone_id\']').load('index.php?rt=common/zone&country_id='+$('#guestFrm_shipping_country_id').val()+'&zone_id=<?php echo $shipping_zone_id; ?>');
//--></script>