<?php

	$exclude_settings = array(
		'store_id',
		'default_pp_standart_status',
		'default_pp_standart_logoimg',
		'default_pp_standart_cartbordercolor',
	);

	if ( !$this->config->get('default_pp_standart_logoimg') ) {
		$custom_logo = 'resources/' . $this->config->get('config_logo');
		$this->config->set('default_pp_standart_logoimg', $custom_logo);
	} else {
		$custom_logo = $this->config->get('default_pp_standart_logoimg');
	}

?>
<div id="aPopup">
	<div class="message_body">
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
							<div class="message_text" id="msg_body"></div>
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
	</div>
</div>

<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $resources_scripts ?>
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
				<?php echo $form_language_switch; ?>
			</div>
			<div class="tools">
				<a class="btn_standard" href="<?php echo $back; ?>"><?php echo $button_back; ?></a>
				<a class="btn_standard" href="<?php echo $reload; ?>"><?php echo $button_reload ?></a>
			</div>
		</div>
	</div>
</div>
<div class="cbox_cl">
<div class="cbox_cr">
<div class="cbox_cc">

<div class="extension_info">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td><img src="<?php echo $extension['icon'] ?>" alt="" border="0"/></td>
			<td><?php echo $extension['name'] ?></td>
			<td><?php echo ($extension['version'] ? $text_version . ': ' . $extension['version'] : ''); ?></td>
			<td><?php echo ($extension['installed'] ? $text_installed_on . ' ' . $extension['installed'] : ''); ?></td>
			<td><?php echo ($extension['date_added'] ? $text_date_added . ' ' . $extension['date_added'] : ''); ?></td>
			<td><?php echo ($extension['license'] ? $text_license . ': ' . $extension['license'] : ''); ?></td>
			<?php if ($add_sett) { ?>
			<td><a class="btn_standard" href="<?php echo $add_sett['link']; ?>"
				   target="_blank"><?php echo $add_sett['text']; ?></a></td>
			<?php
						}
							if ($extension['upgrade']['text']) {
								?>
			<td><a class="btn_standard"
				   href="<?php echo $extension['upgrade']['link'] ?>"><?php echo $extension['upgrade']['text'] ?></a>
			</td>
			<?php } ?>
			<?php if ($extension['help']['file']): ?>
			<td><a class="btn_standard" href="javascript:void(0);"
				   onClick="show_help();"><?php echo $extension['help']['text'] ?></a></td>
			<?php elseif ($extension['help']['ext_link']): ?>
			<td><a class="btn_standard" href="<?php echo $extension['help']['ext_link'] ?>"
				   target="_help"><?php echo $extension['help']['text'] ?></a></td>
			<?php endif; ?>
		</tr>
	</table>
