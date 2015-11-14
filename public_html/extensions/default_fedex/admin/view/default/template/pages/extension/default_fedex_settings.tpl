<?php
	$test_connection_url = $this->html->getSecureURL('r/extension/default_fedex/test');


	include($tpl_common_dir . 'action_confirm.tpl');

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


<?php echo $form['form_open']; ?>
<div class="panel-body panel-body-nopadding tab-content col-xs-12">

	<?php if ($extension_info['note']) { ?>
	<div class="alert alert-warning alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<i class="fa fa-info-circle fa-fw fa-lg"></i>
		<?php echo $extension_info['note']; ?>
	</div>
	<?php } ?>
	
	<label class="h4 heading"><?php echo ${'tab_' . $section}; ?></label>
	<?php foreach ($settings as $name => $field) {
	if (is_integer($field['note']) || $field['value']->type=='hidden') {
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

	<?php if($name=='default_fedex_test'){?>

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
				'style' => 'btn btn-info lock-on-click'
			)); ?>
		</div>
	</div>

	<?php }
	} ?><!-- <div class="fieldset"> -->
</div>

<?php if ($extension_info['preview']) { ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading"><?php echo $text_preview; ?></label>


		<div class="product_images">
			<div class="main_image center">
				<a href="<?php echo $extension_info['preview'][0]; ?>" title="<?php echo $heading_title; ?>" data-gallery>
					<img class="tooltips img-thumbnail"
						 title="<?php echo $text_enlarge; ?>"
						 width="150" src="<?php echo $extension_info['preview'][0]; ?>" alt="<?php echo $heading_title; ?>"
						 id="image"/>
				</a>
			</div>
			<?php if (count($extension_info['preview']) > 1) { ?>
				<div class="additional_images row">
					<?php for ($i = 1; $i < count($extension_info['preview']); $i++) { ?>
						<div class="col-sm-2">
							<a href="<?php echo $extension_info['preview'][$i]; ?>" data-gallery
							   title="<?php echo $heading_title; ?>">
								<img class="tooltips img-thumbnail"
									 width="50"
									 title="<?php echo $text_enlarge; ?>"
									 src="<?php echo $extension_info['preview'][$i]; ?>"
									 alt="<?php echo $heading_title; ?>"/>
							</a>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php //MODAL FOR IMAGE GALLERY ?>
	<div id="blueimp-gallery" class="blueimp-gallery">
		<!-- The container for the modal slides -->
		<div class="slides"></div>
		<!-- Controls for the borderless lightbox -->
		<h3 class="title"></h3>
		<a class="prev">‹</a>
		<a class="next">›</a>
		<a class="close">×</a>
		<a class="play-pause"></a>
		<ol class="indicator"></ol>
		<!-- The modal dialog, which will be used to wrap the lightbox content -->
		<div class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" aria-hidden="true">&times;</button>
						<h4 class="modal-title"></h4>
					</div>
					<div class="modal-body next"></div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default pull-left prev">
							<i class="fa fa-chevron-left"></i>
							<?php echo $text_previous; ?>
						</button>
						<button type="button" class="btn btn-primary next">
							<?php echo $text_next; ?>
							<i class="fa fa-chevron-right"></i>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
<?php if (!empty($extension_info['dependencies'])) { ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading"><?php echo $text_dependencies; ?></label>
		<table class="table table-striped">
			<thead>
			<tr>
				<th><?php echo $column_id; ?></th>
				<th><?php echo $column_required; ?></th>
				<th><?php echo $column_status; ?></th>
				<th><?php echo $column_action; ?></th>
			</tr>
			</thead>
			<?php foreach ($extension_info['dependencies'] as $item) { ?>
				<tbody>
				<tr class="<?php echo $item['class'] == 'warning' ? 'alert-danger' : ''; ?>">
					<td><?php echo $item['id']; ?></td>
					<td><?php echo($item['required'] ? $text_required : $text_optional); ?></td>
					<td><?php echo $item['status']; ?></td>
					<td><?php
						foreach ($item['actions'] as $key => $action) {
							?>
							<a class="btn_action tooltips <?php echo $action->style; ?>"
							   href="<?php echo $action->href; ?>"
							   title="<?php echo $action->title; ?>"
							   data-original-title="<?php echo $action->title; ?>"
							   target="<?php echo $action->target; ?>"
									<?php if ($key == 'delete') { ?>
										data-confirmation="delete"
										data-confirmation-text="<?php echo $text_delete_confirm; ?>"

									<?php } elseif ($key == 'uninstall') { ?>
										data-confirmation="delete"
										data-confirmation-text="<?php echo $text_uninstall_confirm; ?>"
									<?php } ?>><i class="<?php echo $action->icon; ?> fa-lg"></i>
							</a>
						<?php } ?>
					</td>
				</tr>
				</tbody>
			<?php } ?>
		</table>
		<br/><br/>

	</div>
<?php } ?>
	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			<i class="fa fa-save fa-fw"></i> <?php echo $button_save->text; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $button_restore_defaults->href; ?>">
			<i class="fa fa-arrow-left fa-fw"></i> <?php echo $button_restore_defaults->text; ?>
			</a>
		</div>
	</div>
</form>

</div>

<?php
echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'howto_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax'
		));
?>

<script type="text/javascript">
	<!--

	$("#store_id").change(function () {
		goTo('<?php echo $target_url;?>&store_id=' + $(this).val());
	});

	if ($('#btn_upgrade')) {
		$('#btn_upgrade').click(function () {
			window.open($(this).parent('a').attr('href'), '', 'width=700,height=700,resizable=yes,scrollbars=yes');
			return false;
		});
	}


	$('#test_connection').click(function() {
		if($('#editSettings_default_fedex_status').attr('data-orgvalue')!='1'){
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
				if(response['error']==false) {
					info_alert(response['message']);
				}else{
					error_alert(response['message']);
				}
				$('#test_connection').button('reset');
			}
		});
		return false;
	});

	-->
</script>