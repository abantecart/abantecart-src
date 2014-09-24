<?php if ($logged) { ?>
	<div class="leftpanel">

		<div class="logopanel">
			<i class="sticky_header fa fa-circle-o fa-fw"></i>
			<a href="<?php echo $home; ?>">
				<?php
				if ($this->getHookVar('logoimage_hookvar')) {
					echo $this->getHookVar('logoimage_hookvar');
				} else {
					?>
					<img class="logo_image" src="<?php echo $template_dir; ?>image/logo.png" width="190"
						 title="<?php echo $heading_title; ?>"/>
				<?php
				}
				?>
			</a>
		</div>
		<!-- logopanel -->

		<div class="leftpanelinner">
			<i class="sticky_left fa fa-circle-o fa-fw"></i>

			<!-- This is only visible to small devices -->
			<div class="visible-xs hidden-sm hidden-md hidden-lg">
				<div class="media userlogged">
					<img src="<?php echo $avatar; ?>" alt="<?php echo $username; ?>"/>

					<div class="media-body">
						<h4><?php echo $username; ?></h4>
					</div>
				</div>

				<h5 class="sidebartitle actitle"><?php echo $last_login; ?></h5>
				<ul class="nav nav-pills nav-stacked nav-bracket mb30">
					<li><a href="<?php echo $account_edit; ?>"><i
									class="fa fa-edit"></i> <?php echo $text_edit_details; ?></a></li>
					<li><a href="<?php echo $logout; ?>"><i class="fa fa-unlock"></i><?php echo $text_logout; ?></a>
					</li>
				</ul>
			</div>

			<div id="menu_box">
				<?php echo $menu; ?>
			</div>

			<div class="side_summary">
				<?php include($template_dir . '/template/common/summary.tpl'); ?>
			</div>
			<!-- side_summary -->

			<?php echo $this->getHookVar('leftpanel_bottom'); ?>

		</div>
		<!-- leftpanelinner -->

	</div><!-- leftpanel -->

	<div class="mainpanel">

	<div class="headerbar">

		<a class="menutoggle"><i class="fa fa-bars"></i></a>

		<form id="searchform" action="<?php echo $search_action; ?>" method="post">
			<input id="global_search" class="form-control" type="text" name="search"
				   placeholder="<?php echo $search_everywhere; ?>"/>
			<a onclick="$('#searchform').submit();"
			   class="btn_search btn_standard"><i class="fa fa-search"></i></a>
		</form>
		<div id="suggest_popup_dialog"></div>

		<div class="header-right">
			<ul class="headermenu">
				<li>
					<?php if ($config_voicecontrol) { ?>
						<div class="btn-group" id="voice_start">
							<a data-toggle="modal" href="#voiceModal" id="start_button" class="btn btn-default tp-icon"
							   onclick="startButton(event)"
							   title="Click on the microphone icon and begin speaking for as long as you like.">
								<i class="fa fa-microphone fa-lg"></i>
							</a>
						</div>
						<?php include($template_dir . '/template/common/voice_controls.tpl'); ?>
					<?php } else { ?>
						<div class="btn-group" id="voice_disabled">
							<a href="<?php echo $voicecontrol_setting_url; ?>"
							   class="btn btn-default tp-icon activate_setting"
							   title="Click on the microphone to enable this setting.">
								<i class="grey_out fa fa-microphone-slash fa-lg"></i>
							</a>
						</div>
					<?php } ?>
				</li>

				<?php if ($ant) { ?>
					<li>
						<div class="btn-group ant_window">
							<button class="btn btn-default dropdown-toggle tp-icon" data-toggle="dropdown">
								<i class="fa fa-comments fa-lg"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-head ant-menu-head pull-right">
								<h5 class="title">From AbanteCart</h5>
								<ul class="dropdown-list gen-list">
									<li>
										<?php echo $ant; ?>
									</li>
								</ul>
							</div>
						</div>
					</li>
				<?php } ?>
				<li>
					<?php
					echo $this->html->buildElement(
							array('type' => 'modal',
									'id' => 'message_modal',
									'modal_type' => 'lg',
									'data_source' => 'ajax'));

					?>
					<div class="btn-group new_messages">
						<a href="" class="btn btn-default dropdown-toggle tp-icon"
						   data-toggle="dropdown">
							<i class="fa fa-envelope fa-lg"></i>
								<span class="badge"></span>
						</a>

						<div class="dropdown-menu dropdown-menu-head pull-right">
							<h5 class="title"></h5>
							<ul class="dropdown-list gen-list"></ul>
						</div>
					</div>
				</li>
				<?php
				 if ($languages) {
				 	$cur_lang;
				 	foreach ($languages as $language) {
						if ($language['code'] == $language_code) {
							$cur_lang = $language;
							break;
						} 
					} 
				?>					
				<li>
						<div class="btn-group">
							<button class="btn btn-default dropdown-toggle tp-icon" data-toggle="dropdown">
			  					<?php if($cur_lang['image']){  ?>
			  					<img src="<?php echo $cur_lang['image']; ?>" title="<?php echo $cur_lang['name']; ?>" />
			  					<?php } else { ?>
			  					<i class="fa fa-language fa-lg"></i>
			  					<?php } ?>
	      						<span class="caret"></span>
							</button>
							<div class="dropdown-menu dropdown-menu-head pull-right switcher">
								<h5 class="title"><?php echo $cur_lang['name']; ?>
								<a href="<?php echo $language_settings; ?>"><i class="fa fa-gears"></i></a>
								</h5>
								<form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post"
									  enctype="multipart/form-data" id="language_form">
									<ul class="dropdown-list gen-list">
										<?php foreach ($languages as $language) { ?>
											<li>
												<a onClick="$('input[name=\'language_code\']').attr('value', '<?php echo $language['code']; ?>'); $('#language_form').submit();">
													<?php if ($language['image']) { ?>
														<img src="<?php echo $language['image']; ?>"
															 title="<?php echo $language['name']; ?>"/>
													<?php
													} else {
														echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
													} ?>
													&nbsp;&nbsp;<span><?php echo $language['name']; ?></span>
												</a>
											</li>
										<?php } ?>
									</ul>
									<input type="hidden" name="language_code" value=""/>
									<input type="hidden" name="redirect"
										   value="<?php echo str_replace('&', '&amp;', $redirect); ?>"/>
								</form>
							</div>
						</div>
					</li>
				<?php } ?>
				<li>
					<div class="btn-group">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							<img src="<?php echo $avatar; ?>" alt="<?php echo $username; ?>"/>
							<?php echo $username; ?>
							<span class="caret"></span>
						</button>

						<div class="dropdown-menu dropdown-menu-head pull-right">
							<h5 class="title">
								<?php echo $last_login; ?>
								<a href="<?php echo $account_edit; ?>"><i class="fa fa-gears"></i></a>
							</h5>
							<ul class="dropdown-list gen-list">
								<li><a href="<?php echo $account_edit; ?>"><i
												class="fa fa-edit"></i> <?php echo $text_edit_details; ?></a></li>
								<li><a href="<?php echo $logout; ?>"><i
												class="fa fa-unlock"></i><?php echo $text_logout; ?></a></li>
							</ul>
						</div>
					</div>
				</li>
				<li>
					<div class="btn-group">
						<a onClick="window.open('<?php echo $store; ?>');" class="btn btn-default tp-icon"
						   data-toggle="dropdown" title="<?php echo $text_front; ?>">
							<i class="fa fa-external-link fa-lg"></i>
						</a>
					</div>
				</li>
				<li>
					<button id="right_side_view" class="btn btn-default tp-icon chat-icon">
						<i class="fa fa-folder fa-lg"></i>
					</button>
				</li>
			</ul>
		</div>
		<!-- header-right -->

	</div><!-- headerbar -->

	<div class="pageheader">
		<?php
		$current = '';
		$breadcrumbs_html = '';
		foreach ($breadcrumbs as $breadcrumb) {
			$breadcrumbs_html .= '<li>';
			if ($breadcrumb['current']) {
				$current = $breadcrumb;
				$breadcrumbs_html .= $breadcrumb['icon'] . $breadcrumb['text'];
			} else {
				$breadcrumbs_html .= '<a href="' . $breadcrumb['href'] . '">' . $breadcrumb['icon'] . $breadcrumb['text'] . '</a>';
			}
			$breadcrumbs_html .= '</li>';
		}
		?>
		<h2>
			<?php if ($current_menu['icon']) { ?>
				<?php echo $current_menu['icon']; ?>
			<?php } else { ?>
			<i class="fa fa-th-list"></i>
				<?php } ?>
			<?php if ($current && $current['text']) {
				echo $current['text'];
			} else {
				echo $heading_title;
			} ?>
			<?php if ($current && $current['sub_text']) { ?>
				<span><?php echo $current['sub_text']; ?></span>
			<?php } ?>
		</h2>

		<?php if ($breadcrumbs && count($breadcrumbs) > 1) { ?>
			<div class="breadcrumb-wrapper">
				<ol class="breadcrumb">
					<?php echo $breadcrumbs_html; ?>
				</ol>
			</div>
		<?php } else if ($ant) { ?>
			<div class="ant-wrapper">
				<?php echo $ant; ?>
			</div>
		<?php } ?>
	</div>

	<script type="text/javascript">
		$(function () {
			if (!$('#global_search')) return;
			$('#global_search').click(function () {
				$('#global_search').catcomplete('search');
			});


			$('#global_search').bind('keyup', function () {
				if ($(this).val().length < 3) {
					$('.ui-autocomplete.ui-widget-content').hide();
				}
			});

			$('#global_search').focus(function () {
				$('#global_search').catcomplete('search');
			});
			$('#global_search').blur(function () {
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


			$('.ui-autocomplete-category a').on('click', function () {
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
						$('span[id$="cancel"]').on('click', function () {
							$('#suggest_popup_dialog').dialog("close");
						});
						if ($('#store_switcher').length > 0) {
							$('#store_switcher').aform({ triggerChanged: false })
									.on('change', function () {
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

<?php } else { ?><!-- not logged in -->

	<div class="mainpanel no-columns">

	<div class="pageheader">
		<div class="logopanel">
			<a href="<?php echo $home; ?>">
				<?php
				if ($this->getHookVar('logoimage_hookvar')) {
					echo $this->getHookVar('logoimage_hookvar');
				} else {
					?>
					<img class="logo_image" src="<?php echo $template_dir; ?>image/logo.png"
						 title="<?php echo $heading_title; ?>"/>
				<?php
				}
				?>
			</a>
		</div>
		<!-- logopanel -->
	</div>

	<script type="text/javascript">
		//remove cokies if loged out
		$(document).ready(function () {
			$.removeCookie("sticky-header");
			$.removeCookie("sticky-leftpanel");
			$.removeCookie("leftpanel-collapsed");
		});
	</script>

<?php } ?>