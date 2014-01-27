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

<div class="container-fluid">
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
			<div class="control-group">
				<label class="control-label"><?php echo $entry_default; ?></label>
				<div class="controls">
				    <?php echo $form['default']; ?>
				</div>
			</div>		
	
			<?php echo $this->getHookVar('address_edit_sections'); ?>
			<div class="control-group">
				<div class="controls">
					<div class="span4 mt20 mb20">
						<button class="btn btn-orange pull-right" title="<?php echo $form['submit']->name ?>" type="submit">
						    <i class="<?php echo $form['submit']->{'icon'}; ?> icon-white"></i>
						    <?php echo $form['submit']->name ?>
						</button>
						<a href="<?php echo $back; ?>" class="btn mr10" title="<?php echo $form['back']->text ?>">
						    <i class="<?php echo $form['back']->{'icon'}; ?>"></i>
						    <?php echo $form['back']->text ?>
						</a>
					</div>	
				</div>
			</div>
			
		</fieldset>
	</div>
</div>


<script type="text/javascript"><!--

$('#AddressFrm_country_id').change(function() {
    $('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id=' + $(this).val() + '&zone_id=<?php echo $zone_id; ?>');
});
$('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id=' + $('#AddressFrm_country_id').val() + '&zone_id=<?php echo $zone_id; ?>');
//--></script>