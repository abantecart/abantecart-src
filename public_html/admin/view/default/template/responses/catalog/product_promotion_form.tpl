<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php  echo $form_title; ?></h4>
</div>
<div class="tab-content">
	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<label class="h4 heading"></label>
			<?php foreach ($form['fields'] as $name => $field) { ?>
			<?php
				//Logic to calculate fields width
				$widthclasses = "col-sm-7";
				if ( is_int(stripos($field->style, 'large-field')) ) {
					$widthclasses = "col-sm-7";
				} else if ( is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date')) ) {
					$widthclasses = "col-sm-5";
				} else if ( is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch')) ) {
					$widthclasses = "col-sm-3";
				} else if ( is_int(stripos($field->style, 'tiny-field')) ) {
					$widthclasses = "col-sm-2";
				}
				$widthclasses .= " col-xs-12";
			?>
			<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
				<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
				<div class="input-group afield <?php echo $widthclasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
					<?php echo $field; ?>
				</div>
				<?php if (is_array($error[$name]) && !empty($error[$name])) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
				<?php } else if (!empty($error[$name])) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
				<?php } ?>
			</div>
		<?php }  ?><!-- <div class="fieldset"> -->
	</div>

		<div class="panel-footer">
			<div class="row">
			   <div class="col-sm-6 col-sm-offset-3">
			     <button class="btn btn-primary">
			     <i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
			     </button>&nbsp;
			     <a class="btn btn-default" data-dismiss="modal" href="<?php echo $cancel; ?>">
			     <i class="fa fa-refresh"></i> <?php echo $form['cancel']->text; ?>
			     </a>
			   </div>
			</div>
		</div>
		</form>
</div>
