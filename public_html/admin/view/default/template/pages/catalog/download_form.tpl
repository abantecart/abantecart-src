<?php if ($error_warning) { ?>
	<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div id="aPopup">
	<div class="popup_loading"></div>
</div>

<div class="contentBox">
	<div class="cbox_tl">
		<div class="cbox_tr">
			<div class="cbox_tc">
				<div class="heading icon_title_download"><?php echo $heading_title; ?></div>
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
					<?php echo $form; ?>
			</div>
		</div>
	</div>
	<div class="cbox_bl">
		<div class="cbox_br">
			<div class="cbox_bc"></div>
		</div>
	</div>
</div>


<script type="text/javascript"><!--
	onSelectClose = function (e, ui) {
		if (typeof selectResource == 'undefined')  return;
		$('input[name="mask"]').val(selectResource.name);
		$('#download_link').html('<a href="<?php echo $rl_get_preview; ?>&resource_id=' + selectResource.resource_id + '" target="_blank">' + selectResource.name + '</a>');
	}
//--></script>
