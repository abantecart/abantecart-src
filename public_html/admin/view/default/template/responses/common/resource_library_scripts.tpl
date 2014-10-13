<?php echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'rl_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax',
				'title' => $text_resource_library)); ?>

<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/fileupload/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/fileupload/jquery.fileupload-ui.js"></script>

<script type="text/javascript">

$('#rl_modal').appendTo('body'); <?php // move modal at the end of html-body. It needed for exclusion html-form of modal from page html-form?>

var urls = {
			resource_library: '<?php echo $rl_resource_library; ?>',
			resources: '<?php echo $rl_resources; ?>',
			resource_single: '<?php echo $rl_resource_single; ?>',
			map: '<?php echo $rl_map; ?>',
			unmap: '<?php echo $rl_unmap; ?>',
			del: '<?php echo $rl_delete; ?>',
			download: '<?php echo $rl_download; ?>',
			upload: '<?php echo $rl_upload; ?>',
			resource: '<?php echo HTTP_DIR_RESOURCE; ?>'
		},
		default_type = '<?php echo $default_type["type_name"]; ?>';

//vars for open modal.  will purge after modal close!
var modalscope = {
	mode: '',
	wrapper_id: '',
	field_id: ''
};

var rl_error_handler = function(jqXHR, textStatus, errorThrown){
	try {
		var err = $.parseJSON(jqXHR.responseText);
		if (err.hasOwnProperty("error_text")) {
			var errors = err.error_text;
			var errlist = typeof errors === 'string' ? [errors] : errors;

			if (errlist.length > 0) {
				for (var k in errlist) {
					rl_error_alert(errlist[k], false);
				}
			}
		}
	} catch (e) {
		rl_error_alert(jqXHR.responseText, false);
	}
}

/*
 Main resource library modal
 */
var mediaDialog = function (type, action, id) {
	//reset content of modal
	$('#rl_modal .modal-body').html('');
	var src = urls.resource_library + '&action=' + action + '&type=' + type + '&mode=' + modalscope.mode;
	if (id) {
		src += '&resource_id=' + id;
	}
	reloadModal(src);
};

var reloadModal = function (URL) {
	//main ajax call to load rl content
	$.ajax({
		url: URL,
		type: 'GET',
		dataType: 'html',
		global: false,
		success: function (html) {
			var $md = $('#rl_modal');
			var mdb = '#rl_modal .modal-body';
			//remove all open tooltips bootstrap work around
			$('.tooltip.in').remove();
			$(mdb).html('');
			$(mdb).html(html);
			//if modal is not yet open, open and initilize close event
			if (!isModalOpen()) {
				$(mdb).css({height: '560'});
				$md.modal('show');
				$md.unbind('hidden.bs.modal').on('hidden.bs.modal', function () {
					//reload original media list to show new selections
					//not for URL mode
					$(mdb).html('');
					if (modalscope.mode != 'single') {
						<?php 	foreach ($types as $type) { ?>
						loadMedia('<?php echo $type['type_name']?>');
						<?php 	} ?>
					}
				});
			}
			//bind evend in the modal
			bindCustomEvents(mdb);
			bind_rl(mdb);
		},

		error: rl_error_handler

	});
}

var saveRL = function (URL, postdata) {
	var rid;
	//main ajax call to load rl content
	$.ajax({
		url: URL,
		data: postdata,
		type: 'POST',
		dataType: 'json',
		async: false,
		global: false,
		success: function (new_rl_id) {
			rid = new_rl_id;
			rl_success_alert('<?php echo $text_success; ?>', true);
		},

		error: rl_error_handler
	});
	return rid;
}

