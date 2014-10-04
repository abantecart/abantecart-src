<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $resources_scripts ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title"><?php echo $extension['name']; ?></h4>
	</div>
	<div class="panel-body panel-body-nopadding table-responsive" style="display: block;">
		<div class="row">
			<div class="col-sm-1"><img src="<?php echo $extension['icon'] ?>" alt="<?php echo $exrension['name'] ?>"
									   border="0"/></div>
			<?php if ($extension['version']) { ?>
				<div class="col-sm-1"><?php echo $text_version . ': ' . $extension['version']; ?></div>
			<?php
			}
			if ($extension['installed']) {
				?>
				<div class="col-sm-4"><?php echo $text_installed_on . ' ' . $extension['installed']; ?></div>
			<?php
			}
			if ($extension['date_added']) {
				?>
				<div class="col-sm-4"><?php echo $text_date_added . ' ' . $extension['date_added']; ?></div>
			<?php
			}
			if ($extension['license']) {
				?>
				<div class="col-sm-3"><?php echo $text_license . ': ' . $extension['license']; ?></div>
			<?php
			}
			if ($add_sett) { ?>
				<div class="col-sm-1"><a class="btn btn-primary" href="<?php echo $add_sett->href; ?>"
										 target="_blank"><?php echo $add_sett->text; ?></a></div>
			<?php }
			if ( $upgrade_button ) { ?>
				<div class="col-sm-1"><a class="btn btn-primary" href="<?php echo $upgrade_button->href ?>"><?php echo $upgrade_button->text ?></a></div>
			<?php } ?>
			<?php echo $this->getHookVar('extension_summary_item'); ?>
		</div>
	</div>
</div>


<div class="tab-content">

<div class="panel-heading">

	<div class="pull-right">
		<div class="btn-group mr10 toolbar">
			<?php if ($extension['help']) {
				if ($extension['help']['file']) {
					?>
					<a class="btn btn-white tooltips"
					   href="<?php echo $extension['help']['file']['link']; ?>"
					   data-toggle="modal" data-target="#howto_modal"
					   title="<?php echo $text_more_help ?>"><i
								class="fa fa-flask fa-lg"></i> <?php echo $extension['help']['file']['text'] ?></a>
				<?php
				}
				if ($extension['help']['ext_link']) {
					?>
					<a class="btn btn-white tooltips" target="_blank"
					   href="<?php echo $extension['help']['ext_link']['link']; ?>"
					   title="<?php echo $extension['help']['ext_link']['text']; ?>"><i
								class="fa fa-life-ring fa-lg"></i></a>

				<?php } ?>

				<?php echo $this->getHookVar('extension_toolbar_buttons'); ?>

			<?php
			}
			if (!empty ($help_url)) : ?>
				<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip"
				   title="" data-original-title="Help">
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
	<div class="form-group <? if (!empty($error[$name])) {
		echo "has-error";
	} ?>">
		<label class="control-label col-sm-3 col-xs-12"
			   for="<?php echo $field['value']->element_id; ?>"><?php echo $field['note']; ?></label>

		<div class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '') ?>">
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
	<?php } ?><!-- <div class="fieldset"> -->
</div>

<?php if ($extension['preview']) { ?>
	<div class="panel-body panel-body-nopadding">
		<label class="h4 heading"><?php echo $text_preview; ?></label>


		<div class="product_images">
			<div class="main_image center">
				<a href="<?php echo $extension['preview'][0]; ?>" title="<?php echo $heading_title; ?>" data-gallery>
					<img class="tooltips img-thumbnail"
						 title="<?php echo $text_enlarge; ?>"
						 width="150" src="<?php echo $extension['preview'][0]; ?>" alt="<?php echo $heading_title; ?>"
						 id="image"/>
				</a>
			</div>
			<?php if (count($extension['preview']) > 1) { ?>
				<div class="additional_images row">
					<?php for ($i = 1; $i < count($extension['preview']); $i++) { ?>
						<div class="col-sm-2">
							<a href="<?php echo $extension['preview'][$i]; ?>" data-gallery
							   title="<?php echo $heading_title; ?>">
								<img class="tooltips img-thumbnail"
									 width="50"
									 title="<?php echo $text_enlarge; ?>"
									 src="<?php echo $extension['preview'][$i]; ?>"
									 alt="<?php echo $heading_title; ?>"/>
							</a>
						</div>
					<?php } ?>
				</div>

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
										<i class="glyphicon glyphicon-chevron-left"></i>
										<?php echo $text_previous; ?>
									</button>
									<button type="button" class="btn btn-primary next">
										<?php echo $text_next; ?>
										<i class="glyphicon glyphicon-chevron-right"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>

			<?php } ?>
		</div>
	</div>
<?php } ?>
<?php if (!empty($extension['dependencies'])) { ?>
	<div class="panel-body panel-body-nopadding">
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
			<?php foreach ($extension['dependencies'] as $item) { ?>
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
<div class="panel-footer">
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3 center">
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

	$("#store_id").change(function () {
		goTo('<?php echo $target_url;?>&store_id=' + $(this).val());
	});

	if ($('#btn_upgrade')) {
		$('#btn_upgrade').click(function () {
			window.open($(this).parent('a').attr('href'), '', 'width=700,height=700,resizable=yes,scrollbars=yes');
			return false;
		});
	}

	<?php if($has_dependants){ ?>


	$("#editSettings_<?php echo $extension['id']; ?>_status_layer>button").on('click', function () {

		var switcher = $("#editSettings_<?php echo $extension['id']; ?>_status");
		var value = switcher.val();

		if (value != 1) {
			return false;
		}
		$('#howto_modal').modal({remote: '<?php echo $dependants_url; ?>' }).show();
		$('#howto_modal').on('shown.bs.modal', function () {
			$('#modal_confirm').click(function () {
				$("#editSettings").prop('changed', 'submit').prop('data-confirm-exit', 'false').submit();
			});
		});
		$('#howto_modal').on('hidden.bs.modal', function () {
			switcher.next('.quicksave').find('a.icon_reset').click();
		});

	});

	<?php } ?>
	-->
</script>