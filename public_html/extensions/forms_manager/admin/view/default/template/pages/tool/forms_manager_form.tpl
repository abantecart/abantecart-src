<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>


<div id="content" class="tab-content">

	<div class="panel-heading">
		<div class="pull-right">
			<div class="btn-group mr10 toolbar">
				<?php echo $form_language_switch; ?>
			</div>
			<div class="btn-group mr10 toolbar">
				<?php if (!empty ($help_url)) : ?>
					<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip"
					   title="" data-original-title="Help">
						<i class="fa fa-question-circle fa-lg"></i>
					</a>
				<?php endif; ?>
			</div>
		</div>

	</div>



	<?php echo $head_form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		</label>
		<?php foreach ($head_form['fields'] as $name => $field) {

		//Logic to calculate fields width
		$widthcasses = "col-sm-7";
		if (is_int(stripos($field->style, 'large-field'))) {
			$widthcasses = "col-sm-7";
		} else if (is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date'))) {
			$widthcasses = "col-sm-5";
		} else if (is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch'))) {
			$widthcasses = "col-sm-4";
		} else if (is_int(stripos($field->style, 'tiny-field'))) {
			$widthcasses = "col-sm-2";
		}
		$widthcasses .= " col-xs-12";
		?>
		<div class="form-group <?php if (!empty($error[$name])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-3 col-xs-12"
			       for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>

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
			<button type="submit" class="btn btn-primary lock-on-click">
				<i class="fa fa-save"></i> <?php echo $head_form['button_save']->text; ?>
			</button>
			&nbsp;
			<a id="reset_field" class="btn btn-default" href="<?php echo $head_form['button_reset']->href; ?>">
				<i class="fa fa-refresh"></i> <?php echo $head_form['button_reset']->text; ?>
			</a>
		</div>
	</div>
	</form>
	<?php if ($form_id) { ?>
		<div class="panel-body panel-body-nopadding tab-content col-xs-12">
			<div class="form-inline">
				<div class="btn-group ml10 toolbar mr20">
					<a class="btn btn-primary tooltips" href="#"
					   title="<?php echo $text_add_new_field; ?>"
					   data-original-title="<?php echo $text_add_new_field; ?>"
					   data-target="#field_modal" data-toggle="modal">
						<i class="fa fa-plus"></i>
					</a>
				</div>
				<div class="form-group">
					<label><?php echo $entry_edit_fields; ?></label>
					<div class="input-group input-group-sm">
						<?php echo $form['fields']; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel-body panel-body-nopadding" id="field_values">
			<?php //# Options HTML loaded from responce controller rt=product/product/load_option ?>
		</div>
	<?php } ?>
</div>

<?php


$modal_content = '<div class="add-option-modal" >
			<div class="panel panel-default">
			    <div id="collapseTwo" >
			    	' . $form['form_open'] . '
			    	<div class="panel-body panel-body-nopadding">
			    		' . $attributes . '
			    		<div class="mt10 ">
			    			<div class="form-group ' . (!empty($error['status']) ? "has-error" : "") . '">
			    				<label class="control-label col-sm-3 col-xs-12" for="' . $field->element_id . '">' . $entry_status . '</label>
			    				<div class="input-group afield ">
			    					' . $status . '
			    				</div>
			    			</div>
			    			<div class="form-group ' . (!empty($error['element_type']) ? "has-error" : "") . '">
			    				<label class="control-label col-sm-3 col-xs-12" for="' . $field->element_id . '">' . $entry_element_type . '</label>
			    				<div class="input-group afield ">
			    					' . $element_type . '
			    				</div>
			    			</div>
			    			<div class="form-group ' . (!empty($error['option']) ? "has-error" : "") . '">
			    				<label class="control-label col-sm-3 col-xs-12" for="' . $new_field_description->element_id . '">' . $entry_new_field_description . '</label>
			    				<div class="input-group afield ">
			    					' . $new_field_description . '
			    				</div>
			    			</div>
			    			<div class="form-group ' . (!empty($error['option']) ? "has-error" : "") . '">
			    				<label class="control-label col-sm-3 col-xs-12" for="' . $new_field_name->element_id . '">' . $entry_new_field_name . '</label>
			    				<div class="input-group afield ">
			    					' . $new_field_name . '
			    				</div>
			    			</div>
			    			<div class="form-group ' . (!empty($error['option']) ? "has-error" : "") . '">
			    				<label class="control-label col-sm-3 col-xs-12" for="' . $new_field_note->element_id . '">' . $entry_new_field_note . '</label>
			    				<div class="input-group afield ">
			    					' . $new_field_note . '
			    				</div>
			    			</div>
			    			<div class="form-group ' . (!empty($error['sort_order']) ? "has-error" : "") . '">
			    				<label class="control-label col-sm-3 col-xs-12" for="' . $field->element_id . '">' . $entry_sort_order . '</label>
			    				<div class="input-group afield ">
			    					' . $sort_order . '
			    				</div>
			    			</div>
			    			<div class="form-group ' . (!empty($error['required']) ? "has-error" : "") . '">
			    				<label class="control-label col-sm-3 col-xs-12" for="' . $field->element_id . '">' . $entry_required . '</label>
			    				<div class="input-group afield ">
			    					' . $required . '
			    				</div>
			    			</div>
			    		</div>
			    	</div>
			    	<div class="panel-footer">
			    		<div class="row">
			    		   <div class="center">
			    			 <button class="btn btn-primary" type="submit">
			    			 <i class="fa fa-save"></i> ' . $form['submit']->text . '
			    			 </button>&nbsp;
			    			 <button type="button" class="btn btn-default" data-dismiss="modal">
			    			 <i class="fa fa-times"></i> ' . $form['cancel']->text . '
			    			 </button>
			    		   </div>
			    		</div>
			    	</div>
			    	</form>
			    </div>
			</div>
		</div>';

echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'field_modal',
				'modal_type' => 'lg',
				'title' => $text_add_field,
				'content' => $modal_content));