var loadMedia = function (type, wrapper) {
	wrapper = !wrapper ? '#type_' + type + ' div.type_blocks' : wrapper;
	$.ajax({
		url: urls.resources,
		type: 'GET',
		data: { type: type },
		dataType: 'json',
		global: false,
		success: function (json) {
			if (json.items.length<1 && type != default_type) {
				$( '#type_' + type ).hide();
				$( '#panel_' + type ).hide();
				return;
			}
			$('#type_' + type).show();

			var html = '';
			var t = new Date().getTime();
			$(json.items).each(function (index, item) {
				var src = '';
				if (type == 'image' && item['resource_code']) {
					src = item['thumbnail_url']
				} else {
					<?php // variable t needs to prevent browser caching in case of replacement of file of resource?>
					src = '<img class="img-responsive" src="' + item['thumbnail_url'] + '?t=' + t + '" title="' + item['name'] + '" />';
				}
				html += '<div class="col-xs-3 col-sm-3 col-md-2">';
				html += '<div class="center thumbnail" id="image_row' + item['resource_id'] + '" >\
                <a class="btn resource_edit" data-type="' + type + '" data-rl-id="' + item['resource_id'] + '">' + src + '</a></div>';

				html += '<div class="caption center">';


				html += '<a class="btn resource_edit tooltips" ' +
						'data-type="' + type + '" ' +
						'data-rl-id="' + item['resource_id'] + '" ' +
						'data-original-title="<?php echo $button_edit ?>"><i class="fa fa-edit"></i></a>' +
						'<a class="btn resource_unmap tooltips" ' +
						'data-rl-id="' + item['resource_id'] + '" ' +
						'data-original-title="<?php echo $button_unmap; ?>" ' +
						'data-confirmation="delete" ' +
						'data-confirmation-text="<?php echo $text_confirm_unmap ?>" ' +
						'onclick="unmap_resource(' + item['resource_id'] + ',\'' + json.object_name + '\',\'' + json.object_id + '\');"><i class="fa fa-unlink"></i></a>';

				if (item['can_delete'] == true) {
					html += '<a class="btn resource_delete tooltips" data-rl-id="' + item['resource_id'] + '" ' +
							'data-original-title="<?php echo $button_delete ?>" ' +
							'data-confirmation="delete" ' +
							'data-confirmation-text="<?php echo $text_confirm_del ?>" ' +
							'onclick="delete_resource(' + item['resource_id'] + ',\'' + json.object_name + '\',\'' + json.object_id + '\');"><i class="fa fa-trash-o"></i></a>';
				}

				html += '</div></div></div>';
			});

			html += '<div class="col-xs-3 col-sm-3 col-md-2">' +
					'<div class="center thumbnail fileupload_drag_area">' +
					'<form action="<?php echo $rl_upload; ?>&type=' + type + '" method="POST" enctype="multipart/form-data"><input type="file" name="files[]" multiple="" class="hide">';
			html += '<a class="btn resource_add tooltips transparent" data-type="' + type + '" data-original-title="<?php echo $text_add_media ?>"><img src="<?php echo $template_dir . 'image/icons/icon_add_media.png'; ?>" alt="<?php echo $text_add_media; ?>" width="100" /></a>';
			html += '</form</div></div>';

			$(wrapper).html(html);
		},
		error: rl_error_handler,
		complete: function () {
			bindCustomEvents('#type_' + type);
		}
	});

}

var loadSingle = function (type, wrapper_id, resource_id, field) {
	if (!wrapper_id || wrapper_id == undefined || wrapper_id == null || wrapper_id == '') {
		wrapper_id = modalscope.wrapper_id;
	} else {
		modalscope.wrapper_id = wrapper_id;
	}
	if (!field || field == undefined || field == null || field == '') {
		field = modalscope.field;
	} else {
		modalscope.field = field;
	}

	$.ajax({
		url: urls.resource_single + '&resource_id=' + resource_id,
		type: 'GET',
		data: { type: type },
		dataType: 'json',
		global: false,
		success: function (item) {
			var html = '';
			if (item != null) {
				var t = new Date().getTime();
				var src = '';
				if (type == 'image' && item['resource_code']) {
					src = item['thumbnail_url']
				} else {
					<?php // variable t needs to prevent browser caching in case of replacement of file of resource?>
					src = '<img class="img-responsive" src="' + item['thumbnail_url'] + '?t=' + t + '" title="' + item['name'] + '" />';
				}
				html += '<div class="col-xs-3 col-sm-3 col-md-3">';


				html += '<div class="center thumbnail fileupload_drag_area" id="image_row' + item['resource_id'] + '" >'+
						'<a class="btn resource_edit" ' +
								'data-mode="single" ' +
								'data-type="' + type + '" ' +
								'data-wrapper_id="' + wrapper_id + '" ' +
								'data-field="' + field + '" ' +
								'data-rl-id="' + item['resource_id'] + '">' + src + '</a></div>';
				html += '<a class="btn resource_delete tooltips" data-rl-id="' + item['resource_id'] + '" ' +
						'data-original-title="<?php echo $button_delete ?>" ' +
						'onclick="loadSingle(\'' + type + '\', \'' + wrapper_id + '\', null, \'' + field + '\');"><i class="fa fa-times"></i>&nbsp;<?php echo $button_remove?></a>';
				html += '</div>';

				$('#'+field).val(item['resource_path'].lenght>0 ? item['type_name']+'/'+item['resource_path'] : '');
				$('#'+field+'_resource_id').val(item['resource_id']);


			} else {
				html = '<div class="col-xs-3 col-sm-3 col-md-3"><div class="center thumbnail fileupload_drag_area" >';
				html += '<a class="btn resource_add tooltips transparent" ' +
						'data-mode="single" ' +
						'data-type="' + type + '" ' +
						'data-wrapper_id="' + wrapper_id + '" ' +
						'data-field="' + field + '" ' +
						'data-original-title="<?php echo $text_add_media ?>"><img src="<?php echo $template_dir . 'image/icons/icon_add_media.png'; ?>" alt="<?php echo $text_add_media; ?>" width="100" /></a>';
				html += '</div></div>';

				$('#'+field).val('');
				$('#'+field+'_resource_id').val('');
			}

			$('#' + wrapper_id).html(html);
		},
		error: rl_error_handler
	});

}

