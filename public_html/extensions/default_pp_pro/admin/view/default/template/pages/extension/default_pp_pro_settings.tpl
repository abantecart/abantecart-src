<?php

	$exclude_settings = array(
		'default_pp_pro_status',
		'default_pp_pro_username',
		'default_pp_pro_password',
		'default_pp_pro_signature',
	);

	$test_connection_url = $this->html->getSecureURL('r/extension/default_pp_pro/test');
?>
<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
<?php
echo $resources_scripts;
echo $extension_summary;
echo $tabs;
?>
<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
				<?php echo $this->getHookVar('common_content_buttons'); ?>
				<?php if ($extension_info['help']) {
					if ($extension_info['help']['file']) {
						?>
						<a class="btn btn-white tooltips"
						   href="<?php echo $extension_info['help']['file']['link']; ?>"
						   data-toggle="modal" data-target="#howto_modal"
						   title="<?php echo $text_more_help ?>"><i
									class="fa fa-flask fa-lg"></i> <?php echo $extension_info['help']['file']['text'] ?></a>
					<?php
					}
					if ($extension_info['help']['ext_link']) {
						?>
						<a class="btn btn-white tooltips" target="_blank"
						   href="<?php echo $extension_info['help']['ext_link']['link']; ?>"
						   title="<?php echo $extension_info['help']['ext_link']['text']; ?>"><i
									class="fa fa-life-ring fa-lg"></i></a>

					<?php } ?>
				<?php } ?>
				<?php echo $this->getHookVar('extension_toolbar_buttons'); ?>
			</div>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>
		
	<div class="panel-body panel-body-nopadding">
		<div class="row">
			<div class="col-sm-3 mt10"><img src="<?php echo HTTP_EXT . 'default_pp_pro/image/all_in_one_solution_logo_u2645_normal.gif'; ?>"/></div>
			<div class="col-sm-6"><?php echo $text_signup_account_note; ?></div>
			<div class="col-sm-3 mt10"><a class="btn btn-primary"
			                         target="_blank"
			                         href="https://www.paypal.com/us/webapps/mpp/referral/paypal-payments-pro?partner_id=V5VQZUVNK5RT6"
									 title="Sign Up Now"><?php echo $button_signup; ?></a>
			</div>
		</div>
	</div>

</div>

