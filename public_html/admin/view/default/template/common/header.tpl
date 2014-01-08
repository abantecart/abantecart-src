<div class="header_logo">
	<a href="<?php echo $home; ?>">
		<?php 
			if( $this->getHookVar('logoimage_hookvar') ) {
				echo $this->getHookVar('logoimage_hookvar');
			} else {
		?> 
			<img src="<?php echo $template_dir; ?>image/logo.png" title="<?php echo $heading_title; ?>"/>
		<?php 
			}
		?> 
	</a>
</div>

<div class="header_logo">

	<?php if ($logged) { ?>
		<?php if ($config_voicecontrol) { ?>
			<div id="voice_start">
				<p id="info_start">
					<a data-toggle="modal" href="#voiceModal" id="start_button" onclick="startButton(event)" title="Click on the microphone icon and begin speaking for as long as you like."><i class="icon-microphone icon-2x"></i></a>
				</p>
			</div>

		<?php include($template_dir . '/template/common/voice_controls.tpl'); ?>
		<?php } else { ?>
			<div id="voice_disabled">
				<p>
					<a href="<?php echo $voicecontrol_setting_url; ?>" class="activate_setting" title="Click on the microphone to enable this setting."><i class="icon-microphone-off icon-2x"></i></a>
				</p>
			</div>
		<?php } ?>
	<?php } ?>

</div>

<div class="section1">
	<?php if ($logged) { ?>

		<div class="search_box flt_right">
			<div class="cl">
				<div class="cr">
					<div class="cc">
						<form id="searchform" action="<?php echo $search_action; ?>" method="post">
							<span class="icon_search">&nbsp;</span>
							<input id="global_search" type="text" name="search" value="<?php echo $search_everywhere; ?>"/>
							<a onclick="$('#searchform').submit();"
							   class="btn_search btn_standard"><?php echo $button_go; ?></a>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php if ($languages) { ?>
			<div class="language_box flt_right">
				<div class="cl">
					<div class="cr">
						<div class="cc">
							<form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post"
							      enctype="multipart/form-data" id="language_form">
								<div class="switcher">
									<?php foreach ($languages as $language) { ?>
										<?php if ($language['code'] == $language_code) { ?>
											<div class="selected"><a>
													<?php if ($language['image']) { ?>
														<img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>"/>
													<?php } else {
														echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
													} ?>
													&nbsp;&nbsp;<span><?php echo $language['name']; ?></span></a>
											</div>
										<?php } ?>
									<?php } ?>
									<div class="option">
										<?php foreach ($languages as $language) { ?>
											<a onClick="$('input[name=\'language_code\']').attr('value', '<?php echo $language['code']; ?>'); $('#language_form').submit();">
												<?php if ($language['image']) { ?>
													<img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>"/>
												<?php } else {
													echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
												} ?>
												&nbsp;&nbsp;<?php echo $language['name']; ?>
											</a>
										<?php } ?>
									</div>
									<input type="hidden" name="language_code" value=""/>
									<input type="hidden" name="redirect"
									       value="<?php echo str_replace('&', '&amp;', $redirect); ?>"/>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="store_nav flt_right">
			<a onClick="window.open('<?php echo $store; ?>');"><?php echo $text_front; ?></a>&nbsp;|&nbsp;<?php echo $logged; ?>
			(<a class="top" href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a>)
		</div>

	<?php } ?>
</div>
<div class="section2">
	<?php if ($logged) {
		?>
		<div class="msg_box flt_right">
			<div class="msg_box_tl">
				<div class="msg_box_tr">
					<div class="msg_box_tc"></div>
				</div>
			</div>
			<div class="msg_box_cl">
				<div class="msg_box_cr">
					<div class="msg_box_cc">
						<?php echo $ant; ?>
					</div>
				</div>
			</div>
			<div class="msg_box_bl">
				<div class="msg_box_br">
					<div class="msg_box_bc"></div>
				</div>
			</div>
		</div>

		<a href="<?php echo $messages_link ?>">
			<div>
				<?php
				if ($new_messages) {

					if ($new_messages['N'] > 0) {
						?>
						<div class="n_msg_box" id="notice_msg_box">
							<div id="notice_msg_cnt" class="msg_count"><?php echo $new_messages['N']; ?></div>
						</div>
					<?php
					}
					if ($new_messages['W'] > 0) {
						?>
						<div class="w_msg_box" id="warning_msg_box">
							<div id="warning_msg_cnt" class="msg_count"><?php echo $new_messages['W']; ?></div>
						</div>
					<?php
					}
					if ($new_messages['E'] > 0) {
						?>
						<div class="e_msg_box" id="error_msg_box">
							<div id="error_msg_cnt" class="msg_count"><?php echo $new_messages['E']; ?></div>
						</div>
					<?php
					}
					?>
				<?php } ?>
			</div>
		</a>
	<?php } ?>
</div>

<?php if ($logged) { ?>
	<div id="menu_box"><?php echo $menu; ?></div>
<?php } ?>
<div class="breadcrumb_wrapper">
	<?php if ($breadcrumbs && count($breadcrumbs) > 1) { ?>
		<div class="breadcrumb" style="float: left;">
			<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
			<?php } ?>
		</div>
	<?php } ?>