jQuery(function () {

	<?php
	if($onload){
		foreach ($types as $type) { ?>
			loadMedia('<?php echo $type['type_name']?>');
		<?php }
	}?>

	$(document).on('click', 'a.resource_add', function () {
		modalscope.mode = $(this).attr('data-mode') ? $(this).attr('data-mode') : ''; //set here mode based on link attribute (in case when we have a few RL single elements in the form)

		if(modalscope.mode=='single'){
			if( $(this).attr('data-wrapper_id') ){
				modalscope.wrapper_id = $(this).attr('data-wrapper_id');
			}
			if( $(this).attr('data-field') ){
				modalscope.field = $(this).attr('data-field');
			}
		}

		mediaDialog($(this).attr('data-type'), 'add');
		return false;
	});

	$(document).on("click", 'a.resource_edit', function () {

		modalscope.mode = $(this).attr('data-mode') ? $(this).attr('data-mode') : ''; //set here mode based on link attribute (in case when we have a few RL single elements in the form)

		if(modalscope.mode=='single'){
			if( $(this).attr('data-wrapper_id') ){
				modalscope.wrapper_id = $(this).attr('data-wrapper_id');
			}
			if( $(this).attr('data-field') ){
				modalscope.field = $(this).attr('data-field');
			}
		}

		mediaDialog($(this).attr('data-type'), 'update', $(this).attr('data-rl-id'));
		return false;
	});

	$(document).on("click", '#resource', function () {
		mediaDialog($(this).attr('data-type'), 'update', $(this).attr('data-rl-id'));
		return false;
	});

	$(document).on("click", '#object', function () {
		mediaDialog($(this).attr('data-type'), 'list_object', $(this).attr('data-rl-id'));
		return false;
	});

	$(document).on("click", '#library', function () {
		mediaDialog($(this).attr('data-type'), 'list_library', $(this).attr('data-rl-id'));
		return false;
	});

	$(document).on("click", '.rl_pagination a', function () {
		if (!$(this).parent().hasClass('disabled')) {
			reloadModal($(this).attr('href'));
		}
		return false;
	});

	$(document).on('click', 'button.rl_add_file', function () {
		$('#choose_resource_type').fadeOut("normal", function () {
			$('#add_form, #file_subform').fadeIn("normal");
		});
	});
	$(document).on('click', 'button.rl_add_code', function () {
		$('#choose_resource_type').fadeOut("normal", function () {
			$('#add_form, #code_subform, #add_resource_buttons').fadeIn("normal");
		});
	});

});


function map_resource(rl_id, object_name, object_id) {
	if (rl_id == null || rl_id == '') {
		return false;
	}
	var URL = urls.map + '&resource_id=' + rl_id + '&mode=' + modalscope.mode + '&type=' + $('#library').attr('data-type');
	if (object_name != undefined && object_name != null && object_name.length > 0) {
		URL += '&object_name=' + object_name + '&object_id=' + object_id;
	}
	$.ajax({
		url: URL,
		type: 'GET',
		dataType: 'json',
		async: false,
		global: false,
		success: function (json) {
			if (json) {
				$('#image_row' + rl_id).parent().remove();
			}
			rl_success_alert('<?php echo $text_map_success; ?>', true);
		},
		error: rl_error_handler
	});
	return false;
}

