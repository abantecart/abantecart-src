<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="tab-content">
    <?php echo $tabs; ?>

    <div class="panel panel-default">
        <div class="panel-heading col-xs-12">
            <div class="primary_content_actions pull-left">
                <a class="btn btn-white tooltips back-to-grid mr10"
                   href="<?php echo $list_url; ?>" data-toggle="tooltip"
                   data-original-title="<?php echo_html2view($text_back_to_list); ?>">
                    <i class="fa fa-arrow-left fa-lg"></i>
                </a>
                <div class="btn-group mr10 toolbar">
                    <a class="btn btn-primary tooltips" href="#"
                       title="<?php echo $text_add_new_field; ?>"
                       data-original-title="<?php echo $text_add_new_field; ?>"
                       data-target="#field_modal" data-toggle="modal">
                        <i class="fa fa-plus"></i>
                    </a>
                </div>
                <div class="form-inline">
                    <div class="form-group ml10">
                        <label><?php echo $entry_edit_fields; ?></label>
                        <div class="input-group ml5">
                            <?php echo $form['fields']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
        </div>

        <div class="panel-body panel-body-nopadding" id="field_values"></div>
    </div>
</div>

<?php
$modal_content = '<div class="add-option-modal" >
			<div class="panel panel-default">
			    <div id="collapseTwo" >
			    	' . $form['form_open'] . '
			    	<div class="panel-body panel-body-nopadding">
			    		<div class="mt10 ">
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_status . '</label>
			    				<div class="input-group afield ">
			    					' . $status . '
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_element_type . '</label>
			    				<div class="input-group afield ">
			    					' . $element_type . '
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_new_field_name . '</label>
			    				<div class="input-group afield ">
			    					' . $new_field_name . '
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_new_field_description . '</label>
			    				<div class="input-group afield ">
			    					' . $new_field_description . '
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_new_field_note . '</label>
			    				<div class="input-group afield ">
			    					' . $new_field_note . '
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_field_help_text . '</label>
			    				<div class="input-group afield ">
			    					' . $field_help_text . '
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_field_error_text . '</label>
			    				<div class="input-group afield ">
			    					' . $field_error_text . '
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_field_placeholder . '</label>
			    				<div class="input-group afield ">
			    					' . $field_placeholder . '
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_field_default . '</label>
			    				<div class="input-group afield ">
			    					' . $field_default . '
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_sort_order . '</label>
			    				<div class="input-group afield ">
			    					' . $sort_order . '
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_required . '</label>
			    				<div class="input-group afield ">
			    					' . $required . '
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_field_validation . '</label>
			    				<div class="input-group afield ">
			    					' . $field_validation . '
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_field_regexp_pattern . '</label>
			    				<div class="input-group afield ">
			    					' . $field_regexp_pattern . '
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_field_regexp_error_text . '</label>
			    				<div class="input-group afield ">
			    					' . $field_regexp_error_text . '
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-sm-3 col-xs-12">' . $entry_field_settings . '</label>
			    				<div class="input-group afield ">
			    					' . $field_settings . '
			    				</div>
			    			</div>
			    		</div>
			    	</div>
			    	<div class="panel-footer">
			    		<div class="text-center">
			    			<button type="submit" class="btn btn-primary lock-on-click">
			    				<i class="fa fa-save"></i> ' . $button_save . '
			    			</button>
			    			<button type="button" class="btn btn-default" data-dismiss="modal">
			    				<i class="fa fa-times"></i> ' . $button_cancel . '
			    			</button>
			    		</div>
			    	</div>
			    	</form>
			    </div>
			</div>
		</div>';

echo $this->html->buildElement([
    'type'       => 'modal',
    'id'         => 'field_modal',
    'modal_type' => 'lg',
    'title'      => $text_add_new_field,
    'content'    => $modal_content,
]);
?>

<script type="text/javascript">
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
					$("#new_fieldFrm_field_id").append('<option value="' + key + '"' + selected + '>' + json[key]['name'] + '</option>');
				}
			},
			complete: function () {
				bindCustomEvents("#new_fieldFrm_field_id");
			}
		});
	}

	var editFieldDetails = function () {
		$('#notify_error').remove();
		const data = $('#field_edit_form').find(':input').serialize()
			+ '&field_id='+ current_field_id;

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
				location = location.href;
			},
			complete: function () {
				bindAform($("input, checkbox, select", '#field_edit_form'));
				bindAform($("input, checkbox, select", '#update_field_values'));
				bindCustomEvents('#field_values');
			}
		});
		return false;
	});

	//restrict captcha field name
	$('select#new_fieldFrm_element_type').on('change', function(){
		if($(this).val() !== 'K'){
			$('input#new_fieldFrm_field_name').removeAttr('readonly');
			return;
		}
		$('input#new_fieldFrm_field_name').val(
			'<?php echo $this->config->get('config_recaptcha_site_key') ? 'g-recaptcha-response' : 'captcha'?>'
		).attr('readonly', 'readonly' );
	});

	$(document).ready(function () {
        <?php if($field_id){    ?>
		$('#new_fieldFrm_field_id').val('<?php echo $field_id;?>');
        <?php } ?>
        <?php if($form['fields'] && key($form['fields']->options) != 'new' ){?>
		$('#new_fieldFrm_field_id').change();
        <?php } ?>
	});
</script>
