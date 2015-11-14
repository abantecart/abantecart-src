<div id="rl_container">
<ul class="nav nav-tabs nav-justified nav-profile">
	<li class="active" id="resource" data-rl-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>">
		<a class="widthM400 ellipsis" href="#">
			<strong><i class="fa fa-info-circle fa-fw"></i> <?php echo $resource['name']; ?></strong>
		</a>
	</li>
	<?php if (has_value($object_id)) { ?>
		<li id="object" data-rl-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>">
			<a class="widthM300 ellipsis" href="#"><strong><i class="fa fa-bookmark fa-fw"></i> <?php echo $object_title . " (" . $object_name . ")"; ?></strong></a>
		</li>
	<?php } ?>
	<li id="library" data-rl-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>">
		<a class="widthM300 ellipsis" href="#"><span><i class="fa fa-book fa-fw"></i> <?php echo $heading_title; ?></span></a>
	</li>
</ul>

<?php
$txt_link_resource = sprintf($text_map_to, $object_title);
$txt_unlink_resource = sprintf($text_unmap_from, $object_title);
?>

<div class="tab-content rl-content">
<ul class="reslibrary-options">
	<li>
		<a id="add_resource" class="btn btn-xs btn-default add_resource tooltips"
		   data-original-title="<?php echo $button_add; ?>" data-type="<?php echo $type; ?>"><i class="fa fa-plus"></i></a>
	</li>
	<li>
		<a class="actionitem rl_download" data-rl-id="<?php echo $resource['resource_id']; ?>" href="#"
		   onclick="return false;"><i class="fa fa-download"></i></a>
	</li>
	<?php if ($resource['mapped_to_current']) { ?>
		<li>
			<a class="actionitem rl_unlink tooltips" data-rl-id="<?php echo $resource['resource_id']; ?>"
			   onclick="return false;" href="#" data-original-title="<?php echo $txt_unlink_resource; ?>">
				<i class="fa fa-unlink"></i>
			</a>
		</li>
	<?php } else if ((int)$object_id) { ?>
		<li>
			<a class="actionitem rl_link tooltips"
			   data-rl-id="<?php echo $resource['resource_id']; ?>"
			   data-type="<?php echo $type; ?>"
			   onclick="return false;" href="#" data-original-title="<?php echo $txt_link_resource; ?>">
				<i class="fa fa-link"></i>
			</a>
		</li>
	<?php } ?>
	<li>
		<?php
		//disable delete button for multi-linked resource
		if ($resource['can_delete']) {
			$cssclass = $onclick = "";
			if ($resource['mapped_to_current']) {
				$onclick = "delete_resource(" . $resource['resource_id'] . ", '" . $object_name . "', '" . $object_id . "');";
			}else{
				$onclick = "delete_resource(" . $resource['resource_id'] . ", '" . $object_name . "', '" . $object_id . "');";
			}
		} else {
			$cssclass = "disabled";
			$onclick = "";
		}
		?>
		<a class="actionitem <?php echo $cssclass; ?> rl_delete"
		   href="#"
		   onclick="<?php echo $onclick; ?> return false;"
		   data-rl-id="<?php echo $resource['resource_id']; ?>"
		   data-confirmation="delete"
		   data-confirmation-text="<?php echo $text_confirm_delete; ?>"
				><i class="fa fa-trash-o"></i></a>
	</li>
	<?php if ($form_language_switch) { ?>
		<li><?php echo $form_language_switch; ?></li>
	<?php } ?>
	<?php if (!empty ($help_url)) { ?>
		<li>
			<a class="btn btn-white btn-xs tooltips" href="<?php echo $help_url; ?>" target="new" title=""
			   data-original-title="Help">
				<i class="fa fa-question-circle fa-lg"></i>
			</a>
		</li>
	<?php } ?>

</ul>