function unmap_resource(rl_id, object_name, object_id) {
	if (rl_id == null || rl_id == '') {
		return false;
	}
	var URL = urls.unmap + '&resource_id=' + rl_id + '&mode=' + modalscope.mode + '&type=' + $('#library').attr('data-type');
	if (object_name != undefined && object_name != null && object_name.length > 0) {
		URL += '&object_name=' + object_name + '&object_id=' + object_id;
	}
	$.ajax({
		url: URL,
		type: 'GET',
		dataType: 'json',
		async: false,
		global: false,
		success: function (json) {
			if (json) {
				$('#image_row' + rl_id).parent().remove();
			}
			rl_success_alert('<?php echo $text_success_unmap; ?>', true);
		},
		error: rl_error_handler
	});
	return false;
}

function delete_resource(rl_id, object_name, object_id) {
	if (rl_id == null || rl_id == '') {
		return false;
	}
	var URL = urls.del + '&resource_id=' + rl_id;
	if (object_name.length > 0) {
		URL += '&object_name=' + object_name + '&object_id=' + object_id;
	}
	$.ajax({
		url: URL,
		type: 'GET',
		dataType: 'json',
		async: false,
		global: false,
		success: function (json) {
			if (json) {
				$('#image_row' + rl_id).parent().remove();
			}
			if (isModalOpen()) {
				var type = $('#RlFrm_type').val();
				mediaDialog(type, 'list_library');
			}
			rl_success_alert('<?php echo $text_file_delete; ?>', true);
		},
		error: rl_error_handler
	});
	return false;
}