<div class="panel-body panel-body-nopadding tab-content <?php if ( $this->config->get('default_pp_pro_test') ) { echo 'status_test'; } ?>">
	<?php  echo $form['form_open']; ?>
		<label class="h4 heading"><?php echo $this->config->get('default_pp_pro_test') ? $text_api_credentials_sandbox : $text_api_credentials; ?></label>
		<?php foreach ($settings as $name => $field) {
			if ( !in_array($name, $exclude_settings) ) {
				continue;
			}

			if (is_integer($field['note']) || $field['value']->type == 'hidden' ) {
				echo $field['value'];
				continue;
			}

		//Logic to calculate fields width
		$widthcasses = "col-sm-7";
		if (is_int(stripos($field['value']->style, 'large-field'))) {
			$widthcasses = "col-sm-7";
		} else if (is_int(stripos($field['value']->style, 'medium-field')) || is_int(stripos($field['value']->style, 'date'))) {
			$widthcasses = "col-sm-5";
		} else if (is_int(stripos($field['value']->style, 'small-field')) || is_int(stripos($field['value']->style, 'btn_switch'))) {
			$widthcasses = "col-sm-3";
		} else if (is_int(stripos($field['value']->style, 'tiny-field'))) {
			$widthcasses = "col-sm-2";
		}
		$widthcasses .= " col-xs-12";
		?>
		<div class="form-group <?php if (!empty($error[$name])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-4 col-xs-12"
				   for="<?php echo $field['value']->element_id; ?>"><?php echo $field['note']; ?></label>

			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '') ?>">
				<?php echo $field['value']; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
		<?php } ?>

		<?php //TEST CONNECTION BUTTON ?>

		<div class="form-group">
			<label class="control-label col-sm-4 col-xs-12" ><?php echo $text_test_connection; ?></label>

			<div class="input-group afield <?php echo $widthcasses; ?>">
				<?php
				echo $this->html->buildElement( array(
					'type' => 'button',
					'name' => 'test_connection',
					'title' => $text_test,
					'text' => $text_test,
					'style' => 'btn btn-info'
				)); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-4 col-xs-12" ><?php echo $this->config->get('default_pp_pro_test') ? $text_api_credentials_note_sandbox : $text_api_credentials_note; ?></label>
			<div class="input-group afield <?php echo $widthcasses; ?>">
				<?php
				$title = ($this->config->get('default_pp_pro_test') ? $button_get_api_credentials_sandbox : $button_get_api_credentials);
				echo $this->html->buildElement( array(
					'type' => 'button',
					'id' => 'button_get_api_credentials',
					'name' => 'button_get_api_credentials',
					'href' => "https://www.paypal.com/us/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true",
					'title' => $title,
					'text' => $title,
					'style' => 'btn btn-info'
				)); ?>
			</div>
		</div>

			<label class="h4 heading"><?php echo $text_optional_settings; ?></label>
			<?php foreach ($settings as $name=> $field) {
					if ( in_array($name, $exclude_settings) ) {
						continue;
					}

					if (is_integer($field['note'])) {
						echo $field['value'];
						continue;
					}
					//Logic to calculate fields width
					$widthcasses = "col-sm-7";
					if($name=='default_pp_standart_cartbordercolor'){
						$widthcasses = "col-sm-2";
					}
					$widthcasses .= " col-xs-12";
					?>
					<div class="form-group <?php if (!empty($error[$name])) {echo "has-error";} ?>">
						<label class="control-label col-sm-4 col-xs-12" for="<?php echo $field['value']->element_id; ?>"><?php echo $field['note']; ?></label>
						<div class="input-group afield <?php echo $widthcasses; ?>">
						<?php
							echo $field['value']; ?>
						</div>
						<?php if (!empty($error[$name])) { ?>
							<span class="help-block field_err"><?php echo $error[$name]; ?></span>
						<?php } ?>
					</div>

		<?php } ?><!-- <div class="fieldset"> -->

	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
	    	<button class="btn btn-primary">
				<i class="fa fa-save"></i> <?php echo $button_save->text; ?>
	    	</button>
	    	&nbsp;
	    	<a class="btn btn-default" href="<?php echo $button_restore_defaults->href; ?>">
	    		<i class="fa fa-refresh"></i> <?php echo $button_restore_defaults->text; ?>
	    	</a>
	    <?php if($add_sett){?>
	    	&nbsp;
	    	<a class="btn btn-primary" href="<?php echo $add_sett->href; ?>">
	    		<i class="fa fa-sliders"></i> <?php echo $add_sett->text; ?>
	    	</a>
	    <?php } ?>
		</div>
	</div>
	</form>
</div>


<?php if ($extension['note']) { ?>
	<div class="alert alert-warning"><i class="fa fa-info-circle fa-fw"></i> <?php echo $extension['note']; ?></div>
<?php }


echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'howto_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax'
		));
?>

<script type="text/javascript">
	<!--

	$("#<?php echo $extension['id']; ?>_test").attr('reload_on_save', 'true');

	$('#test_connection').click(function() {
		if($('#editSettings_default_pp_pro_status').attr('data-orgvalue')!='1'){
			error_alert(<?php js_echo($error_turn_extension_on); ?>);
			return false;
		}
		$.ajax({
			url: '<?php echo $test_connection_url; ?>',
			type: 'GET',
			dataType: 'json',
			beforeSend: function() {
				$('#test_connection').button('loading');
			},
			success: function( response ) {
				if ( !response ) {
					error_alert( <?php js_echo($error_turn_extension_on); ?> );
					return false;
				}
				info_alert( response['message'] );
				$('#test_connection').button('reset');
			}
		});
		return false;
	});


-->
</script>