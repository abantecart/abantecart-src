<div id="rl_add_container">
	<ul class="nav nav-tabs nav-justified nav-profile">
		<li class="active" id="add_resource"  data-type="<?php echo $type; ?>">
			<a class="widthM300" href="javascript:void(0);"><strong><i class="fa fa-plus fa-fw"></i> <?php echo $button_add; ?></strong></a>
		</li>
	</ul>

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
				$active = $type==$rl_type['type_name'] || (!$type && $rl_type['type_name']=='image') ? 'active' : '';
				?>
	        <li class="<?php echo $active; ?>" data-type="<?php echo $rl_type['type_name']; ?>">
				  <a class="actionitem tooltips" data-original-title="<?php echo $text_type.': '.$rl_type['type_name']; ?>"
					 onclick="return false;" href="#"> 
					 <i class="fa <?php echo $icon; ?>"></i>
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
					<div class="col-sm-12 col-xs-12 center">
						<div class="fileupload-buttonbar">
						    <label class="btn tooltips fileinput-button ui-button" role="button" data-original-title="<?php echo $text_upload_files.' '.$text_drag; ?>">
						    	<span class="ui-button-text"><span>
						    		<i class="fa fa-upload" style="font-size: 5em;"></i>
									<p class="ellipsis"><?php echo $text_add_file; ?></p>
						    	</span></span>
						    	<input type="file" name="files[]" <?php echo $mode!='single' ? 'multiple=""' : ''; ?>>
						    </label>
						</div>
					</div>
					<div class="col-sm-12 col-xs-12 center">
						<a class="btn tooltips rl_add_code" data-original-title="<?php echo $text_add_code; ?>" <?php echo $wrapper_id ? 'data-wrapper_id="'.$wrapper_id.'"' :'' ?> <?php echo $field_id ? 'data-field="'.$field_id.'"' :'' ?>>
							<i class="fa fa-file-code-o " style="font-size: 5em;"></i>
						</a>
						<p class="ellipsis"><?php echo $text_add_code; ?></p>
					</div>
					<p class="text-center"><?php echo $text_upload_files.' '.$text_drag; ?></p>
				</div>
			</form>
		</div>

		<?php echo $form['form_open']; ?>
		<div id="code_subform" class="row edit_resource_form">
			<div class="panel-body panel-body-nopadding">
			<?php // resource_code form ?>
				<div>
					<div class="col-sm-12 col-xs-12">
						<div class="form-group <?php echo(!empty($error['resource_code']) ? "has-error" : ""); ?>">
							<label class="control-label" for="<?php echo $form['field_resource_code']->element_id; ?>"><?php echo $text_resource_code; ?></label>
							<div class="input-group afield col-sm-12">
								<?php echo $form['field_resource_code']; ?>
							</div>
						</div>
					</div>
					<!-- col-sm-6 -->
					<div class="col-sm-12 col-xs-12">
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
			<div id="add_resource_buttons">
				<div class="row">
					<div class="center">
						<button class="btn btn-primary rl_save tooltips" title="<?php echo $button_save; ?>">
							<i class="fa fa-save"></i> <?php echo $button_save; ?>
						</button>
						&nbsp;
						<a class="btn btn-default rl_reset tooltips" title="<?php echo $button_reload; ?>" href="<?php echo $cancel; ?>">
							<i class="fa fa-refresh"></i> <?php echo $button_reset; ?>
						</a>
					</div>
				</div>
			</div>
		<!-- <div class="tab-content"> -->
		</div>
		</form>
	</div>