var bind_rl = function (elm) {
	var $obj;
	if (elm) {
		$obj = $(elm);
	} else {
		$obj = $(document).find('html');
	}

	//bind form action if any
	bindAform($("input, checkbox, select, textarea", 'form'));

	$obj.find('.thmb').hover(function () {
		var t = $(this);
		t.find('.ckbox').show();
		t.find('.rl-group').show();
	}, function () {
		var t = $(this);
		if (!t.closest('.thmb').hasClass('checked')) {
			t.find('.ckbox').hide();
			t.find('.rl-group').hide();
		}
	});

	$obj.find('.ckbox').each(function () {
		var t = $(this);
		var parent = t.parent();
		if (t.find('input').is(':checked')) {
			t.show();
			parent.find('.rl-group').show();
			parent.addClass('checked');
		}
	});

	$obj.find('.thmb .ckbox').click(function () {
		if (modalscope.mode == 'single') {
			var rl_id = $(this).find('input.checksign').val()

			loadSingle($('#library').attr('data-type'), null, rl_id);
			$('#rl_modal').modal('hide');
			modalscope.mode = '';
			modalscope.wrapper_id = '';
			modalscope.field_id = '';
			return false;
		}

		var $t = $(this);
		$t.closest('.thmb').toggleClass('checked');
		enable_menu($obj, true);
		//togle checkbox
		$t.find('input:checkbox').attr('checked', function (idx, oldProp) {
			enable_menu($obj, false);
			return !oldProp;
		});
	});

	$obj.find('.rl_select').click(function () {
		if (modalscope.mode == 'single') {
			var rl_id = $(this).attr('data-rl-id');

			loadSingle($('#library').attr('data-type'), null, rl_id);
			$('#rl_modal').modal('hide');
			modalscope.mode = '';
			modalscope.wrapper_id = '';
			modalscope.field_id = '';
		}
		return false;
	});

	$obj.find('#rl_selectall').click(function () {
		if (modalscope.mode == 'single') {
			return false;
		}

		if (this.checked) {
			$('.thmb').each(function () {
				$(this).find('input:checkbox').attr('checked', true);
				$(this).addClass('checked');
				$(this).find('.ckbox, .rl-group').show();
			});
			enable_menu($obj, true);
		} else {
			$('.thmb').each(function () {
				$(this).find('input:checkbox').attr('checked', false);
				$(this).removeClass('checked');
				$(this).find('.ckbox, .rl-group').hide();
			});
			enable_menu($obj, false);
		}
	});

	$obj.find('.rl_link').click(function () {
		var tab = active_tab();
		var tab_id = tab.attr('id');
		var rl_id;
		if (tab_id == 'resource') {
			rl_id = tab.attr('data-rl-id');
		} else {
			rl_id = $(this).attr('data-rl-id');
		}

		if (rl_id < 1 || rl_id == 'undefined') {
			return false;
		}

		var type = $('#library').attr('data-type');

		map_resource(rl_id)

		if (tab_id == 'resource') {
			mediaDialog(type, 'update', rl_id);
		} else {
			tab.click();
		}
		return false;
	});

	$obj.find('.rl_link_multiple').click(function () {
		if (modalscope.mode == 'single') {
			return false;
		}
		multi_action('map');
		return false;
	});

	$obj.find('.rl_unlink').click(function () {
		var rl_id = $(this).attr('data-rl-id');
		if (rl_id < 1 || rl_id == 'undefined') {
			return false;
		}
		unmap_resource(rl_id);
		var tab = active_tab();

		if (tab.attr('id') == 'resource') {
			mediaDialog($(this).attr('data-type'), 'update', rl_id);
		} else {
			tab.click();
		}

		return false;
	});

	$obj.find('.rl_unlink_multiple').click(function () {
		multi_action('unmap');
		return false;
	});

	$obj.find('.rl_save').click(function () {
		//save rl details. 
		var datastring = $('.edit_resource_form').find('[id^="RlFrm_"]').serialize();
		var type = $('#type').val();
		if (type == undefined) {
			type = $('#rl_types').val();
		}
		var src = urls.resource_library + '&type=' + type;
		var rid = $('#RlFrm_resource_id').val();
		if (rid) {
			src += '&resource_id=' + rid + '&action=save';
		} else {
			src += '&action=add';
		}
		var new_rid = saveRL(src, datastring);
		if (rid) {

		} else {
			rid = new_rid;
		}
		mediaDialog(type, 'update', rid);
		return false;
	});

	$obj.find('.rl_save_multiple').click(function () {
		if (modalscope.mode == 'single') {
			return false;
		}

		var thmbs = $('div.thmb.checked');
		if (thmbs.length == 0) {
			return false;
		}

		var postdata = thmbs.find("input[name^='sort_order']").serialize();
		//save rl details.
		var type = $('#library').attr('data-type');
		var src = urls.resource_library + '&action=multisave' + '&type=' + type;
		//main ajax call to load rl content
		$.ajax({
			url: src,
			data: postdata,
			type: 'POST',
			dataType: 'html',
			async: false,
			global: false,
			success: function (html) {
				rl_success_alert('<?php echo $text_success; ?>', true);
			},
			error: rl_error_handler
		});
		$('#object').click(); // reload modal with object's resources
		return false;
	});

	$obj.find('.rl_reset').click(function () {
		//reset rl details. 
		var type = $('#RlFrm_type').val();
		var rid = $('#RlFrm_resource_id').val();
		mediaDialog(type, 'update', rid);
		return false;
	});

	<?php
	   //NOTE! all events for deleting of resource see inside resource_library.tpl and resource_library_edit.tpl in attribute "onclick"
	   // It maden so because user must to confirm action by another js-click-event
	   ?>

	$obj.find('.rl_download').click(function () {
		var rl_id = $(this).attr('data-rl-id');
		var url = urls.download + '&resource_id=' + rl_id;

		var hiddenIFrameID = 'hiddenDownloader',
				iframe = document.getElementById(hiddenIFrameID);
		if (iframe === null) {
			iframe = document.createElement('iframe');
			iframe.id = hiddenIFrameID;
			iframe.style.display = 'none';
			document.body.appendChild(iframe);
		}
		iframe.src = url;

		return false;
	});

	$obj.find('.rl_edit').click(function () {
		mediaDialog(type, 'update', $(this).attr('data-rl-id'));	// поменять на релоад
		return false;
	});

	$('#add_resource').click(function () {
		mediaDialog($(this).attr('data-type'), 'add');
		return false;
	});

	$('#rlsearchform').submit(function () {
		var keyword = $(this).find('input[name=search]').val();
		var type = $(this).find('select[name=rl_types] option:selected').text();
		var url = $(this).attr('action') + '&keyword=' + keyword + '&type=' + type;
		reloadModal(url);
		return false;
	});

	//hook to switch language
	$('#content_language_form').submit(function () {
		var $inputs = $('#content_language_form :input');
		var url = urls.resource_library;
		$inputs.each(function () {
			if (url.indexOf(this.name + '=' + $(this).val()) <= 0) {
				url += '&' + this.name + '=' + $(this).val();
			}
		});
		reloadModal(url);
		return false;
	});

	$('#resource_types_tabs a').click(function () {
		$('#resource_types_tabs li.active').removeClass('active');
		$(this).parents('li').addClass('active');
		return false;
	});
}