</div><br class="clr_both">
<div id="suggest_popup_dialog"></div>
<script type="text/javascript">
	$(function () {
		if (!$('#global_search')) return;
		$('#global_search').click(function () {
			if ($(this).val() == '<?php echo $search_everywhere; ?>') {
				$(this).val('');
			}
			$('#global_search').catcomplete('search');
		});


		$('#global_search').bind('keyup', function () {
			if ($(this).val().length < 3) {
				$('.ui-autocomplete.ui-widget-content').hide();
			}
		});

		$('#global_search').focus(function () {
			if ($(this).val() == '<?php echo $search_everywhere; ?>') {
				$(this).val('');
			}
			$('#global_search').catcomplete('search');
		});
		$('#global_search').blur(function () {
			if ($(this).val() == '') {
				$(this).val('<?php echo $search_everywhere; ?>');
			}
			$('#global_search').catcomplete('close');
		});


		$.widget("custom.catcomplete", $.ui.autocomplete, {
			_renderMenu: function (ul, items) {
				var self = this,
					currentCategory;
				$.each(items, function (index, item) {
					if (item.category != currentCategory && item.category != 'undefined') {
						ul.append('<span class="ui-autocomplete-category">' + item.category_name + '<a id="' + item.category + '"><?php echo $text_all_matches;?></a> ' + '</span>');
						currentCategory = item.category;
					}

					if (!item.category) {
						ul.append('<span class="ui-autocomplete-category">' + item.label + '</span>');
						return;
					} else {
						self._renderItem(ul, item);
					}
				});
			},
			_renderItem: function (ul, item) {
				$("<li></li>").data("item.autocomplete", item).append('<a>' + item.label + '</a>').appendTo(ul);
				return;
			},
			// Attempt to cancel the Close event, so when someone makes a selection, the box does not close
			close: function (event, ui) {
				return false;
			}

		});

		var cache = {},
			lastXhr;
		$("#global_search").catcomplete({
			position: { my: 'right top', at: 'right', of: '#global_search', offset: '33 11'},
			minLength: 3,
			focus: function () {
				return false;
			},
			source: function (request, response) {
				var term = request.term;

				if (term in cache) {
					response(cache[ term ]);
					return;
				}

				ajaxrequest = $.getJSON("<?php echo $search_suggest_url; ?>&term=" + term, request, function (data, status, xhr) {
					cache[ term ] = data.response;
					if (xhr === ajaxrequest) {
						if (data.response.length > 0) {
							response(data.response);
						} else {
							response([
								{'label': '<?php echo $text_no_results; ?>'}
							]);
						}
					}
				});
			},
			select: function (e, ui) {
				if (ui.item.controller != '' && ui.item.controller != 'undefined') {
					openSuggestDiag(ui.item);
				} else {
					location = ui.item.page;
				}
				return false;
			}

		});


		$('.ui-autocomplete-category a').live('click', function () {
			location = '<?php echo $search_action ?>&search=' + $('#global_search').val() + '#' + $(this).prop('id');
		});
		$(document).bind('keyup', function (e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			if (code == $.ui.keyCode.ESCAPE) {
				$('.ui-autocomplete.ui-widget-content').hide();
			}
		});

		$(document).bind('mousedown', function (e) {
			if ($(e.target).parents('.ui-autocomplete').length == 0) {
				$('.ui-autocomplete.ui-widget-content').hide();
			}
		});

		var openSuggestDiag = function (item) {
			$('#suggest_popup_dialog').focus();
			var $Popup = $('#suggest_popup_dialog').dialog({
				title: '<?php echo $dialog_title; ?>',
				autoOpen: true,
				modal: true,
				bgiframe: false,
				width: 900,
				height: 500,
				position: 'center',
				draggable: true,
				close: function (event) {
					$('#global_search').focus();
					CKEditor('destroy');
					$(this).dialog('destroy');

				}
			});
			CKEditor('destroy');

			// spinner
			$("#suggest_popup_dialog").html('<div class="progressbar">Loading ...</div>');

			$.ajax({
				url: item.controller + '&target=suggest_popup_dialog',
				type: 'GET',
				dataType: 'json',
				success: function (data) {
					$("#suggest_popup_dialog").html(data.html);
					$('#suggest_popup_dialog').dialog('option', 'title', data.title);
					$('#suggest_popup_dialog').dialog('option', 'height', 'auto');
					$('span[id$="cancel"]').live('click', function () {
						$('#suggest_popup_dialog').dialog("close");
					});
					if ($('#store_switcher').length > 0) {
						$('#store_switcher').aform({ triggerChanged: false })
							.live('change', function () {
								$.getJSON(item.controller + '&target=suggest_popup_dialog&store_id=' + $(this).val(),
									function (response) {
										$('#suggest_popup_dialog').html(response.html);
										CKEditor('destroy');
										CKEditor('add');
									});
							});
					}
					CKEditor('add');
				}
			});
		}


	});

	var CKEditor = function (mode) {
		var settings = [];
		settings[0] = 'qsFrm_config_description_<?php echo $content_language_id; ?>';
		settings[1] = 'qsFrm_config_meta_description';

		for (var k in settings) {

			if ($('#' + settings[k]).length > 0) {
				if (mode == 'add') {
					$('#' + settings[k]).parents('.afield').removeClass('mask2');
					$('#' + settings[k]).parents('td').removeClass('ml_field').addClass('ml_ckeditor');

					CKEDITOR.replace(settings[k], {
						filebrowserBrowseUrl: false,
						filebrowserImageBrowseUrl: '<?php echo $rl; ?>',
						filebrowserWindowWidth: '920',
						filebrowserWindowHeight: '520',
						language: '<?php echo $language_code; ?>'
					});
					$("#edit_dialog").dialog('option', 'width', '800');
				} else {
					var editor = CKEDITOR.instances[settings[k]];
					if (editor) {
						editor.destroy(true);
					}
				}
			}
		}
	}
</script>