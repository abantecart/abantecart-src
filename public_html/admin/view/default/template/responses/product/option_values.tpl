
<div id="notify" class="align_center success" style="display: none;"></div>
<?php if ($success) { ?>
<script type="text/javascript">
	$('#notify').html('<?php echo $success?>').fadeIn(500).delay(2000).fadeOut(500);
</script>
<?php } ?>


<div class="panel panel-default">
	<div class="panel-heading">
		<div class="panel-btns">
            <a class="minimize" href="">−</a>
		</div>
          <h3 class="panel-title"><?php echo $option_data['language'][$language_id]['name']; ?></h3>
	</div>
	<div class="panel-body panel-body-nopadding table-responsive" style="display: block;">
		<div class="mb20">
		<h4><?php echo $text_option_type; ?>: <?php echo $option_type; ?></h4>

			<a class=" pull-right btn btn-default" href="<?php echo $button_remove_option->href; ?>">
			 <i class="fa fa-trash-o"></i> <?php echo $button_remove_option->text; ?>
			 </a>


		</div>

		<?php
		$fields = array('entry_status'=>'status',
						'entry_option_name'=>'option_name',
						'entry_option_placeholder' => 'option_placeholder',
						'entry_sort_order' => 'option_sort_order',
						'entry_required' => 'required',
						'entry_regexp_pattern' => 'option_regexp_pattern',
						'entry_error_text'=>'option_error_text'

		);
		foreach ($fields as $e=>$name) { ?>
				<?php
					$entry = $$e;
					$field = $$name;

					if($name == 'option_placeholder' && !(string)$option_placeholder){
						continue;
					}
					//Logic to cululate fileds width
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
			<div class="form-group <? if (!empty($error[$name])) { echo "has-error"; } ?>">
				<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo $entry; ?></label>
				<div class="input-group afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
					<?php echo $field; ?>
				</div>
			    <?php if (!empty($error[$name])) { ?>
			    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
			    <?php } ?>
			</div>
		<?php } ?>
	</div>
	<div class="panel-footer">
		<div class="row">
		   <div class="col-sm-7 col-sm-offset-1">
			 <button id="update_option" class="btn btn-primary pull-right">
			 <i class="fa fa-save"></i> <?php echo $button_save->text; ?>
			 </button>
		   </div>
		</div>
	</div>
</div>


<?php echo $update_option_values_form['open']; ?>
<div class="tab-content">
	<div class="panel-body panel-body-nopadding">
		<label class="h4 heading"><?php echo $text_option_values; ?></label>
		<table id="option_values_tbl" class="table">
			<thead>
				<tr>
					<?php if($with_default){?>
					<th class="left"><?php echo $text_default; ?>&nbsp;&nbsp;<span class="default_uncheck">[x]</span></th>
					<?php }?>
					<th class="left"><?php echo $entry_option_value; ?></th>
					<th class="left"><?php echo $entry_option_quantity; ?></th>
					<th class="left"><?php echo $entry_track_option_stock; ?></th>
					<th class="left"><?php echo $entry_option_price; ?></th>
					<th class="left"><?php echo $entry_option_prefix; ?></th>
					<th class="left"><?php echo $entry_sort_order; ?></th>
					<th class="left"></th>
					<?php if ($selectable){?>
						<th class="left"><?php echo $column_action; ?></th>
					<?php }?>
				</tr>
			</thead>
		    <?php foreach ($option_values as $item) { ?>
		        <?php echo $item['row']; ?>
		    <?php } ?>

		</table>


	</div>
	<div class="panel-footer">
		<div class="row">
			<div class="col-sm-9 col-sm-offset-8">
			<?php if (in_array($option_data['element_type'], $elements_with_options)) { ?>
				<a href="#" title="<?php echo $button_add?>" id="add_option_value" class="btn btn-success"><i class="fa fa-plus-circle"></i></a>
			<?php } ?>

			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3">
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