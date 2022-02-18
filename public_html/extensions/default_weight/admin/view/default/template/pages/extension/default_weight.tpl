<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $extension_summary;
echo $tabs;?>
<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">

		<label class="h4 heading"><?php echo $form_title; ?></label>
			<?php foreach ($form['fields'] as $name => $field) {
			if($name=='rates'){ continue;}
			?>
			<?php
				//Logic to calculate fields width
				$widthcasses = "col-sm-7";
				if ( is_int(stripos($field->style, 'large-field')) ) {
					$widthcasses = "col-sm-7";
				} else if ( is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date')) ) {
					$widthcasses = "col-sm-5";
				} else if ( is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch')) ) {
					$widthcasses = "col-sm-3";
				} else if ( is_int(stripos($field->style, 'tiny-field')) ) {
					$widthcasses = "col-sm-2";
				}
				$widthcasses .= " col-xs-12";
		 ?>

		<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
				<?php echo $field; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
		<?php }

		// rates for locations
		$field_names = array('status','rate');
		foreach($locations as $location){
			$rate = 'default_weight_' . $location['location_id'] . '_rate';
			$status = 'default_weight_' . $location['location_id'] . '_status';
			?>
			<label class="h4 heading"><?php echo $location['name']; ?></label>
			<div class="form-group">
				<label class="control-label col-sm-3 col-xs-12" for="editFrm_<?php echo $status; ?>"><?php echo $entry_status; ?></label>
				<div class="input-group afield col-sm-3 col-xs-12">
					<?php echo $form['fields']['rates'][$status]; ?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3 col-xs-12" for="editFrm_<?php echo $rate; ?>"><?php echo $entry_rate; ?></label>
				<div class="input-group afield col-sm-3 col-xs-12 ml_ckeditor">
					<?php echo $form['fields']['rates'][$rate]; ?>
				</div>
			</div>

		<?php } ?>

	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary">
			<i class="fa fa-save fa-fw"></i> <?php echo $button_save; ?>
			</button>
			<button class="btn btn-default" type="reset">
			<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $cancel; ?>">
			<i class="fa fa-arrow-left fa-fw"></i> <?php echo $button_cancel; ?>
			</a>
		</div>
	</div>
	</form>

</div>
