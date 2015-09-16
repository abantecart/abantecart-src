<?php if ($logged) { ?>
	<div class="leftpanel">

		<div class="logopanel">
			<i class="sticky_header fa fa-toggle-off fa-fw"></i>
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
			<i class="sticky_left fa fa-toggle-off fa-fw"></i>

			<!-- This is only visible to small devices -->
			<div class="visible-xs hidden-sm hidden-md hidden-lg">
				<div class="media userlogged">
					<a href="<?php echo $account_edit; ?>">
					<img class="tooltips" src="<?php echo $avatar; ?>" title="<?php echo $username; ?>"/>
					</a>
				</div>
			</div>

			<div id="menu_box">
				<?php echo $menu; ?>
			</div>

			<div class="side_summary">
				<?php include($tpl_common_dir . 'summary.tpl'); ?>
			</div>
			<!-- side_summary -->

			<?php echo $this->getHookVar('leftpanel_bottom'); ?>

		</div>
		<!-- leftpanelinner -->

	</div><!-- leftpanel -->

	<div class="mainpanel">

	<div class="headerbar">

		<a class="menutoggle"><i class="fa fa-bars"></i></a>

		<select id="global_search" name="search" data-placeholder="<?php echo $search_everywhere; ?>" class="chosen-select form-control aselect ">
		<option></option>
		</select>

		<div id="suggest_popup_dialog"></div>

		<div class="header-right">
			<ul class="headermenu">
				<?php echo $this->getHookVar('headermenu_left'); ?>
				<li class="hidden-xs">
					<?php if ($config_voicecontrol) { ?>
						<div class="btn-group" id="voice_start">
							<a data-toggle="modal" href="#voiceModal" id="start_button" class="btn btn-default tp-icon"
							   onclick="startButton(event)"
							   title="Click on the microphone icon and begin speaking for as long as you like.">
								<i class="fa fa-microphone fa-lg"></i>
							</a>
						</div>
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
								<?php if ($ant_viewed <= 0) { ?>
								<span class="badge"><i class="fa fa-bell"></i></span>
								<?php } ?>
							</button>
							<div class="dropdown-menu dropdown-menu-head ant-menu-head pull-right">
								<h5 class="title"><?php echo $text_abc_notification; ?></h5>
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
							<span class="hidden-xs"><?php echo $username; ?></span>
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
				<li class="hidden-xs">
					<div class="btn-group">
						<a onClick="window.open('<?php echo $store; ?>');" class="btn btn-default tp-icon"
						   data-toggle="dropdown" title="<?php echo $text_front; ?>">
							<i class="fa fa-external-link fa-lg"></i>
						</a>
					</div>
				</li>
				<?php echo $this->getHookVar('headermenu_right'); ?>
				<li>
					<button id="right_side_view" class="btn btn-default tp-icon chat-icon">
						<i class="fa fa-folder fa-lg"></i>
					</button>
				</li>
			</ul>
		</div>
		<!-- header-right -->

	</div><!-- headerbar -->

	<!-- modals location outside of headerbar -->
	<?php if ($config_voicecontrol) { ?>
		<?php include($template_dir . '/template/common/voice_controls.tpl'); ?>
	<?php } ?>
	<?php
		echo $this->html->buildElement(
			array(	'type' => 'modal',
					'id' => 'message_modal',
					'modal_type' => 'lg',
					'data_source' => 'ajax'));
	?>

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
$(document).ready(function () {
		
	<?php if (count($breadcrumbs) <= 1 && $ant) { ?>
	//register ant shown in dashboard 
	updateANT('<?php echo $mark_read_url; ?>');
	<?php } ?>
	
	//global seach section 
	$("#global_search").chosen({'width':'260px','white-space':'nowrap'});

	$("#global_search").ajaxChosen({
	    type: 'GET',
	    url: '<?php echo $search_suggest_url; ?>',
	    dataType: 'json',
		jsonTermKey: "term",
		keepTypingMsg: "<?php echo $text_continue_typing; ?>",
		lookingForMsg: "<?php echo $text_looking_for; ?>"   
	}, function (data) {
		if(data.response.length < 1) {
			$("#searchform").chosen({no_results_text: "<?php echo $text_no_results; ?>"});
			return '';
		}
		//build result array 
		var dataobj = new Object;
	    $.each(data.response, function (i, row) {
	    	if(!dataobj[row.category]){
	    		dataobj[row.category] = new Object;
	    		dataobj[row.category].name = row.category_name;
	    		dataobj[row.category].icon = row.category_icon;
	    		dataobj[row.category].items = [];
	    	}
	    	//if controller present need to open modal 
	    	var onclick = 'onClick="window.open(\''+row.page+'\');"';
	    	if (row.controller) {
	    		onclick = ' data-toggle="modal" data-target="#message_modal"' + 'href="'+row.controller+'" ';
	    	}
	    	var html = '<a '+onclick+' "class=search_result" title="'+row.text+'">'+ row.title + '</a>';
			dataobj[row.category].items.push({ value: row.order_id, text: html });
	    });
	    var results = [];
	    var serch_action = '<?php echo $search_action ?>&search=' + $('#global_search_chosen input').val();
	    var onclick = 'onClick="window.open(\''+serch_action+'\');"';
	    results.push({ 
	    	value: 0,
	    	text: '<div class="text-center"><a '+onclick+' class="btn btn-deafult"><?php echo $search_everywhere; ?></a></div>'
	    });
	    $.each(dataobj, function (category, datacat) {
	    	var url = serch_action + '#' + category;
	    	var onclick = 'onClick="window.open(\''+url+'\');"';
	    	var header = '<span class="h5">'+searchSectionIcon(category)+datacat.name+'</span>';
	    	//show more result only if there are more records
	    	if(datacat.items.length == 3) {
	    		header += '<span class="pull-right"><a class="more-in-category" '+onclick+'><?php echo $text_all_matches;?></a></span>';
	    	}
	    	results.push({ 
				group: true,
				text: header, 
				items: datacat.items
			});    	
		});
		//inbind chosen click events 
		$('#global_search_chosen .chosen-results').unbind();
		
		return results;
	});
		
	<?php if(!$home_page) { ?>
	$(window).on('load',function(){
		setTimeout(
			function() {
				$('.ant_window').addClass('open'); //show with delay
				setTimeout(	function() {$('.ant_window').removeClass('open')}, 6000); //hide
			}, 1500
		);
	});

	<?php } ?>
	//update ANT Viewed message only on click
	$('.ant_window button').click(function (event) {
		updateANT('<?php echo $mark_read_url; ?>');
	});
	
	//process side tabs ajax
	$('#right_side_view').click(function (event) {
		//right side not opened yet? load data for first tab
		if(!$('body').hasClass('stats-view')) {
			loadAndShowData('<?php echo $latest_customers_url; ?>', $('#rp-alluser'));
		}
	});

	$('.rightpanel').on('shown.bs.tab', function (e) {
	  var target = $(e.target).attr("href");
	  if(target == '#rp-alluser') {
		loadAndShowData('<?php echo $latest_customers_url; ?>', $('#rp-alluser'));  	
	  } else if(target == '#rp-orders') {
		loadAndShowData('<?php echo $latest_orders_url; ?>', $('#rp-orders'));  		  
	  }
	});
			
});
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