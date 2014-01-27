<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<div id="aPopup">
	<div class="popbox_tl" style="margin-top: 10px;">
		<div class="popbox_tr">
			<div class="popbox_tc"></div>
		</div>
	</div>
	<div class="popbox_cl"><div class="popbox_cr"><div class="popbox_cc message_body" >
		<div class="aform">
			<div class="afield mask2">
				<div class="tl"><div class="tr"><div class="tc"></div></div></div>
				<div class="cl"><div class="cr"><div class="cc">
					<div class="message_text">
						<table id="popup_text" style="width: 100%"></table>
					</div>
				</div></div></div>
				<div class="bl"><div class="br"><div class="bc"></div></div></div>
			</div>
		</div>
	</div></div></div>
	<div class="popbox_bl"><div class="popbox_br"><div class="popbox_bc"></div></div></div>
</div>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_information"><?php echo $heading_title; ?></div>
    <div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
	    <div class="buttons">
		  <a class="btn_toolbar" title="<?php echo $button_insert; ?>" href="<?php echo $insert; ?>">
			<span class="icon_add">&nbsp;</span>
		  </a>
		</div>
    </div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    <?php echo $listing_grid; ?>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>

<script type="text/javascript">
	$('.view.btn_action').live('click',function(){
		show_popup($(this).attr('id'));
	});

	var $aPopup = $('#aPopup');
	var msg_id;
	var show_popup = function(id){
		var $aPopup = $('#aPopup').dialog({
			autoOpen: false,
			modal: true,
			resizable: false,
			width: 550,
			minWidth: 550,
			title: '<?php echo $popup_title;?>',
			buttons:{
				"close": function(event, ui) {
					$(this).dialog('destroy');
				}
			},
			open: function() {},
			resize: function(event, ui){
			},
			close: function(event, ui) {
				$(this).dialog('destroy');
			}
		});

		$aPopup.removeClass('popbox popbox2');

		$.ajax({
			url: '<?php echo $popup_action; ?>',
			type: 'GET',
			dataType: 'text',
			data: 'block_id='+id,
			success: function(data) {
				$('#popup_text').html(data);
				$aPopup.dialog('open');
			}
		});
	}

</script>