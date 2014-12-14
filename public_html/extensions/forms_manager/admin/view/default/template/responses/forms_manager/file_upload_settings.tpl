<?php
$arr = array(
		'entry_allowed_extensions'=>'extensions',
		'entry_min_size'=>'min_size',
		'entry_max_size'=>'max_size',
		'entry_upload_dir'=>'directory');
foreach( $arr as $e=>$name ){
	$entry = $$e;
	$field = $form['settings_fields'][$name];
?>
	<div class="form-group <?php if (!empty($error[$name])) {
		echo "has-error";
	} ?>">
		<label class="control-label col-md-6"
			   for="<?php echo $field->element_id; ?>"><?php echo $entry; ?></label>

		<div class="input-group input-group-sm afield col-sm-6 col-xs-12 ">
			<?php echo $field;?>
		</div>
		<?php if (!empty($error[$name])) { ?>
		<span class="help-block field_err"><?php echo $error[$name]; ?></span>
		<?php } ?>
	</div>
<?php } ?>


