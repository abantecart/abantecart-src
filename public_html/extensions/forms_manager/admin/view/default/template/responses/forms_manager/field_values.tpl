<div class="row">

	<div class="col-md-4 panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $field_data['field_name']; ?></h3>
		</div>
		<div id="field_edit_form" class="panel-body panel-body-nopadding">
			<div class="form-group">
				<label class="heading col-sm-10"><?php echo $text_field_type; ?>: <?php echo $field_type; ?></label>
				<div class="input-group col-sm-2">
				<a class=" pull-right btn btn-default tooltips"
				   href="<?php echo $button_remove_field->href; ?>"
				   data-original-title="<?php echo $button_remove_field->text; ?>" data-confirmation="delete">
				    <i class="fa fa-trash-o"></i>
				</a>
				</div>
			</div>

			<?php
			$fields = array(
					'entry_status' => 'status',
					'entry_field_name' => 'field_name',
					'entry_field_description' => 'field_description',
					'entry_field_note' => 'field_note',
					'entry_sort_order' => 'field_sort_order',
					'entry_required' => 'required',
					'entry_regexp_pattern' => 'field_regexp_pattern',
					'entry_error_text' => 'field_error_text'
			);


			foreach ($fields as $e=>$name) { ?>
			<?php
			$entry = $$e;
			$field = $$name;
			if(!$field){ continue;}

			if ($name == 'option_placeholder' && !(string)$option_placeholder) {
				continue;
			}
			//Logic to calculate fields width
			$widthcasses = "col-sm-6";
			if (is_int(stripos($field->style, 'large-field'))) {
				$widthcasses = "col-sm-6";
			} else if (is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date'))) {
				$widthcasses = "col-sm-6";
			} else if (is_int(stripos($field->style, 'small-field')) ) {
				$widthcasses = "col-sm-3";
			} else if (is_int(stripos($field->style, 'tiny-field'))) {
				$widthcasses = "col-sm-2";
			}
			$widthcasses .= " col-xs-12";
			?>
			<div class="form-group <?php if (!empty($error[$name])) {
				echo "has-error";
			} ?>">
				<label class="control-label col-md-6"
					   for="<?php echo $field->element_id; ?>"><?php echo $entry; ?></label>

				<div class="input-group input-group-sm afield <?php echo $widthcasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '')?>">
					<?php echo $field;?>
				</div>
				<?php if (!empty($error[$name])) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
				<?php } ?>
			</div>
			<?php } ?>
			<?php echo $field_settings; ?>

		</div>
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
<?php if($new_value_row){?>
	<?php echo $update_field_values_form['open']; ?>
	<div class="col-md-8 tab-content">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $text_field_values; ?></h3>
		</div>
		<div class="panel-body panel-body-nopadding">
			<table id="field_values_tbl" class="table table_narrow">
				<thead>
				<tr>
					<th class="left"><?php echo $entry_field_value; ?></th>
					<?php if ($selectable){?>
					<th class="left"><?php echo $entry_sort_order; ?></th>
					<th class="left"></th>
					<?php }?>
				</tr>
				</thead>
				<?php foreach ($field_values as $item) { ?>
				<?php echo $item['row']; ?>
				<?php } ?>

			</table>
		</div>
		<div class="panel-footer">
			<div class="center">
				<?php

				if (in_array($field_data['element_type'], $elements_with_options)) { ?>
				<a href="#" title="<?php echo $button_add?>" id="add_field_value" class="btn btn-success"><i
						class="fa fa-plus-circle fa-lg"></i></a>&nbsp;&nbsp;
				<?php } ?>
				<button type="submit" class="btn btn-primary">
					<i class="fa fa-save"></i> <?php echo $button_save->text; ?>
				</button>
				&nbsp;
				<a id="reset_field" class="btn btn-default" href="<?php echo $button_reset->href; ?>">
					<i class="fa fa-refresh"></i> <?php echo $button_reset->text; ?>
				</a>
			</div>
		</div>
	</div>
	</form>

	<table style="display:none;" id="new_row_table">
		<?php echo $new_value_row ?>
	</table>
<?php } ?>
</div>