?>

<script type="text/javascript"><!--

var text = {
	error_attribute_not_selected: <?php js_echo($error_attribute_not_selected); ?>,
	text_expand: <?php js_echo($text_expand); ?>,
	text_hide: <?php js_echo($text_hide); ?>
};
var opt_urls = {
	load_field: '<?php echo $urls['load_field'] ?>',
	update_field: '<?php echo $urls['update_field'] ?>',
	get_options_list: '<?php echo $urls['get_fields_list'] ?>'
};
var current_field_id = null;
var row_id = 1;

jQuery(function ($) {

	var updateFieldsList = function () {
		$.ajax({
			url: opt_urls.get_options_list,
			type: 'GET',
			dataType: 'json',
			async: false,
			success: function (json) {
				$("#new_fieldFrm_field_id option").remove();
				for (var key in json) {
					var selected = '';
					if (key == current_field_id) {
						selected = ' selected ';
					}
					$("#new_fieldFrm_field_id").append('<option value="' + key + '"' + selected + '>' + json[key]['field_name'] + '</option>');
				}
			},
			complete: function () {
				bindCustomEvents("#new_fieldFrm_field_id");
			}
		});
	}

	var editFieldDetails = function (id) {
		$('#notify_error').remove();
		var flds = ['status', 'field_name', 'field_description', 'field_note', 'sort_order', 'required', 'regexp_pattern', 'error_text'];
		var data = {field_id: current_field_id};
		for (var k in flds) {
			data[flds[k]] = $('#' + flds[k]).val();
		}
		var settings = $('input[name^=settings]');
		if(settings.length>0){
			settings.each(function(){
				data[$(this).attr('name')] = $(this).val();
			});
		}

		$.ajax({
			url: opt_urls.update_field,
			data: data,
			type: 'POST',
			async: false,
			success: function (html) {
				$('#field_name').html($('#name').val());
				updateFieldsList();
				//Reset changed values marks
				resetAForm($("input, checkbox, select", '#field_edit_form'));
				success_alert(<?php js_echo($text_success_field); ?>, true);
			}
		});
		return false;
	}

	$(document).on('click', "#field_values_tbl a.remove", function () {
		if ($(this).closest('tr').find('input[name^=field_value_id]').val() == 'new') {
			//remove new completely
			$(this).closest('tr').next().remove();
			$(this).closest('tr').remove();
		} else {
			//mark for delete and set disabled
			$(this).closest('tr').toggleClass('toDelete').toggleClass('transparent');
		}
		return false;
	});


	$(document).on('click', "#add_field_value", function () {

		var new_row = $('#new_row').clone();
		$(new_row).attr('id', 'new' + row_id);

		//find next sort order number
		var so = $('#field_values_tbl').find("input[name^='sort_order']");
		if (so.length > 0) {
			var highest = 0;
			so.each(function () {
				highest = Math.max(highest, parseInt(this.value));
			});

			$(new_row).find("input[name^='sort_order']").val(highest + 1);
		} else {
			$(new_row).find("input[name^='sort_order']").val(0);
		}

		if ($('#field_values_tbl tbody').length) {
			//add one more row
			$('#field_values_tbl tbody tr:last-child').after(new_row);
		} else {
			//we insert first row
			$('#field_values_tbl tr:last-child').after(new_row);
		}
		bindAform($("input, checkbox, select", new_row));
		//Mark rows to be new
				$('#new' + row_id + ' input[name^=field_value_id]').val('new');
				$("#new" + row_id + " input, #new" + row_id + " textarea, #new" + row_id + " select").each(function (i) {
					var new_name = $(this).attr('name');
					new_name = new_name.replace("[]", "[new" + row_id + "]");
					$(this).attr('name', new_name);
				});
		row_id++;
		return false;
	});

	$('#new_fieldFrm_field_id').change(function () {
		current_field_id = $(this).val();
		$.ajax({
			url: opt_urls.load_field,
			type: 'GET',
			data: {field_id: current_field_id},
			success: function (html) {
				$('#field_values').html(html);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				error_alert(errorThrown);
			},
			complete: function () {
				bindAform($("input, checkbox, select", '#field_edit_form'));
				bindAform($("input, checkbox, select", '#update_field_values'));
				bindCustomEvents('#field_values');
			}
		});

	});


	//select option and load data for it
	$('#option option:first-child').attr("selected", "selected").change();

	$(document).on('click', '#update_field', function () {
		editFieldDetails();
	});

	$(document).on('click', '#reset_option', function () {
		$('#new_fieldFrm_field_id').change();
		return false;
	});

	$(document).on('click', '#field_values button[type="submit"]', function () {
		//Mark rows to be deleted
		$('#field_values_tbl .toDelete input[name^=field_value_id]').val('delete');
		$(this).attr('disabled', 'disabled');

		editFieldDetails();

		var that = this;
		$.ajax({
			url: $(that).closest('form').attr('action'),
			type: 'GET',
			data: $(that).closest('form').serializeArray(),
			success: function (html) {
				$('#new_fieldFrm_field_id').change();
			},
			error: function (jqXHR, textStatus, errorThrown) {
				error_alert(errorThrown);
			},
			complete: function () {
				bindAform($("input, checkbox, select", '#field_edit_form'));
				bindAform($("input, checkbox, select", '#update_field_values'));
				bindCustomEvents('#field_values');
			}
		});
		return false;
	});

	$(document).on('click', '#new_fieldFrm button[type="submit"]', function () {
		var that = this;
		$.ajax({
			url: $(that).closest('form').attr('action'),
			type: 'POST',
			global: true,
			async: false,
			data: $(that).closest('form').serializeArray(),
			success: function (html) {
				updateFieldsList();
				$('#new_fieldFrm_field_id').delay(1000).change();
				$('#field_modal').modal('hide');
				success_alert(<?php js_echo($text_success_added_field); ?>, true);
				//reset form in modal
				$("#new_fieldFrm").trigger('reset');
				$("#new_fieldFrm .changed").removeClass('changed');
			},
			complete: function () {
				bindAform($("input, checkbox, select", '#field_edit_form'));
				bindAform($("input, checkbox, select", '#update_field_values'));
				bindCustomEvents('#field_values');
			}
		});
		return false;
	});

});


$(document).ready(function () {
	<?php if($field_id){	?>
	$('#new_fieldFrm_field_id').val('<?php echo $field_id;?>');
	<?php } ?>
	<?php if(key($form['fields']->options)!='new' ){?>
	$('#new_fieldFrm_field_id').change();
	<?php } ?>
});
//--></script>