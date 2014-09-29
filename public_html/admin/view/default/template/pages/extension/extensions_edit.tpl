<?php if (!empty($error['warning'])) { ?>
<div class="warning alert alert-error alert-danger"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<?php echo $resources_scripts ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title"><?php echo $extension['name']; ?></h4>
	</div>
	<div class="panel-body panel-body-nopadding table-responsive" style="display: block;">
		<div class="row">
			<div class="col-sm-1"><img src="<?php echo $extension['icon'] ?>" alt="<?php echo $exrension['name']?>" border="0"/></div>
		<?php if($extension['version']){ ?>
			<div class="col-sm-1"><?php echo $text_version . ': ' . $extension['version']; ?></div>
		<?php }
		if($extension['installed']){ ?>
			<div class="col-sm-4"><?php echo $text_installed_on . ' ' . $extension['installed']; ?></div>
		<?php }
		if($extension['date_added']){ ?>
			<div class="col-sm-4"><?php echo $text_date_added . ' ' . $extension['date_added']; ?></div>
		<?php }
		if($extension['license']){ ?>
			<div class="col-sm-3"><?php echo $text_license . ': ' . $extension['license']; ?></div>
		<?php }
		if ($add_sett) { ?>
			<div class="col-sm-1"><a class="btn btn-primary" href="<?php echo $add_sett['link']; ?>" target="_blank"><?php echo $add_sett['text']; ?></a></div>
		<?php }
		if($extension['upgrade']['text']){ ?>
			<div class="col-sm-1"><a class="btn btn-primary" href="<?php echo $extension['upgrade']['link'] ?>"><?php echo $extension['upgrade']['text'] ?></a></div>
		<?php }	?>


		</div>
		<table id="summary" class="table summary">
			<tr>

				<?php if ($extension['help']['file']){ ?>
					<td><a class="btn btn-primary" href="javascript:void(0);" ><?php echo $extension['help']['text'] ?></a></td>
				<?php }elseif ($extension['help']['ext_link']){ ?>
					<td><a class="btn btn-primary" href="<?php echo $extension['help']['ext_link'] ?>"
						   target="_help"><?php echo $extension['help']['text'] ?></a></td>
				<?php } ?>
			</tr>
		</table>
	</div>
</div>



<div class="tab-content">

	<div class="panel-heading">

			<div class="pull-right">
			    <div class="btn-group mr10 toolbar">
					<?php if($extension['help']){
						if($extension['help']['file']){
							$help_url = $extension['help']['file_link'];
							$help_toggle = 'modal';
						}elseif($extension['help']['ext_link']){
							$help_url = $extension['help']['ext_link'];
							$help_toggle = '_blank';
						}
					?>
                    <a class="btn btn-white"
					   href="<?php echo $help_url; ?>"
					   data-toggle="<?php echo $help_toggle; ?>" data-target="#howto_modal"
					   title="<?php echo $text_help; ?>"><i class="fa fa-flask fa-lg"></i> <?php echo $extension['help']['text']->text ?></a>
                    <?php }
					if (!empty ($help_url)) : ?>
                    <a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip" title="" data-original-title="Help">
                    <i class="fa fa-question-circle fa-lg"></i>
                    </a>
                    <?php endif; ?>
			    </div>

                <?php echo $form_language_switch; ?>
			</div>

	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">

		<label class="h4 heading"><?php echo ${'tab_' . $section}; ?></label>
			<?php foreach ($settings as $name => $field) {
			 		if (is_integer($field['note'])) {
						echo $field['value'];
						continue;
					}

				//Logic to cululate fileds width
				$widthcasses = "col-sm-7";
				if ( is_int(stripos($field['value']->style, 'large-field')) ) {
					$widthcasses = "col-sm-7";
				} else if ( is_int(stripos($field['value']->style, 'medium-field')) || is_int(stripos($field['value']->style, 'date')) ) {
					$widthcasses = "col-sm-5";
				} else if ( is_int(stripos($field['value']->style, 'small-field')) || is_int(stripos($field['value']->style, 'btn_switch')) ) {
					$widthcasses = "col-sm-3";
				} else if ( is_int(stripos($field['value']->style, 'tiny-field')) ) {
					$widthcasses = "col-sm-2";
				}
				$widthcasses .= " col-xs-12";
			?>
		<div class="form-group <? if (!empty($error[$name])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field['value']->element_id; ?>"><?php echo $field['note']; ?></label>
			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
				<?php
				if (in_array($key, array_keys($resource_field_list))) {
					echo '<div id="' . $key . '">' . $resource_field_list[$key]['value'] . '</div>';
					//echo $text_click_to_change;
				}
				echo $field['value']; ?>
			</div>
		    <?php if (!empty($error[$name])) { ?>
		    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
		    <?php } ?>
		</div>
			<?php }  ?><!-- <div class="fieldset"> -->

	</div>

	<div class="panel-footer">
		<div class="row">
		   <div class="col-sm-6 col-sm-offset-3 center">
		     <button class="btn btn-primary">
		     <i class="fa fa-save"></i> <?php echo $button_save->text; ?>
		     </button>&nbsp;
		     <a class="btn btn-default" href="<?php echo $button_restore_defaults->href; ?>">
		     <i class="fa fa-refresh"></i> <?php echo $button_restore_defaults->text; ?>
		     </a>
		   </div>
		</div>
	</div>
	</form>

</div><!-- <div class="tab-content"> -->

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
	/*$("input, textarea, select, .scrollbox", '.contentBox #editSettings').not('.no-save').aform({
		triggerChanged: true,
        buttons: {
            save: '<?php echo str_replace("\r\n", "", $button_save_green); ?>',
            reset: '<?php echo str_replace("\r\n", "", $button_reset); ?>'
        },
        save_url: '<?php echo $update; ?>'
	});*/

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
});

