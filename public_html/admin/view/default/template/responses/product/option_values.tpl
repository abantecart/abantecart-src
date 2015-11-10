
<div class="row">

<div class="col-md-4"> 
	<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $option_data['language'][$language_id]['name']; ?></h3>
	</div>
	<div id="option_edit_form" class="panel-body panel-body-nopadding">
		<div class="form-group">
			<label class="heading col-sm-10"><?php echo $text_option_type; ?>: <?php echo $option_type; ?></label>
			<div class="input-group col-sm-2">
			<a class="pull-right btn btn-default tooltips" onclick="optionDelete('<?php echo $button_remove_option->href; ?>')" data-original-title="<?php echo $button_remove_option->text; ?>" data-confirmation="delete">
			 <i class="fa fa-trash-o"></i>
			 </a>
		    </div>
		</div>

		<?php
		$fields = array('entry_status'=>'status',
						'entry_option_name'=>'option_name',
						'entry_option_placeholder' => 'option_placeholder',
						'entry_sort_order' => 'option_sort_order',
						'entry_required' => 'required',
						'entry_allowed_extensions' => 'extensions',
						'entry_min_size'=>'min_size',
						'entry_max_size'=>'max_size',
						'entry_upload_dir'=>'directory',
						'entry_regexp_pattern' => 'option_regexp_pattern',
						'entry_error_text'=>'option_error_text'

		);
		foreach ($fields as $e=>$name) { ?>
				<?php
					$entry = $$e;
					$field = $$name;
					if(!is_object($field)){
						continue;
					}

					if($name == 'option_placeholder' && !(string)$option_placeholder){
						continue;
					}
					//Logic to calculate fields width
					$widthcasses = "col-sm-7";
					if ( is_int(stripos($field->style, 'large-field')) ) {
						$widthcasses = "col-sm-7";
					} else if ( is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date')) ) {
						$widthcasses = "col-sm-6";
					} else if ( is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch')) ) {
						$widthcasses = "col-sm-3";
					} else if ( is_int(stripos($field->style, 'tiny-field')) ) {
						$widthcasses = "col-sm-2";
					}
					$widthcasses .= " col-xs-12";
				?>
			<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
				<label class="control-label col-md-6" for="<?php echo $field->element_id; ?>"><?php echo $entry; ?></label>
				<div class="input-group input-group-sm afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
					<?php echo $field;?>
				</div>
			    <?php if (!empty($error[$name])) { ?>
			    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
			    <?php } ?>
			</div>
		<?php } ?>
	</div>
	<div class="panel-footer">
		<div class="center">
			 <button id="update_option" class="btn btn-primary">
			 <i class="fa fa-save"></i> <?php echo $button_save->text; ?>
			 </button>
			 &nbsp;
			 <a id="reset_option" class="btn btn-default" href="<?php echo $button_reset->href; ?>">
			     <i class="fa fa-refresh"></i> <?php echo $button_reset->text; ?>
			 </a>
		</div>
	</div>
</div>
</div>

<?php echo $update_option_values_form['open']; ?>
<div class="col-md-8"> 
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $text_option_values; ?></h3>
	</div>
	<div class="panel-body panel-body-nopadding">
		<table id="option_values_tbl" class="table table_narrow">
			<thead>
				<tr>
					<?php if($with_default){?>
					<th class="left">
						<span title="click to uncheck default value" class="uncheck">
							<?php echo $text_default; ?>
						</span>
					</th>
					<?php }
					if($option_data['element_type']!='U'){ ?>
					<th class="left"><?php echo $entry_option_value; ?></th>
					<th class="left"><?php echo $entry_option_quantity; ?></th>
					<th class="left"><?php echo $entry_track_option_stock; ?></th>
					<?php } ?>

					<th class="left"><?php echo $entry_option_price; ?></th>
					<th class="left"><?php echo $entry_option_prefix; ?></th>
					<th class="left"><?php echo $entry_sort_order; ?></th>
					<th class="left"></th>
					<?php if ($selectable){?>
						<th class="left"></th>
					<?php }?>
				</tr>
			</thead>
		    <?php foreach ($option_values as $item) { ?>
		        <?php echo $item['row']; ?>
		    <?php } ?>

		</table>
	</div>
	<div class="panel-footer">
		<div class="center">
			<?php if (in_array($option_data['element_type'], $elements_with_options)) { ?>
			<a href="#" title="<?php echo $button_add?>" id="add_option_value" class="btn btn-success"><i class="fa fa-plus-circle fa-lg"></i></a>&nbsp;&nbsp;
			<?php } ?>
			<button type="submit" class="btn btn-primary">
			    <i class="fa fa-save"></i> <?php echo $button_save->text; ?>
			</button>
			&nbsp;
			<a id="reset_option" class="btn btn-default" href="<?php echo $button_reset->href; ?>">
			    <i class="fa fa-refresh"></i> <?php echo $button_reset->text; ?>
			</a>
		</div>
	</div>
</div>
</div>
</form>


<table style="display:none;" id="new_row_table">
	<?php echo $new_option_row ?>
</table>


</div>