var enable_menu = function ($obj, enable) {

	if (enable) {
		$obj.find('.actionitem').removeClass('disabled');
	} else {

		var ch = false;
		$obj.find('.thmb').each(function () {
			if ($(this).hasClass('checked'))
				ch = true;
		});

		if (!ch)
			$obj.find('.actionitem').addClass('disabled');
	}
}

var active_tab = function () {
	return $('#rl_container ul.nav>li.active');
}

var multi_action = function (action) {
	var thmbs = $('div.thmb.checked');
	if (thmbs.length == 0) {
		return false;
	}
	if (action != 'map' && action != 'unmap' && action != 'delete') {
		return false;
	}

	var postdata = '';
	thmbs.each(function () {
		postdata = postdata + action + '[]=' + $(this).attr('data-rl-id') + '&';
	});


	//save rl details.
	var type = $('#library').attr('data-type');
	var src = urls.resource_library + '&action=multisave' + '&type=' + type;
	//main ajax call to load rl content
	$.ajax({
		url: src,
		data: postdata,
		type: 'POST',
		dataType: 'html',
		async: false,
		global: false,
		success: function (html) {
			rl_success_alert('<?php echo $text_success; ?>', true);
		},
		error: rl_error_handler
	});

	active_tab().click(); // reload modal with object's resources
}

var isModalOpen = function () {
	if (typeof $("#rl_modal").data === 'function' && $("#rl_modal").data('bs.modal') != undefined && $("#rl_modal").data('bs.modal').isShown) {
		return true;
	}
	return false;
}

var rl_error_alert = function (text, autohide) {
	if (isModalOpen()) {
		error_alert(text, autohide, '.modal-content');
	} else {
		error_alert(text, autohide);
	}
}

var rl_success_alert = function (text, autohide) {
	if (isModalOpen()) {
		success_alert(text, autohide, '.modal-content');
	} else {
		success_alert(text, autohide);
	}
}


