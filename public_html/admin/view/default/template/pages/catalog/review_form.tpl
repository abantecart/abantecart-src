<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group">
				<a class="btn btn-white tooltips back-to-grid hidden" data-table-id="review_grid"
                   href="<?php echo $list_url; ?>" data-toggle="tooltip" data-original-title="<?php echo $text_back_to_list; ?>">
					<i class="fa fa-arrow-left fa-lg"></i>
				</a>
			</div>
            <?php if($preview){ ?>
			<a href="<?php echo $preview; ?>" class="btn btn-small btn-default" target="_new"><i class="fa fa-external-link"></i> <?php echo $text_view; ?></a>
            <?php } ?>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>
	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php foreach ($form['fields'] as $name => $field) {
		//Logic to calculate fields width
		$widthcasses = "col-sm-7";
		if (is_int(stripos($field->style, 'large-field'))) {
			$widthcasses = "col-sm-7";
		} else if (is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date'))) {
			$widthcasses = "col-sm-5";
		} else if (is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch'))) {
			$widthcasses = "col-sm-3";
		} else if (is_int(stripos($field->style, 'tiny-field'))) {
			$widthcasses = "col-sm-2";
		}
		$widthcasses .= " col-xs-12";
		?>
		<div class="form-group <?php if (!empty($error[$name])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-3 col-xs-12"
				   for="<?php echo $field->element_id; ?>"><?php
                if ($name === 'author' && $customerUrl) {
                    echo '<a href="'.$customerUrl.'" target="_blank">'.${'entry_' . $name}.'</a>';
                } else {
                    echo ${'entry_'.$name};
				} ?>
            </label>

			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '') ?>">
				<?php echo $field; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
		<?php } ?><!-- <div class="fieldset"> -->
	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $cancel; ?>">
			<i class="fa fa-arrow-left fa-fw"></i> <?php echo $form['cancel']->text; ?>
			</a>
		</div>
	</div>
	</form>

</div><!-- <div class="tab-content"> -->