<div class="row edit_resource_form">
	<div class="col-sm-6 col-xs-12">
		<?php if (!empty ($resource['resource_code'])) { ?>

			<div class="form-group <?php echo(!empty($error['resource_code']) ? "has-error" : ""); ?>">
				<label class="control-label"
				       for="<?php echo $form['field_resource_code']->element_id; ?>"><?php echo $text_resource_code; ?></label>

				<div class="input-group afield col-sm-12">
					<?php echo $form['field_resource_code']; ?>
				</div>
			</div>

		<?php } else { ?>

			<div class="resource_image center">
				<a target="_preview"
				   href="<?php echo $rl_get_preview; ?>&resource_id=<?php echo $resource['resource_id']; ?>&language_id=<?php echo $resource['language_id']; ?>"
				   title="<?php echo $text_preview; ?>">
					<?php // NOTE: USE time as parameter for image to prevent caching of thumbnail (in case of replacement of resource file)?>
					<img src="<?php echo $resource['thumbnail_url']; ?>?t=<?php echo time(); ?>"
					     title="<?php echo $resource['title']; ?>"/>
				</a>
			</div>
			<?php // upload form for file replacement ?>
			<div class="form-group fileupload_drag_area" data-upload-type="single">
				<form name="RlRplc" action="<?php echo $rl_replace; ?>" method="POST" enctype="multipart/form-data"
				      fole="form">
					<div class="fileupload-buttonbar col-sm-12">
						<label class="btn btn-block tooltips fileinput-button ui-button"
						       role="button"
						       data-original-title="<?php echo $text_replace_file . ' ' . $text_drag; ?>">
							<span class="btn btn-primary btn-block ui-button-text"><span><i
											class="fa fa-upload"></i><?php echo $text_replace_file; ?></span></span>
							<input type="file" name="files[]">
						</label>
					</div>
				</form>
			</div>

		<?php } ?>

		<div class="form-group">
			<?php if($resource['resource_objects'] || $mode!='single'){ ?>
			<label class="col-sm-6 control-label"><?php echo $text_mapped_to; ?></label>

			<div class="col-sm-3">
				<div class="btn-group maped_resources">
					<?php
					if (is_array($resource['resource_objects']) ) {
						$total_cnt = 0;
						?>
						<div class="dropdown-menu dropdown-menu-sm pull-right">
							<?php
							foreach ($resource['resource_objects'] as $obj_area => $items) {
								?>
								<h5 class="title"><?php echo $obj_area; ?></h5>
								<ul class="dropdown-list dropdown-list-sm">
									<?php
									foreach ($items as $item) {
										$total_cnt++; ?>
										<li>
											<a href="<?php echo $item['url']; ?>" target="_new"
											   data-object-id="<?php echo $item['object_id']; ?>">
												<?php echo $item['name']; ?>
											</a>
										</li>

									<?php } ?>
								</ul>
							<?php } ?>
						</div>
					<?php } ?>
					<button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle">
						<i class="fa fa-external-link fa-lg"></i>&nbsp;
						<span class="caret"></span>&nbsp;
						<span class="badge"><?php echo $total_cnt; ?></span>&nbsp;
					</button>
				</div>
			</div>
			<?php
			}
			if ($mode == 'single') { ?>
				<div class="col-sm-3">
					<a class="btn btn-default btn-xs rl_select tooltips"
					   data-original-title="<?php echo $txt_select_resource; ?>"
					   data-rl-id="<?php echo $resource_id; ?>"
					   data-type="<?php echo $type; ?>"><i class="fa fa-check-square-o fa-lg"></i>
					</a>
				</div>
			<?php } else if ($resource['mapped_to_current']) { ?>
				<div class="col-sm-3">
					<a class="btn btn-default btn-xs rl_unlink tooltips"
					   data-original-title="<?php echo $txt_unlink_resource; ?>"
					   data-rl-id="<?php echo $resource_id; ?>"
					   data-type="<?php echo $type; ?>"><i class="fa fa-unlink fa-lg"></i>
					</a>
				</div>
			<?php } else if (has_value($object_id)) { ?>
				<div class="col-sm-3">
					<a class="btn btn-default btn-xs rl_link tooltips"
					   data-original-title="<?php echo $txt_link_resource; ?>"
					   data-rl-id="<?php echo $resource_id; ?>"
					   data-type="<?php echo $type; ?>"><i class="fa fa-link fa-lg"></i>
					</a>
				</div>
			<?php } ?>

		</div>

	</div>
	<!-- col-sm-6 -->

	<div class="col-sm-6 col-xs-12">
		<?php if ($mode == 'new') { ?>
			<div class="form-group">
				<div class="input-group afield col-sm-12">
					<?php echo $rl_types; ?>
				</div>
			</div>
		<?php } else { ?>
			<?php echo $form['field_resource_id']; ?>
			<?php echo $form['field_type']; ?>
		<?php } ?>

		<div class="form-group <?php echo(!empty($error['name']) ? "has-error" : ""); ?>">
			<label class="control-label"
			       for="<?php echo $form['field_name']->element_id; ?>"><?php echo $text_name; ?></label>

			<div class="input-group afield col-sm-12">
				<?php echo $form['field_name']; ?>
			</div>
		</div>

		<div class="form-group <?php echo(!empty($error['title']) ? "has-error" : ""); ?>">
			<label class="control-label"
			       for="<?php echo $form['field_title']->element_id; ?>"><?php echo $text_title; ?></label>

			<div class="input-group afield col-sm-12">
				<?php echo $form['field_title']; ?>
			</div>
		</div>

		<div class="form-group <?php echo(!empty($error['description']) ? "has-error" : ""); ?>">
			<label class="control-label"
			       for="<?php echo $form['field_description']->element_id; ?>"><?php echo $text_description; ?></label>

			<div class="input-group afield col-sm-12">
				<?php echo $form['field_description']; ?>
			</div>
		</div>

	</div>
	<!-- col-sm-6 -->
</div>

<div class="panel-footer">
	<div class="row">
		<div class="center">
			<a class="btn btn-primary rl_save" href="#">
				<i class="fa fa-save"></i> <?php echo $button_save; ?>
			</a>
			&nbsp;
			<?php if ($mode == 'single') { ?>
				<a class="btn btn-primary rl_save rl_select"
				        data-rl-id="<?php echo $resource_id; ?>"
				        data-type="<?php echo $type; ?>">
					<i class="fa fa-save"></i> <?php echo $button_save_n_apply; ?>
				</a>&nbsp;
			<?php } else { ?>
				<a class="btn btn-primary rl_link rl_save rlclose" href="#">
					<i class="fa fa-save"></i> <?php echo $button_save_n_apply; ?>
				</a>&nbsp;
			<?php } ?>
			<a class="btn btn-default rl_reset" href="<?php echo $cancel; ?>">
				<i class="fa fa-refresh"></i> <?php echo $button_reset; ?>
			</a>
		</div>
	</div>
</div>
</div>
<!-- <div class="tab-content"> -->

</div>