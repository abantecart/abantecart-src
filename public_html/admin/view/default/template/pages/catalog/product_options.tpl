<?php if (!empty($error['warning'])) { ?>
	<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<a name="top"></a>

<div class="contentBox">
	<div class="cbox_tl">
		<div class="cbox_tr">
			<div class="cbox_tc">
				<div class="heading icon_title_product"><?php echo $form_title; ?></div>
				<?php echo $product_tabs ?>
				<div class="toolbar">
					<?php if (!empty ($help_url)) : ?>
						<div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img
										src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
					<?php endif; ?>
					<?php echo $form_language_switch; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="cbox_cl">
		<div class="cbox_cr">
			<div class="cbox_cc">

				<?php echo $summary_form; ?>

				<div class="fieldset">
					<div class="heading"><a id="tab_option"><?php echo $tab_option; ?></a></div>
					<div class="top_left">
						<div class="top_right">
							<div class="top_mid"></div>
						</div>
					</div>
					<div class="cont_left">
						<div class="cont_right">
							<div class="cont_mid">

								<div class="option_form">
									<div class="option_field">
										<div class="aform">
											<div class="afield mask2">
												<div class="tl">
													<div class="tr">
														<div class="tc"></div>
													</div>
												</div>
												<div class="cl">
													<div class="cr">
														<div class="cc">
															<select id="product_form_option" size="10"
																	class="attribute_list static_field">
																<?php foreach ($product_options as $product_option) { ?>
																	<option value="<?php echo $product_option['product_option_id']; ?>"><?php echo $product_option['language'][$language_id]['name']; ?></option>
																<?php } ?>
															</select>
														</div>
													</div>
												</div>
												<div class="bl">
													<div class="br">
														<div class="bc"></div>
													</div>
												</div>
											</div>
										</div>
										<div class="options_buttons">
											<?php echo $form['form_open']; ?>
											<table cellpadding="3" cellspacing="0">
												<tr>
													<td colspan="2"><?php echo $attributes; ?></td>
												</tr>
											</table>
											<table cellpadding="2" cellspacing="0" id="option_name_block">
												<tr>
													<td><?php echo $entry_status; ?></td>
													<td><?php echo $status; ?></td>
												</tr>
												<tr>
													<td><?php echo $entry_option; ?></td>
													<td>
														<?php echo $option_name; ?>
														<div class="error"
															 style="display:none"><?php echo $error_required ?></div>
													</td>
												</tr>
												<tr>
													<td><?php echo $entry_element_type; ?></td>
													<td>
														<?php echo $element_type; ?>
														<div class="error"
															 style="display:none"><?php echo $error_required ?></div>
													</td>
												</tr>
												<tr>
													<td><?php echo $entry_sort_order; ?></td>
													<td><?php echo $sort_order; ?></td>
												</tr>
												<tr>
													<td><?php echo $entry_required; ?></td>
													<td><?php echo $required; ?></td>
												</tr>
											</table>
											<button type="submit"
													class="btn_standard"><?php echo $form['submit']; ?></button>
											<button type="reset" class="btn_standard" style="display:none"
													id="option_name_reset"><?php echo $button_reset; ?></button>
											</form>
										</div>
									</div>

									<div id="options">
										<div id="option_values"></div>
									</div>
								</div>

							</div>
						</div>
					</div>
					<div class="bottom_left">
						<div class="bottom_right">
							<div class="bottom_mid"></div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
	<div class="cbox_bl">
		<div class="cbox_br">
			<div class="cbox_bc"></div>
		</div>
	</div>
</div>
<?php echo $resources_scripts; ?>
<script type="text/javascript"><!--
var setRLparams = function(attr_val_id) {
	urls.resource_library = '<?php echo $rl_rl_path; ?>&object_id=' + attr_val_id;
	urls.resources = '<?php echo $rl_resources_path; ?>&object_id=' + attr_val_id;
	urls.unmap = '<?php echo $rl_unmap_path; ?>&object_id=' + attr_val_id;
	urls.attr_val_id = attr_val_id;
}

var openRL = function(attr_val_id) {
	setRLparams(attr_val_id);
	mediaDialog('image', 'add', attr_val_id);
}


// override rl js-script function
var loadMedia = function (type) {
	if (!urls.attr_val_id) return;
	var type = "image";
	$.ajax({
		url: urls.resources,
		type: 'GET',
		data: { type: type },
		dataType: 'json',
		success: function (json) {


			var html = '';
			$(json.items).each(function (index, item) {
				var src = '<img src="' + item['thumbnail_url'] + '" title="' + item['name'] + '" />';
				if (type == 'image' && item['resource_code']) {
					src = item['thumbnail_url'];
				}
				html += '<span id="image_row' + item['resource_id'] + '" class="image_block">\
                <a class="resource_edit" type="' + type + '" id="' + item['resource_id'] + '">' + src + '</a><br /></span>';
			});
			html += '<span class="image_block"><a class="resource_add" type="' + type + '"><img src="<?php echo $template_dir.'/image/icons/icon_add_media.png'; ?>" alt="<?php echo $text_add_media; ?>"/></a></span>';

			$('#rl_' + urls.attr_val_id).html(html);
			if ($(json.items).length) {
				$('a.resource_edit').unbind('click');
				$('a.resource_edit').click(function () {
					setRLparams($(this).parent().parent().prop('id').replace('rl_', ''));
					mediaDialog($(this).prop('type'), 'update', $(this).prop('id'));
					return false;
				})
			}
			$('a.resource_add').unbind('click');
			$('a.resource_add').click(function () {
				setRLparams($(this).parent().parent().prop('id').replace('rl_', ''));
				mediaDialog($(this).prop('type'), 'add', $(this).prop('id'));
				return false;
			});
		},
		error: function (jqXHR, textStatus, errorThrown) {
			$('#type_' + type).show();
			$('#rl_' + urls.attr_val_id).html('<div class="error" align="center"><b>' + textStatus + '</b>  ' + errorThrown + '</div>');
		}
	});

}


var mediaDialog = function (type, action, id) {
	$('#dialog').remove();

	var src = urls.resource_library + '&' + action + '=1&type=' + type;

	if (id) {
		src += '&resource_id=' + id;
	}
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="' + src + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	$('#dialog iframe').load(function (e) {
		try {
			var error_data = $.parseJSON($(this).contents().find('body').html());
		} catch (e) {
			var error_data = null;
		}
		if (error_data && error_data.error_code) {
			$('#dialog').dialog('close');
			httpError(error_data);
		}
	});

	$('#dialog').dialog({
		title: '<?php echo $text_resource_library; ?>',
		close: function (event, ui) {
			loadMedia(type);
		},
		width: 900,
		height: 500,
		resizable: false,
		modal: true
	});
};