/*UPLOAD FUNCTIONS*/
jQuery(function () {
	var sendFileToServer = function (formData, status) {
		var response = {};
		var extraData = {}; //Extra Data.

		var URL = '';
		if ($('#resource_types_tabs li.active').attr('data-type')) {
			URL = $('div.fileupload_drag_area').find('form').attr('action') + '&type=' + $('#resource_types_tabs li.active').attr('data-type');
		} else {
			URL = $('div.fileupload_drag_area').find('form').attr('action');
		}

		//if need to upload from html-resource element of html-form
		if(!URL){
			URL = urls.upload+'&type='+$('div.fileupload_drag_area').find('a.btn').attr('data-type');
		}

		var jqXHR = $.ajax({
			xhr: function () {
				var xhrobj = $.ajaxSettings.xhr();
				if (xhrobj.upload) {
					xhrobj.upload.addEventListener('progress', function (event) {
						var percent = 0;
						var position = event.loaded || event.position;
						var total = event.total;
						if (event.lengthComputable) {
							percent = Math.ceil(position / total * 100);
						}
						//Set progress
						status.setProgress(percent);
					}, false);
				}
				return xhrobj;
			},
			url: URL,
			type: "POST",
			contentType: false,
			processData: false,
			cache: false,
			data: formData,
			datatype: 'json',
			async: false,
			success: function (data) {
				response = data[0];
				status.setProgress(100);
			}
		});

		status.setAbort(jqXHR);
		return response;
	}

	var rowCount = 0;
	var createStatusbar = function (obj) {
		rowCount++;
		var row = "odd";
		if (rowCount % 2 == 0) row = "even";
		this.statusbar = $("<div class='statusbar row " + row + "'></div>");
		this.filename = $("<div class='filename col-sm-6'></div>").appendTo(this.statusbar);
		this.size = $("<div class='filesize col-sm-2'></div>").appendTo(this.statusbar);
		this.progressBar = $('<div class="progress col-sm-3"><div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div>')
				.appendTo(this.statusbar);
		this.abort = $('<a class="remove btn btn-xs btn-danger-alt tooltips" data-original-title="Abort" title="Abort"><i class="fa fa-minus-circle"></i></a>')
				.appendTo(this.statusbar);
		this.statusbar.appendTo(obj);

		this.setFileNameSize = function (name, size) {
			var sizeStr = "";
			var sizeKB = size / 1024;
			if (parseInt(sizeKB) > 1024) {
				var sizeMB = sizeKB / 1024;
				sizeStr = sizeMB.toFixed(2) + " MB";
			} else {
				sizeStr = sizeKB.toFixed(2) + " KB";
			}

			this.filename.html(name);
			this.size.html(sizeStr);
		}
		this.setProgress = function (progress) {
			var progressBarWidth = progress * this.progressBar.width() / 100;
			this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + "%&nbsp;");
			if (parseInt(progress) >= 100) {
				this.abort.hide();
			}
		}
		this.setAbort = function (jqxhr) {
			var sb = this.statusbar;
			this.abort.click(function () {
				jqxhr.abort();
				sb.hide();
			});
		}
	}

	var handleFileUpload = function (files, obj) {
		if(files.length<1){ return false;}

		$(obj).find('.fileupload-buttonbar').html('');
		var e = 0;
		var rl_type = $('#resource_types_tabs li.active').attr('data-type');

		for (var i = 0; i < files.length; i++) {
			var fd = new FormData();
			fd.append('files', files[i]);

			var status = new createStatusbar($(obj).find('.fileupload-buttonbar')); //Using this we can set progress.
			status.setFileNameSize(files[i].name, files[i].size);
			var response = sendFileToServer(fd, status);
			if (response.hasOwnProperty('error_text')) {
				rl_error_alert('File ' + files[i].name + ' (' + response.error_text + ')', false);
				e++;
			}
		}

		if ((rl_type == undefined || rl_type.length < 1) && response.hasOwnProperty('type')) {
			rl_type = response.type;
		}

		if (e != files.length) {
			if (files.length > 1) {
				mediaDialog(rl_type, 'list_object');
			} else {
				if( modalscope.mode=='single'){
					loadSingle(rl_type, modalscope.wrapper_id, response.resource_id, modalscope.field);
				}
				mediaDialog(rl_type, 'update', response.resource_id);
			}
		} else {
			if (isModalOpen()) {
				mediaDialog(rl_type, 'add');
			}
		}
	}

	var obj = $("body");

	obj.on('dragenter', "div.fileupload_drag_area", function (e) {
		e.stopPropagation();
		e.preventDefault();
	});
	obj.on('dragover', "div.fileupload_drag_area", function (e) {
		$(this).css('border', '2px dotted #F19013');
		e.stopPropagation();
		e.preventDefault();
	});
	obj.on('drop', "div.fileupload_drag_area", function (e) {

		$("div.fileupload_drag_area").css('border', '2px dotted #F19013');
		e.preventDefault();
		var files = e.originalEvent.dataTransfer.files;

		//enable single mode based on attribute
		var btn = $("div.fileupload_drag_area").find('a.btn');
		modalscope.mode = btn.attr('data-mode') ? btn.attr('data-mode') : '';
		modalscope.wrapper_id = btn.attr('data-wrapper_id');
		modalscope.field = btn.attr('data-field');

		//if replacement of file - take only first dragged file
		if ($("div.fileupload_drag_area").attr('data-upload-type') == 'single') {
			files = [files[0]];
		}

		//We need to send dropped files to Server
		handleFileUpload(files, obj);
	});

	var doc = $(document);
	doc.on('dragenter', "div.fileupload_drag_area", function (e) {
		e.stopPropagation();
		e.preventDefault();
	});
	doc.on('dragover', "div.fileupload_drag_area", function (e) {
		e.stopPropagation();
		e.preventDefault();
		obj.css('border', '2px dotted #F19013');
	});
	doc.on('drop', "div.fileupload_drag_area", function (e) {
		obj.css('border', '1px dashed grey');
		e.stopPropagation();
		e.preventDefault();
	});

	$('body').on('change', 'input[name="files\[\]"]', function () {
		obj.css('border', '2px dotted #F19013');
		var files = this.files;
		//We need to send dropped files to Server
		handleFileUpload(files, obj);
	});

});


</script>