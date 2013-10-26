<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
	<div class="cbox_tl">
		<div class="cbox_tr">
			<div class="cbox_tc">
				<div class="heading icon_information"><?php echo $heading_title; ?></div>
				<div class="toolbar">
					<?php if (!empty ($help_url)) : ?>
					<div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img
							src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
					<?php endif; ?>
					<div class="buttons">
						<div class="flt_left align_left"><?php echo $text_select_store; ?>
							&nbsp;&nbsp;<?php echo $store_selector; ?></div>
						<div class="flt_left">&nbsp;&nbsp;&nbsp;<a class="btn_standard"
																   href="<?php echo $extensions_store ?>"><?php echo $btn_extensions_store ?></a>
						</div>
						<div class="flt_left">&nbsp;&nbsp;&nbsp;<a class="btn_standard"
																   href="<?php echo $install_new ?>"><?php echo $btn_add_new ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="cbox_cl">
		<div class="cbox_cr">
			<div class="cbox_cc">
				<?php echo $listing_grid; ?>
			</div>
		</div>
	</div>
	<div class="cbox_bl">
		<div class="cbox_br">
			<div class="cbox_bc"></div>
		</div>
	</div>
</div>


<div id="aPopup">

	<div class="popbox_tl">
		<div class="popbox_tr">
			<div class="popbox_tc"></div>
		</div>
	</div>
	<div class="error" id="error">
		<div class="warning alert alert-error"><p id="error_text"></p></div>
		<div class="align_center" style="margin-top: 7px;">
			<a class="btn_standard"><?php echo $close; ?></a></div>
	</div>
	<div id="license" class="popbox_cl">
		<div class="popbox_cr">
			<div class="popbox_cc">
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

									<div class="message_text" id="license_text"></div>
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
				<div class="align_center" style="margin-top: 7px;">
					<a class="btn_standard"><?php echo $cancel_install; ?></a>&nbsp;<a class="btn_standard" id="agree"
																					   href=""><?php echo $agree_install; ?></a>
				</div>
			</div>
		</div>
	</div>
	<div class="popbox_bl">
		<div class="popbox_br">
			<div class="popbox_bc"></div>
		</div>
	</div>
</div>
<div id="confirm_dialog"></div>

<script type="text/javascript">
	<!--

	var $aPopup = $('#aPopup');
	var msg_id;
	function show_popup(extension,installURL){
		$aPopup = $('#aPopup').dialog({
			autoOpen: false,
			modal: true,
			resizable: false,
			dialogClass: 'aPopup',
			width: 550,
			minWidth: 550,
			resize: function(event, ui){
			},
			close: function(event, ui) {
				$(this).dialog('destroy');
			}
		});

		$aPopup.removeClass('popbox popbox2');
		$.ajax({
			url: '<?php echo $license_url; ?>',
		type: 'GET',
		data: 'extension='+extension,
		dataType: 'json',
		success: function(data) {

			if(data=='' || data==null){
				$(window.location).attr('href', installURL);
			}else{
				$aPopup.addClass("popbox2");
				if(data.license_text){
					$('#license').show();
					$('#error').hide();
					$('#license_text').html(data.license_text);
				}else{
					$('#license').hide();
					$('#error').show();
					$('#error_text').html(data.error_text);
				}
				$('#agree').attr('href',installURL);
				$aPopup.dialog('open');
			}
		}
	});
}

$("td[aria-describedby='extension_grid_status']").find('.aswitcher').live('click',
	function(){
		var switcher = $(this).find("input[type='checkbox']");
		var value = switcher.val();
		if(value==0){
			var extension = $("td[aria-describedby='extension_grid_key']").html();
			$aPopup = $('#confirm_dialog').dialog({
				autoOpen: false,
				modal: true,
				resizable: false,
				height: 'auto',
				minWidth: 100,
				buttons: {
							"<?php echo $button_agree;?>": function() {
								$( this ).dialog( "destroy" );
							},
							"<?php echo $button_cancel;?>": function() {
								$("td[aria-describedby='extension_grid_status']").find('.abuttons_grp').find('a:eq(1)').click();
								$( this ).dialog( "destroy" );
						}
				},
				close: function(event, ui) {
							$("td[aria-describedby='extension_grid_status']").find('.abuttons_grp').find('a:eq(1)').click();
							$(this).dialog('destroy');
						}

			});

			$.ajax({
						url: '<?php echo $dependants_url; ?>',
						type: 'GET',
						data: 'extension='+extension,
						dataType: 'json',
						success: function(data) {

							if(data=='' || data==null){
								return null;
							}else{
								if(data.text_confirm){
									$('#confirm_dialog').html(data.text_confirm)

								}
								$aPopup.dialog('open');
							}
						}
					});
		}

});

-->
</script>