var text = {
	error_attribute_not_selected: '<?php echo $error_attribute_not_selected ?>',
	text_expand: '<?php echo $text_expand ?>',
	text_hide: '<?php echo $text_hide ?>'
};
var opt_urls = {
	load_option: '<?php echo $url['load_option'] ?>',
	update_option: '<?php echo $url['update_option'] ?>',
	get_options_list: '<?php echo $url['get_options_list'] ?>'
};
var current_option_id = null;
var row_id = 1;

jQuery(function ($) {

	$("#option_name_block").hide();
	$("#product_form").submit(function () {
		if ($("#new_option_form_attribute_id").val() == 'new' && ( $("#new_option_form_option_name").val() == '' || $("#new_option_form_element_type").val() == ''  )) {
			if (!$("#option_name_block").is(':visible')) {
				$("#option_name_block").show();
				$("#option_name_reset").show();
				return false;
			}
			if ($("#new_option_form_option_name").val() == '') {
				$("#new_option_form_option_name").focus();
				$("#new_option_form_option_name").closest("span").next().next().show();
			} else {
				$("#new_option_form_option_name").closest("span").next().next().hide();
			}

			if ($("#new_option_form_element_type").val() == '') {
				$("#new_option_form_element_type").focus();
				$("#new_option_form_element_type").closest("span").next().next().show();
			} else {
				$("#new_option_form_element_type").closest("span").next().next().hide();
			}

			return false;
		}
	});

var updateOptions = function() {
		$.ajax({
			url: opt_urls.get_options_list,
			type: 'GET',
			dataType: 'json',
			success: function (json) {
				$("#product_form_option option").remove();
				for (var key in json) {
					$("#product_form_option").append($('<option value="' + key + '">' + json[key] + '</option>'));
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$('#option_values').html('<div class="error" align="center"><b>' + textStatus + '</b>  ' + errorThrown + '</div>');
			}
		});
	}

	var editOption = function(id) {
		$('#notify_error').remove();
		$.ajax({
			url: opt_urls.update_option,
			data: {
				option_id: current_option_id,
				status: ( $('#status').val() ),
				sort_order: $('#sort_order').val(),
				name: $('#name').val(),
				option_placeholder: ($('#option_placeholder') ? $('#option_placeholder').val() : ''),
				regexp_pattern: ($('#regexp_pattern') ? $('#regexp_pattern').val() : ''),
				error_text: ($('#error_text') ? $('#error_text').val() : ''),
				required: ($('#required').is(':checked') ? 1 : 0)
			},
			type: 'GET',
			success: function (html) {
				$('#option_name').html($('#name').val());
				updateOptions();
				$('#notify').html('<?php echo $text_success_option?>').fadeIn(500).delay(2000).fadeOut(500);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$('#notify').after('<div id="notify_error" class="warning error" align="center">' + errorThrown + '</div>');
			}
		});
		return false;
	}

	$("#option_values_tbl a.remove").live('click', function () {
		if ($(this).closest('tr').find('input[name^=product_option_value_id]').val() == 'new') {
			//remove new completely
			$(this).closest('tr').next().remove();
			$(this).closest('tr').remove();
		} else {
			$(this).closest('tr').toggleClass('toDelete');
		}
		$(this).parent().parent().next().find('div.additionalRow').toggleClass('toDelete').hide();
		//$(this).parent().parent().find('a.expandRow').click();
		return false;
	});

	$("#option_values_tbl a.expandRow").live('click', function () {
		var additional_row = $(this).parent().parent().next().find('div.additionalRow');
		if ($(additional_row).is(':visible')) {
			$(additional_row).hide();
			$(this).text(text.text_expand);
			$(this).parent().parent().next().find('div.add_resource').html();
		} else {
			$(additional_row).show();
			$(this).text(text.text_hide);
			$('div.aform', additional_row).show();
			setRLparams($(this).attr('id'));

			loadMedia('image');
		}

		return false;
	});

	$('.open_newtab').live('click', function () {
		var href = $(this).attr('link');
		top.open(href, '_blank');
		return false;
	});


	$('.default_uncheck').live('click', function () {
		$("input[name='default']").removeAttr('checked');
	});

	$("#add_option_value").live('click', function () {
		var new_row = $('#new_row').parent().find('tr').clone();
		$(new_row).attr('id', 'new' + row_id);

		var so = $('#option_values_tbl').find("input[name^='sort_order']");
		if(so.length>0){
			var highest = 0;
			so.each(function() {
				highest = Math.max(highest, parseInt(this.value));
			});
			$(new_row).find("input[name^='sort_order']").val(highest+1);
		}

		$('#option_values_tbl tr:last-child').after(new_row);
		$("input, checkbox, select", new_row).aform({triggerChanged: true, showButtons: false });
		$('div.aform', new_row).show();
		//Mark rows to be new
		$('#new' + row_id + ' input[name=default]').last()
				.val('new' + row_id)
				.attr('id', 'option_value_form_default_new' + row_id)
				.removeAttr('checked')
				.parent('label')
				.attr('for', 'option_value_form_default_new' + row_id);
		$('#new' + row_id + ' input[name^=product_option_value_id]').val('new');
		$("#new" + row_id + " input, #new" + row_id + " textarea, #new" + row_id + " select").each(function (i) {
			var new_name = $(this).attr('name');
			new_name = new_name.replace("[]", "[new" + row_id + "]");
			$(this).attr('name', new_name);
		});
		row_id++;
		return false;
	});

	// $('#product_form_option').aform({ triggerChanged: false });
	$('#product_form_option').change(function () {
		current_option_id = $(this).val();
		$.ajax({
			url: opt_urls.load_option,
			type: 'GET',
			data: { option_id: current_option_id },
			success: function (html) {
				$('#option_values').html(html);
				$("input, checkbox, select", '#option_values_tbl').aform({triggerChanged: true, showButtons: false});
				$("input, checkbox, select", '.editOption').aform({triggerChanged: true, showButtons: false});
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$('#option_values').html('<div class="error" align="center"><b>' + textStatus + '</b>  ' + errorThrown + '</div>');
			}
		});
	});


	//select option and load data for it
	$('#product_form_option option:first-child').attr("selected", "selected").change();

	$('#update_option').live('click', function () {
		editOption('#update_option');
	});

	$('#reset_option').live('click', function () {
		$('#product_form_option').change();
		return false;
	});

	$('#option_values a').live('click', function () {
		if ($(this).attr('id') == 'update_option' || $(this).attr('id') == 'add_option_value' ||
				$(this).attr('id') == 'reset_option' || $(this).hasClass('remove') || $(this).hasClass('expandRow')) {
			return false;
		}
		if ($(this).attr('id') == 'button_remove_option' && !confirm('<?php echo $text_delete_confirm; ?>')) {
			return false;
		}
		var that = this;
		$.ajax({
			url: $(that).attr('href'),
			type: 'GET',
			success: function (html) {
				if ($(that).attr('id') == 'button_remove_option') {
					$('#product_form_option option:selected').remove();
				}
				$('#option_values').html(html);
				$("input, checkbox", '#option_values_tbl').aform({triggerChanged: true, showButtons: false});
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$('#option_values').html('<div class="error" align="center"><b>' + textStatus + '</b>  ' + errorThrown + '</div>');
			}
		});
		return false;
	});

	$('#option_values button[type="submit"]').live('click', function () {
		//Mark rows to be deleted
		$('#option_values_tbl .toDelete input[name^=product_option_value_id]').val('delete');
		$(this).attr('disabled', 'disabled');

		editOption('#update_option');

		//$('#option_values_tbl tr.toDelete').remove();
		var that = this;
		$.ajax({
			url: $(that).closest('form').attr('action'),
			type: 'POST',
			data: $(that).closest('form').serializeArray(),
			success: function (html) {
				$('#option_values').html(html);
				$("input, checkbox, select", '#option_values_tbl').aform({triggerChanged: true, showButtons: false});
				$("input, checkbox, select", '.editOption').aform({triggerChanged: true, showButtons: false});
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$('#option_values').html('<div class="error" align="center"><b>' + textStatus + '</b>  ' + errorThrown + '</div>');
			}
		});
		return false;
	});

	//$.aform.styleGridForm('#product_form_option');
});
//--></script>