</div>
<div class="fieldset">
	<?php  echo $form['form_open']; ?>

	<?php if ( !ctype_space($settings['store_id']['note']) ) { ?>
	<table class="form">
		<tr>
			<td><?php echo $settings['store_id']['note']; ?></td>
			<td class="ml_field"><?php echo $settings['store_id']['value']; ?></td>
		</tr>
	</table>
	<?php } else { ?>
		<?php echo $settings['store_id']['value']; ?>
	<?php } ?>
	
	<div class="fieldset">
		<div class="top_left"><div class="top_right"><div class="top_mid">
		</div></div></div>
		<div class="cont_left"><div class="cont_right"><div class="cont_mid">

			<table>
				<tr>
					<td width="150" style="padding-right: 40px;"><img src="<?php echo HTTP_EXT . 'default_pp_standart/image/all_in_one_solution_logo_u2645_normal.gif'; ?>"/></td>
					<td width="300" style="padding-right: 40px;">
						<?php echo $text_signup_account_note; ?>
					</td>
					<td style="padding-right: 40px;">
						<a class="btn_standard" target="_blank" href="https://www.paypal.com/us/webapps/mpp/referral/paypal-payments-standard?partner_id=V5VQZUVNK5RT6">
							<span id="button_signup" class="button3" title="Sign Up Now">
								<span><?php echo $button_signup; ?></span>
							</span>
						</a>
					</td>
					<td style="padding-right: 10px;"><?php echo $settings['default_pp_standart_status']['note']; ?></td>
					<td class="ml_field"><?php echo $settings['default_pp_standart_status']['value']; ?></td>
				</tr>
			</table>


		</div></div></div>
		<div class="bottom_left">
			<div class="bottom_right">
				<div class="bottom_mid"></div>
			</div>
		</div>
	</div>

	<div class="fieldset">
		<div class="heading"><?php echo $heading_required_settings; ?></div>
		<div class="top_left"><div class="top_right"><div class="top_mid">
		</div></div></div>
		<div class="cont_left"><div class="cont_right"><div class="cont_mid <?php if ( $this->config->get('default_pp_standart_test') ) { echo 'paypal_sandbox_bg'; } ?>">



			<table class="form">
					<tr>
						<td colspan="2"><?php echo $text_paypal_email_note; ?></td>
					</tr>						

				<?php foreach ($settings as $key => $value) : ?>

					<?php if ( in_array($key, $exclude_settings) ) {
							continue;
						} ?>

					<?php if ( is_integer($value['note']) ) {
							echo $value['value'];
							continue;
						} ?>

					<?php if ( $key == 'default_pp_standart_email' ) { ?>
					<tr>
						<td><?php echo $value['note']; ?></td>
						<td class="ml_field">
							<?php echo preg_replace ('/aform/', 'aform_noaction', $value['value']); ?>
						</td>
					</tr>						
					<tr>
					    <td><?php echo $text_confirm_email; ?></td>
					    <td class="ml_field">
					    	<span class="text_element">
					    		<div class="aform_noaction">
					    			<div class="afield mask1">
					    				<div class="cl"><div class="cr"><div class="cc">
					    					<input id="default_pp_standart_confirm_email" class="atext " type="text" data-orgvalue="" value="" name="default_pp_standart_confirm_email">
					    				</div></div></div>
					    			</div>
					    		</div>
					    	</span>
					    	<span class="required">*</span>
					    </td>
					</tr>
					<?php } else { ?>
					<tr>
						<td><?php echo $value['note']; ?></td>
						<td class="ml_field">
							<?php if (in_array($key, array_keys($resource_field_list))) {
								echo '<div id="' . $key . '">' . $resource_field_list[$key]['value'] . '</div>';
							}
							echo $value['value']; ?>
						</td>
					</tr>						
					<?php } ?>

				<?php endforeach; ?>
			</table>

		</div></div></div>

	<div class="bottom_left">
		<div class="bottom_right">
			<div class="bottom_mid"></div>
		</div>
	</div>
</div>

<div class="fieldset">
	<div class="heading"><?php echo $heading_optional_settings; ?></div>
	<div class="top_left"><div class="top_right"><div class="top_mid">
	</div></div></div>
	<div class="cont_left"><div class="cont_right"><div class="cont_mid">
		<div class="flt_left">
			<table class="form">
				<tr>
					<td colspan="2"><b><?php echo $text_customize_checkout_page; ?></b></td>
				</tr>						
				<tr>
					<td><?php echo $settings['default_pp_standart_logoimg']['note']; ?></td>
					<td class="ml_field">

						<span class="text_element">

							<div class="aform"><div class="afield mask1"><div class="cl"><div class="cr"><div class="cc">

								<input type="text" name="default_pp_standart_logoimg" id="default_pp_standart_logoimg" class="atext " value="<?php echo $custom_logo; ?>" data-orgvalue="0"  />

							</div></div></div></div></div>

						</span>

					</td>

				</tr>
				<tr>
					<td><?php echo $text_custom_logo_preview; ?></td>
					<td><img id="custom_logo_preview" src="<?php echo $custom_logo; ?>" /></td>
				</tr>
				<tr>
					<td><?php echo $settings['default_pp_standart_cartbordercolor']['note']; ?></td>
					<td class="ml_field">#<?php echo $settings['default_pp_standart_cartbordercolor']['value']; ?></td>
					<td></td>
				</tr>
			</table>
		</div>
		<div class="flt_left" style="margin-left: 20px;">
			<img src="<?php echo HTTP_EXT . 'default_pp_standart/image/customize_help.png'; ?>" />
		</div>
		<div class="clr_both"></div>
	</div></div></div>
	<div class="bottom_left">
		<div class="bottom_right">
			<div class="bottom_mid"></div>
		</div>
	</div>
</div>
<div align="center" style="margin-left:-220px;">
	<a class="btn_standard"
	   href="<?php echo $reload ?>&restore=1"><?php echo $button_restore_defaults; ?></a>&nbsp;
	<button class="btn_standard" type="submit"><?php echo $button_save; ?></button>
	&nbsp;
	<?php if ($add_sett) { ?>
	<a class="btn_standard" <?php echo $add_sett['onclick']; ?>
	href="<?php echo $add_sett['link']; ?>"
	target="_blank"><?php echo $add_sett['text']; ?></a>
	<?php } ?>
