<div id="rl_container">
	<ul class="nav nav-tabs nav-justified nav-profile">
		<li class="active" id="resource" data-rl-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>">
			<a class="widthM300 ellipsis" href="#"><strong><i class="fa fa-plus fa-fw"></i> <?php echo $button_add; ?></strong></a>
		</li>
		<?php if (has_value($object_id)) { ?>
			<li id="object" data-rl-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>">
				<a class="widthM400 ellipsis" href="#"><strong><i class="fa fa-bookmark fa-fw"></i> <?php echo $object_title." (".$object_name.")"; ?></strong></a>
			</li>
		<?php } ?>
		<li id="library" data-rl-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>">
			<a class="widthM300 ellipsis" href="#"><span><i class="fa fa-book fa-fw"></i> <?php echo $heading_title; ?></span></a>
		</li>
	</ul>

	<?php
	$txt_link_resource = "Link to " . $object_title;
	$txt_unlink_resource = "Unlink from " . $object_title;
	?>

	<div class="tab-content rl-content">
		<?php if(sizeof($types)>1){ ?>
		<ul id="resource_types_tabs" class="nav nav-tabs nav-justified nav-profile">
	        <?php foreach($types as $rl_type){
				switch($rl_type['type_name']){
					case 'image':
						$icon = 'fa-file-image-o';
					break;
					case 'audio':
						$icon = 'fa-file-audio-o';
					break;
					case 'video':
						$icon = 'fa-file-movie-o';
					break;
					case 'pdf':
						$icon = 'fa-file-pdf-o';
					break;
					case 'archive':
						$icon = 'fa-file-archive-o';
					break;
					case 'download':
						$icon = 'fa-download';
					break;
					default:
						$icon = 'fa-file';
				}
				$active = $current_type==$rl_type['type_name'] || (!$current_type && $rl_type['type_name']=='image') ? 'active' : '';
				?>
	        <li class="<?php echo $active; ?>" data-type="<?php echo $rl_type['type_name']; ?>">
				  <a class="actionitem tooltips"
					 onclick="return false;"
					 href="#"><i class="fa <?php echo $icon; ?>"></i> <?php echo $rl_type['type_name']; ?>
				  </a>
	        </li>
	        <?php } ?>
		</ul>
		<?php } ?>
		<div id="choose_resource_type" class="row fileupload_drag_area">
			<?php //dnd area?>
			<form action="<?php echo $rl_upload; ?>" method="POST" enctype="multipart/form-data">
				<input class="hide" type="file" name="files[]" <?php echo $mode!='single' ? 'multiple=""' : ''; ?>>
				<div class="fileupload-buttonbar">
					<div class="col-sm-6 col-xs-12 center">
						<a class="btn rl_add_file" <?php echo $wrapper_id ? 'data-wrapper_id="'.$wrapper_id.'"' :'' ?> <?php echo $field_id ? 'data-field="'.$field_id.'"' :'' ?>>
							<i class="fa fa-file-image-o" style="font-size: 10em;"></i>
							<br/><?php echo $text_add_file; ?>
						</a>
					</div>
					<div class="col-sm-6 col-xs-12 center">
						<a class="btn rl_add_code" <?php echo $wrapper_id ? 'data-wrapper_id="'.$wrapper_id.'"' :'' ?> <?php echo $field_id ? 'data-field="'.$field_id.'"' :'' ?>>
							<i class="fa fa-file-code-o " style="font-size: 10em;"></i>
							<br/><?php echo $text_add_code; ?>
						</a>
					</div>
				</div>
			</form>
		</div>

		<div id="file_subform" class="row">
			<div class="panel-body panel-body-nopadding">
				<?php // resource file form ?>
				<div class="col-sm-12 col-xs-12 form-horizontal form-bordered">
					<div class="resource_image center">
						<div class="fileupload_drag_area">
							<form action="<?php echo $rl_upload; ?>" method="POST" enctype="multipart/form-data">
								<div class="fileupload-buttonbar">
									<label class="btn tooltips fileinput-button ui-button  "
										   role="button"
										   data-original-title="<?php echo $text_upload_files.' '.$text_drag; ?>">
										<span class="ui-button-text"><span>
											<i class="fa fa-upload" style="font-size: 14em;"></i>
										</span></span>
										<input type="file" name="files[]" <?php echo $mode!='single' ? 'multiple=""' : ''; ?>>
									</label>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php echo $form['form_open']; ?>
		<div id="code_subform" class="row edit_resource_form">
			<div class="panel-body panel-body-nopadding">
			<?php // resource_code form ?>
				<div>
					<div class="col-sm-8 col-xs-12 form-horizontal form-bordered">
						<div class="form-group <?php echo(!empty($error['resource_code']) ? "has-error" : ""); ?>">
							<label class="control-label" for="<?php echo $form['field_resource_code']->element_id; ?>"><?php echo $text_resource_code; ?></label>
							<div class="input-group afield col-sm-12">
								<?php echo $form['field_resource_code']; ?>
							</div>
						</div>
					</div>
					<!-- col-sm-6 -->
					<div class="col-sm-4 col-xs-12">
						<h3 class="panel-title">&nbsp;</h3>

						<div class="form-group">
							<label class="control-label" for="<?php echo $rl_types->element_id; ?>"><?php echo $text_type; ?></label>
							<div class="input-group afield col-sm-12"><?php echo $rl_types; ?></div>
						</div>

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
							<label class="control-label"  for="<?php echo $form['field_description']->element_id; ?>"><?php echo $text_description; ?></label>

							<div class="input-group afield col-sm-12">
								<?php echo $form['field_description']; ?>
							</div>
						</div>
					</div>
					<!-- col-sm-6 -->
				</div>
			</div>
			<div id="add_resource_buttons" class="panel-footer">
				<div class="row">
					<div class="center">
						<button class="btn btn-primary rl_save">
							<i class="fa fa-save"></i> <?php echo $button_save; ?>
						</button>
						&nbsp;
						<a class="btn btn-default rl_reset" href="<?php echo $cancel; ?>">
							<i class="fa fa-refresh"></i> <?php echo $button_reset; ?>
						</a>
					</div>
				</div>
			</div>
		<!-- <div class="tab-content"> -->
		</div>
		</form>
	</div>