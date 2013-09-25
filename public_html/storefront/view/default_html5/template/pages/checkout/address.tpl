<h1 class="heading1">
  <span class="maintext"><i class="icon-book"></i> <?php echo $heading_title; ?></span>
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

<div class="container-fluid addresses">
	<section class="formbox old_address">
	<?php if ($addresses) {
	  echo  $form0['form_open'];
	?>
	<h4 class="heading4"><?php echo $text_entries; ?></h4>
		<div class="registerbox form-horizontal">
			<table class="table table-striped">
			<?php foreach ($addresses as $address) { ?>		
				<tr>
					<td class="align_left"><?php echo $address['radio'];?></td>
					<td class="align_left"><label class="control-label inline" for="address_1_address_id<?php echo $address['address_id']; ?>" style="cursor: pointer;"><?php echo $address['address']; ?></label></td>
				</tr>
			<?php } ?>
			</table>
	
			<div class="control-group">
			    <div class="controls">
			    	<button class="btn btn-orange pull-right" title="<?php echo $form0['continue']->name ?>" type="submit">
			    	    <i class="icon-arrow-right icon-white"></i>
			    	    <?php echo $form0['continue']->name ?>
			    	</button>
			    </div>
			</div>						
		</div>		
	</form>
	</section>
	
	<section class="formbox ml10 new_address">
	<?php }
	   echo $form['form_open'];
	?>
	<h4 class="heading4"><?php echo $text_new_address; ?></h4>
	<div class="registerbox form-horizontal">
		<fieldset>
		<?php
			$field_list = array('firstname' => 'firstname',
								'lastname' => 'lastname',
								'company' => 'company', 
								'address_1' => 'address_1', 
								'address_2' => 'address_2', 
								'city' => 'city',
								'postcode' => 'postcode',
								'country' => 'country_id', 
								'zone' => 'zone',
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
			<?php echo $this->getHookVar('new_address_sections'); ?>
			<div class="control-group">
				<div class="controls">
					<button class="btn btn-orange pull-right" title="<?php echo $form['continue']->name ?>" type="submit">
					    <i class="icon-arrow-right icon-white"></i>
					    <?php echo $form['continue']->name ?>
					</button>
				</div>
			</div>				
		</fieldset>	
	</div>	
	</form>
	</section>

</div>

<script type="text/javascript"><!--
$('#Address2Frm_country_id').change(function() {
    $('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id=' + $(this).val() + '&zone_id=<?php echo $zone_id; ?>');
});
$('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id='+$('#Address2Frm_country_id').val()+'&zone_id=<?php echo $zone_id; ?>');
//--></script>