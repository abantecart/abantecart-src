<?php

	$exclude_settings = array(
		'cardconnect_status',
		'cardconnect_username',
		'cardconnect_password',
		'cardconnect_merchant_id',
		'cardconnect_test_mode',
	);

	$test_connection_url = $this->html->getSecureURL('r/extension/cardconnect/test');
?>
<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
<?php
echo $resources_scripts;
echo $extension_summary;

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

	<?php  echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">

		<label class="h4 heading"><?php echo $this->config->get('cardconnect_test_mode') ? $text_api_credentials_sandbox : $text_api_credentials; ?></label>
		<?php foreach ($settings as $name => $field) {
			if ( !in_array($name, $exclude_settings) ) {
				continue;
			}

			if (is_integer($field['note'])) {
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
			<label class="control-label col-sm-4 col-xs-12" ><?php echo $cardconnect_test_connection; ?></label>

			<div class="input-group afield <?php echo $widthcasses; ?>">
				<?php
				echo $this->html->buildElement( array(
					'type' => 'button',
					'name' => 'test_connection',
					'title' => $cardconnect_test,
					'text' => $cardconnect_test,
					'style' => 'btn btn-info'
				)); ?>
			</div>
		</div>

		<label class="h4 heading"><?php echo $text_optional_settings; ?></label>
		<?php foreach ($settings as $name=> $field) {
			if ( in_array($name, $exclude_settings)) {
				continue;
			}

			if (is_integer($field['note']) || $field['value']->type == 'hidden') {
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
		<?php } ?>

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
		if ($('#editSettings_cardconnect_status').attr('data-orgvalue') != '1') {
			error_alert('<?php echo $error_turn_extension_on; ?>');
			return false;
		}
		console.log('<?php echo $test_connection_url; ?>');
		$.ajax({
			url: '<?php echo $test_connection_url; ?>',
			type: 'GET',
			dataType: 'json',
			beforeSend: function() {
				$('#test_connection').button('loading');
			},
			success: function(response) {
				if (!response) {
					error_alert( '<?php echo $error_turn_extension_on; ?>' );
					return false;
				}
				if (response['error']) {
					error_alert(response['message']);
				} else {
					success_alert(response['message']);
				}
				$('#test_connection').button('reset');
			},
			error: function(){
				$('#test_connection').button('reset');
			}
		});
		return false;
	});


-->
</script>