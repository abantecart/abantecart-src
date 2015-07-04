<?php echo $head; ?>
<h1 class="heading1">
  <span class="maintext"><i class="fa fa-book"></i> <?php echo $heading_title; ?></span>
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
	<h4 class="heading4"><?php echo $text_edit_address; ?></h4>
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
								'zone' => 'zone_id',
								);
			
			foreach ($field_list as $field_name => $field_id) {
		?>
			<div class="form-group <?php if (${'error_'.$field_name}) echo 'has-error'; ?>">
				<label class="control-label col-sm-4 col-md-4"><?php echo ${'entry_'.$field_name}; ?></label>
				<div class="input-group col-sm-6 col-md-4">
				    <?php echo $form[$field_id]; ?>
				</div>
				<span class="help-block"><?php echo ${'error_'.$field_name}; ?></span>
			</div>		
		<?php
			}
		?>	
			<div class="form-group">
				<label class="control-label col-sm-4 col-md-4"><?php echo $entry_default; ?></label>
				<div class="input-group">
				    <?php echo $form['default']; ?>
				</div>
			</div>		
	
			<?php echo $this->getHookVar('address_edit_sections'); ?>
			<div class="form-group">
				<div class="col-md-12">
					<button class="btn btn-orange pull-right" title="<?php echo $form['submit']->name ?>" type="submit">
					    <i class="<?php echo $form['submit']->{'icon'}; ?>"></i>
					    <?php echo $form['submit']->name ?>
					</button>
					<a href="<?php echo $back; ?>" class="btn btn-default mr10" title="<?php echo $form['back']->text ?>">
					    <i class="<?php echo $form['back']->{'icon'}; ?>"></i>
					    <?php echo $form['back']->text ?>
					</a>
				</div>
			</div>
			
		</fieldset>
	</div>
	</form>
</div>


<script type="text/javascript"><!--
<?php $cz_url = $this->html->getURL('common/zone', '&zone_id='. $zone_id); ?>
$('#AddressFrm_country_id').change(function() {
    $('select[name=\'zone_id\']').load('<?php echo $cz_url;?>&country_id=' + $(this).val());
});
$('select[name=\'zone_id\']').load('<?php echo $cz_url;?>&country_id=' + $('#AddressFrm_country_id').val());
//--></script>

<?php echo $footer; ?>