$("#<?php echo $extension['id']; ?>_status").parents('.aswitcher').click(
	function(){
		var switcher = $("#<?php echo $extension['id']; ?>_status");
		var value = switcher.val();
		if(value==1){
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
								$("#<?php echo $extension['id']; ?>_status").parents('.aform').find('.abuttons_grp').find('a:eq(1)').click();
								$( this ).dialog( "destroy" );
						}
				},
				close: function(event, ui) {
							$("#<?php echo $extension['id']; ?>_status").parents('.aform').find('.abuttons_grp').find('a:eq(1)').click();
							$(this).dialog('destroy');
						}

			});

			$.ajax({
						url: '<?php echo $dependants_url; ?>',
						type: 'GET',
						data: 'extension=<?php echo $extension['id']; ?>',
						dataType: 'json',
						success: function(data) {
							if(data=='' || data==null){
								return null;
							}else{
								if(data.text_confirm){
									$('#confirm_dialog').html(data.text_confirm);
								}
								$aPopup.dialog('open');
							}
						}
					});
		}

});
-->
</script>



























<?php
/*
if ($error_warning) { ?>
<div class="warning alert alert-error alert-danger"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
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

					</table>
				</div>

				<?php  echo $form['form_open']; ?>
				<table class="form">
					<?php foreach ($settings as $key => $value){ ?>
					<?php if (is_integer($value['note'])) {
						echo $value['value'];
						continue;
					} ?>
					<tr>
						<td><?php echo $value['note']; ?></td>
						<td class="ml_field">
							<?php
							if (in_array($key, array_keys($resource_field_list))) {
								echo '<div id="' . $key . '">' . $resource_field_list[$key]['value'] . '</div>';
								//echo $text_click_to_change;
							}
							echo $value['value']; ?>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<td align="right"></td>
						<td>
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
						</td>
					</tr>
				</table>
				</form>

				<?php if ($extension['note']) { ?>
				<div class="note"><?php echo $extension['note']; ?></div>
				<?php } ?>

				<?php if ($extension['preview']) { ?>
				<div class="product_images">
					<div class="main_image">
						<a href="<?php echo $extension['preview'][0]; ?>" title="<?php echo $heading_title; ?>">
							<img width="150" src="<?php echo $extension['preview'][0]; ?>"
								 title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" id="image"/>
						</a>
					</div>
					<?php if (count($extension['preview']) > 1) { ?>
					<div class="additional_images">
						<?php for ($i = 1; $i < count($extension['preview']); $i++) { ?>
						<div>
							<a href="<?php echo $extension['preview'][$i]; ?>" title="<?php echo $heading_title; ?>">
								<img width="50" src="<?php echo $extension['preview'][$i]; ?>"
									 title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>"/>
							</a>
						</div>
						<?php } ?>
					</div>
					<?php } ?>
					<br class="clr_both"/>

					<div class="enlarge"><span><?php echo $text_enlarge; ?></span></div>
				</div>
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


<?php */?>
