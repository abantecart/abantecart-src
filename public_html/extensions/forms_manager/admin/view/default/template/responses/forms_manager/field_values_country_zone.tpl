<div class="row">
	<div class="col-md-6 panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $field_data['field_name']; ?></h3>
		</div>
		<div id="field_edit_form" class="panel-body panel-body-nopadding">
			<div class="form-group">
				<label class="heading col-sm-10"><?php echo $text_field_type; ?>: <?php echo $field_type; ?></label>
				<div class="input-group col-sm-2">
                    <?php if(!$field_data['locked']){ ?>
                    <a class=" pull-right btn btn-default tooltips"
                       href="<?php echo $button_remove_field->href; ?>"
                       data-original-title="<?php echo $button_remove_field->text; ?>" data-confirmation="delete">
                        <i class="fa fa-trash-o"></i>
                    </a>
                <?php } ?>
				</div>
			</div>

			<?php
			$fields = [
					'entry_status' => 'status',
					'entry_field_name' => 'field_name',
					'entry_field_description' => 'field_description',
					'entry_field_note' => 'field_note',
                    'entry_field_group' => 'field_group',
					'entry_sort_order' => 'field_sort_order',
					'entry_required' => 'required',
					'entry_icon' => 'icon',
					'entry_html_attributes' => 'field_attributes',
					'entry_regexp_pattern' => 'field_regexp_pattern',
					'entry_error_text' => 'field_error_text'
            ];
        foreach ($fields as $e=>$name) {
			$entry = $$e;
			$field = $$name;
			if(!$field){ continue;}

			if ($name == 'option_placeholder' && !(string)$option_placeholder) {
				continue;
			}
			//Logic to calculate fields width
			$widthCssClasses = "col-sm-6";
            if ( str_contains($field->style, 'small-field') ) {
				$widthCssClasses = "col-sm-3";
			} else if ( str_contains($field->style, 'tiny-field') ) {
				$widthCssClasses = "col-sm-2";
			}
			$widthCssClasses .= " col-xs-12";
?>
			<div class="form-group <?php if (!empty($error[$name])) {
				echo "has-error";
			} ?>">
				<label class="control-label col-md-6" for="<?php echo $field->element_id; ?>">
                    <?php echo $entry; ?>
                </label>
				<div class="input-group input-group-sm afield <?php echo $widthCssClasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '')?>">
					<?php
                    $field->attr .= ' autocomplete="off" ';
                    echo $field;?>
				</div>
				<?php if ($error[$name]) { ?>
				    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
				<?php } ?>
			</div>
			<?php }
            echo $field_settings; ?>
		</div>
        <?php echo $resources_scripts; ?>
		<div class="panel-footer">
			<div class="center">
				<button id="update_field" class="btn btn-primary">
					<i class="fa fa-save"></i> <?php echo $button_save->text; ?>
				</button>
				&nbsp;
				<a id="reset_field" class="btn btn-default" href="<?php echo $button_reset->href; ?>">
					<i class="fa fa-refresh"></i> <?php echo $button_reset->text; ?>
				</a>
			</div>
		</div>
	</div>
	<div class="col-md-6 tab-content">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $text_field_values; ?></h3>
		</div>

		<div class="panel-footer">
			<div class="center">
				<a href="<?php echo $edit_url?>" class="btn btn-primary">
					<i class="fa fa-pencil"></i> <?php echo $text_edit_values; ?>
				</a>
			</div>
		</div>
	</div>
</div>