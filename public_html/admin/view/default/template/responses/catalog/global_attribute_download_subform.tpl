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
	<div class="form-group <?php echo !empty($error[$name]) ? "has-error" :''; ?>">
		<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
		<div class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '') ?>"><?php echo $field; ?></div>
		<?php if (!empty($error[$name])) { ?>
			<span class="help-block field_err"><?php echo $error[$name]; ?></span>
		<?php } ?>
	</div>

	<?php if ($name == 'element_type') {
		if ($child_count == 0) { ?>
			<div id="values" style="display: none;">
				<label class="control-label col-sm-3 col-xs-12"></label>
				<div class="input-group afield col-sm-7">
				<table class="table table-narrow">
					<thead>
						<tr>
							<th><?php echo $entry_element_values; ?></th>
							<th><?php echo $column_sort_order; ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($form['attribute_values'] as $atr_val_id => $atr_field) { ?>
						<tr id="<?php echo $atr_val_id;?>" class="value">
							<td><?php echo $atr_field['attribute_value_ids']; ?><?php echo $atr_field['values']; ?></td>
							<td><?php $atr_field['sort_order']->style = 'col-sm-2';
								echo $atr_field['sort_order']; ?>
                            </td>
							<td>
                                <?php echo $this->getHookVar('attribute_value_extra_buttons_'.$atr_val_id); ?>
                                <a class="remove btn btn-danger-alt" title="<?php echo $button_remove; ?>">
                                    <i class="fa fa-minus-circle"></i>
                                </a>
                            </td>
						</tr>
                        <?php echo $this->getHookVar('attribute_value_extra_'.$atr_val_id); ?>
					<?php } ?>
					<tr>
						<td></td>
						<td></td>
						<td>
							<a href="#" title="<?php echo $button_add ?>" id="add_option_value" class="btn btn-success">
                                <i class="fa fa-plus-circle fa-lg"></i>
                            </a>
						</td>
					</tr>
					</tbody>
				</table>
				</div>
			</div>
		<?php } else { ?>
			<div id="values">
				<label class="control-label col-sm-3 col-xs-12"><?php echo $entry_children_attributes; ?></label>
				<div class="input-group afield col-sm-7">
					<ul class="list-group">
						<?php foreach ($children as $child) { ?>
							<li class="list-group-item"><a href="<?php echo $child['link']; ?>"><?php echo $child['name']; ?></a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		<?php } ?>

		<div id="file_settings" class="form-group" style="display: none;">
			<div class="form-group">
				<label class="control-label col-sm-3 col-xs-12"></label>
				<div class="input-group afield col-sm-7">
					<ul class="list-group">
					<?php if (is_array($children)) { ?>
						<?php foreach ($children as $child) { ?>
							<li class="list-group-item"><a
										href="<?php echo $child['link']; ?>"><?php echo $child['name']; ?></a></li>
						<?php } ?>
					<?php } ?>
					</ul>
				</div>
			</div>
			<?php
			$arr = array(
					'entry_allowed_extensions' => 'extensions',
					'entry_min_size'=>'min_size',
					'entry_max_size'=>'max_size',
					'entry_upload_dir'=>'directory');
			foreach($arr as $entry=>$name){
				$fld = $form['settings_fields'][$name];
				?>
			<div class="form-group ">
				<label class="control-label col-sm-3 col-xs-12" for="<?php echo $fld->name;?>"><?php echo $$entry; ?></label>
				<div class="input-group afield col-sm-7"><?php echo $form['settings_fields'][$name]; ?></div>
			</div>
			<?php }?>

		</div>

	<?php } ?>

	<?php if ($name == 'attribute_parent') { ?>
		<div class="input-group afield col-sm-7"><?php echo $text_parent_note; ?></div>
	<?php } ?>

<?php } //foreach ?>

<script type="text/javascript">
	jQuery(function ($) {
		let elements_with_options = [];
		<?php
		foreach ($elements_with_options as $el) {
			echo "elements_with_options.push('$el');\r\n";
		} ?>

		$('#values .aform').show();
		$(document).on('click', '#values a.remove', function () {
			let row = $(this).parents('tr');
            if(row.hasClass('danger')){
                row.removeClass('danger');
                return;
            }
			if ($('#values tr.value').length > 1) {
                if (row.find('input[name^=attribute_value_ids]').val().substring(0,3) == 'new') {
					row.remove();
				} else {
					row.addClass('danger');
				}
			}
			return false;
		});

		$('#add_option_value').on('click', function () {
            const key = Date.now();
            let so = Number($('#values').find('input[name*=sort_order]').last().val());
            so += 1;
			let row = $('#values tr.value').last().clone();
			$('#values tr.value').last().after(row);

			let last = $('#values tr.value').last();
            last.find('input[name^=attribute_value_ids').attr("name", "attribute_value_ids[new"+key+"]").removeAttr('id').val('new'+key);
            last.find('input[name*="[value]"]').attr("name", "values[new"+key+"][value]").removeAttr('id');
            last.find('input[name*=sort_order]')
                .attr("name", "values[new"+key+"][sort_order]")
                .removeAttr('id')
                .val( so );
			last.removeClass('danger');
			return false;
		});

		if ($.inArray($('#editFrm_element_type').val(), elements_with_options) > -1) {
			$('#values').show();
		}

		if ($('#editFrm_element_type').val() == 'U') {
			$('#file_settings').show();
		} else {
			$('#file_settings').hide();
		}

		$('#editFrm_element_type').change(function () {
			var curr_value = $(this).val();
			if ($.inArray(curr_value, elements_with_options) > -1) {
				$('#values').show();
			} else {
				$('#values').hide();
			}

			if (curr_value == 'U') {
				$('#file_settings').show();
			} else {
				$('#file_settings').hide();
			}
			if(curr_value == 'I' || curr_value == 'T' ){
				$('#editFrm_placeholder').removeAttr('disabled').parents('.form-group').show();
			}else{
				$('#editFrm_placeholder').attr('disabled','disabled').parents('.form-group').hide();
			}
		});

		$('#editFrm_attribute_parent_id').change(function () {
			var attribute_id = $(this).val();
			if (attribute_id == '') {
				$('#editFrm_attribute_type_id')
						.val('')
						.change()
						.removeAttr('disabled');
				return false;
			}
			$.ajax({
				url: '<?php echo $get_attribute_type; ?>' + '&attribute_id=' + attribute_id,
				type: 'GET',
				dataType: 'json',
				success: function (json) {
					$('#editFrm_attribute_type_id')
							.val(json)
							.change()
							.attr('disabled', 'disabled');
				},
				error: function (jqXHR, textStatus, errorThrown) {
					$('#content').prepend('<div class="error" align="center"><b>' + textStatus + '</b>  ' + errorThrown + '</div>');
				}
			});

		});
		if ($('#editFrm_attribute_parent_id').val() != '') {
			$('#editFrm_attribute_parent_id').change();
		}

		$('#editFrm').submit(function () {
			$('#values .danger input[name^=attribute_value_ids]').val('delete');
			$(":disabled", this).removeAttr('disabled');
		});

		$('#file_settings .aform').show();

		$(document).ready(function(){
			$('#editFrm_element_type').change();
		});

	});
</script>