</div>
</form>



</div>

<?php if ($extension['note']) { ?>
<div class="note"><?php echo $extension['note']; ?></div>
<?php } ?>

<?php if (!empty($extension['dependencies'])) { ?>
<h2><?php echo $text_dependencies; ?></h2>
<table class="list">
	<thead>
	<tr>
		<td class="left"><b><?php echo $column_id; ?></b></td>
		<td class="left"><b><?php echo $column_required; ?></b></td>
		<td class="left"><b><?php echo $column_status; ?></b></td>
		<td class="left"><b><?php echo $column_action; ?></b></td>
	</tr>
	</thead>
	<?php foreach ($extension['dependencies'] as $item) { ?>
	<tbody>
	<tr <?php echo ($item['class'] ? 'class="' . $item['class'] . '"' : ''); ?>>
	<td class="left"><?php echo $item['id']; ?></td>
	<td class="left"><?php echo ($item['required'] ? $text_required : $text_optional); ?></td>
	<td class="left"><?php echo $item['status']; ?></td>
	<td class="left"><?php echo $item['action']; ?></td>
	</tr>
	</tbody>
	<?php } ?>
</table>
<br/><br/>
<?php } ?>

</div>
</div>
</div>
<div class="cbox_bl">
	<div class="cbox_br">
		<div class="cbox_bc"></div>
	</div>
</div>
</div>
</div>
<div id="confirm_dialog"></div>
<script type="text/javascript">
	<!--

	$("#<?php echo $extension['id']; ?>_test").attr('reload_on_save', 'true');

	function show_help(){
		$aPopup = $('#aPopup').dialog({
			autoOpen: false,
			modal: true,
			resizable: false,
			width: 550,
			minWidth: 550,
			buttons:{
			<?php if ( $extension['help']['ext_link'] ) { ?>
			"<?php echo $text_more_help; ?>": function() {
				window.open(
					'<?php echo $extension['help']['ext_link']; ?>',
					'_blank'
				)
			},
			<?php } ?>
			"close": function(event, ui) {
				$(this).dialog('destroy');
			}
		},
		open: function() {
		},

		resize: function(event, ui){
		},
		close: function(event, ui) {
			$(this).dialog('destroy');
			$("#message_grid").trigger("reloadGrid");
		}
	});

	$.ajax({
		url: '<?php echo $extension['help']['file_link']; ?>',
		type: 'GET',
		dataType: 'json',
		success: function(data) {

			$aPopup.dialog( "option", "title", data.title );
			$('#msg_body').html(data.content);

			$aPopup.dialog('open');
		}
	});
}

$(function(){

	$('#default_pp_standart_logoimg').change(function(){
		$('#custom_logo_preview').attr('src', $(this).val());
		$('#custom_logo_preview')
		            .load()
		            .error(function(){
		        		alert('resource not found');
		        });
		return false;
	});

	$("input, textarea, select, .scrollbox", '.contentBox #editSettings').not('.no-save').aform({
		triggerChanged: true,
        buttons: {
            save: '<?php echo str_replace("\r\n", "", $button_save_green); ?>',
            reset: '<?php echo str_replace("\r\n", "", $button_reset); ?>'
        },
        save_url: '<?php echo $update; ?>'
	});

	$("#store_id").change(function(){
		location = '<?php echo $target_url;?>&store_id='+ $(this).val();
	});
<?php  if ($resource_field_list) {
		foreach ($resource_field_list as $name => $resource_field) {
			?>
		$('#<?php echo $name; ?>').click(function(){
        selectDialog('<?php echo $resource_field['resource_type'] ?>', $(this).attr('id'));
        return false;
    });
	<?php } ?>

<?php } ?>

	if($('#btn_upgrade')){
		$('#btn_upgrade').click(function(){
			window.open($(this).parent('a').attr('href'),'','width=700,height=700,resizable=yes,scrollbars=yes');
			return false;
		});
	}

	$('#default_pp_standart_confirm_email').val($('#default_pp_standart_email').val());

	$('#editSettings').submit(function() {
		if ( $('#default_pp_standart_confirm_email').val() != $('#default_pp_standart_email').val() ) {
			$(document).scrollTop( $('#default_pp_standart_confirm_email').offset().top );
			//$('.contentBox').prepend("<div class='warning'><?php echo $error_confirm_email; ?></div>");
			alert("<?php echo $error_confirm_email; ?>");
			return false;
		}
	});


});

-->
</script>
