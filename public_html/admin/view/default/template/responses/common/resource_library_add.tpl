<div id="rl_container">
	<ul class="nav nav-tabs nav-justified nav-profile">
		<li class="active" id="resource" data-rl-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>"><a
					class="widthM400 ellipsis" href="#"><strong><?php echo $button_add; ?></strong></a></li>
		<?php if (has_value($object_id)) { ?>
			<li id="object" data-rl-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>"><a
						class="widthM400 ellipsis"
						href="#"><strong><?php echo $object_title." (".$object_name.")"; ?></strong></a></li>
		<?php } ?>
		<li id="library" data-rl-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>"><a
					class="widthM400 ellipsis" href="#"><span><?php echo $heading_title; ?></span></a></li>
	</ul>

	<?php
	$txt_link_resource = "Link to " . $object_title;
	$txt_unlink_resource = "Unlink from " . $object_title;
	?>

	<div class="tab-content rl-content">
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
				  <a class="itemopt tooltips"
					 onclick="return false;"
					 href="#"><i class="fa <?php echo $icon; ?>"></i> <?php echo $rl_type['type_name']; ?>
				  </a>
	        </li>
	        <?php } ?>
		</ul>

		<div id="choose_resource_type" class="row">
			<div class="col-sm-6 col-xs-12 center">
				<button class="tooltips btn btn-primary rl_add_file"
						data-original-title="<?php echo $text_add_file; ?>"><i class="fa fa-file fa-5x"></i></button>
			</div>
			<div class="col-sm-6 col-xs-12 center">
				<button class="tooltips btn btn-primary rl_add_code"
						data-original-title="<?php echo $text_add_code; ?>"><i class="fa fa-file-code-o fa-5x"></i>
				</button>
			</div>
		</div>
		<div class="row">
			<div class="panel-body panel-body-nopadding">
				<?php // resource file form ?>
				<div id="file_subform" class="col-sm-12 col-xs-12 form-horizontal form-bordered">
					<div class="resource_image center">
						<div class="fileupload_drag_area">
							<form action="<?php echo $rl_upload; ?>" method="POST" enctype="multipart/form-data">
								<div class="fileupload-buttonbar">
									<label class="btn btn-primary tooltips fileinput-button ui-button  "
										   role="button"
										   data-original-title="<?php echo $text_upload_files.' '.$text_drag; ?>">
										<span class="ui-button-text"><span><i class="fa fa-upload" style="font-size: 16em;"></i></span></span>
										<input type="file" name="files[]" multiple="">
									</label>
								</div>
							</form>
						</div>
					</div>
				</div>


				<?php echo $form['form_open']; ?>
				<?php // resource_code form ?>
				<div id="code_subform" >
					<div class="col-sm-6 col-xs-12 form-horizontal form-bordered">
						<div class="form-group <?php echo(!empty($error['resource_code']) ? "has-error" : ""); ?>">
							<label class="control-label"
								   for="<?php echo $form['field_resource_code']->element_id; ?>"><?php echo $text_resource_code; ?></label>

							<div class="input-group afield col-sm-12">
								<?php echo $form['field_resource_code']; ?>
							</div>
						</div>
					</div>


					<!-- col-sm-6 -->
					<div class="col-sm-6 col-xs-12">
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
			</form>
		<!-- <div class="tab-content"> -->
		</div>
	</div>

<script type="application/javascript">


/*UPLOAD FUNCTIONS*/
jQuery(function () {
	var sendFileToServer = function (formData, status) {
		var response = {};
		var extraData = {}; //Extra Data.
		var jqXHR = $.ajax({
			xhr: function () {
				var xhrobj = $.ajaxSettings.xhr();
				if (xhrobj.upload) {
					xhrobj.upload.addEventListener('progress', function (event) {
						var percent = 0;
						var position = event.loaded || event.position;
						var total = event.total;
						if (event.lengthComputable) {
							percent = Math.ceil(position / total * 100);
						}
						//Set progress
						status.setProgress(percent);
					}, false);
				}
				return xhrobj;
			},
			url: '<?php echo $rl_upload; ?>&type='+$('#resource_types_tabs li.active').attr('data-type'),
			type: "POST",
			contentType: false,
			processData: false,
			cache: false,
			data: formData,
			datatype: 'json',
			async: false,
			success: function (data) {
				response = data[0];
				status.setProgress(100);
			}
		});

		status.setAbort(jqXHR);
		return response;
	}

	var rowCount = 0;
	var createStatusbar = function (obj){
		 rowCount++;
		 var row="odd";
		 if(rowCount %2 ==0) row ="even";
		 this.statusbar = $("<div class='statusbar row "+row+"'></div>");
	     this.filename = $("<div class='filename col-sm-6'></div>").appendTo(this.statusbar);
	     this.size = $("<div class='filesize col-sm-2'></div>").appendTo(this.statusbar);
	     this.progressBar = $('<div class="progress col-sm-3"><div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div>')
							.appendTo(this.statusbar);
	     this.abort = $('<a class="remove btn btn-xs btn-danger-alt tooltips" data-original-title="Abort" title="Abort"><i class="fa fa-minus-circle"></i></a>')
				 		.appendTo(this.statusbar);
		 this.statusbar.appendTo(obj);

	    this.setFileNameSize = function(name,size){
	    	var sizeStr="";
	    	var sizeKB = size/1024;
	    	if(parseInt(sizeKB) > 1024){
	    		var sizeMB = sizeKB/1024;
	    		sizeStr = sizeMB.toFixed(2)+" MB";
	    	}else{
	    		sizeStr = sizeKB.toFixed(2)+" KB";
	    	}

	    	this.filename.html(name);
	    	this.size.html(sizeStr);
	    }
	    this.setProgress = function(progress){
		 	var progressBarWidth = progress*this.progressBar.width()/ 100;
			this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + "%&nbsp;");
			if(parseInt(progress) >= 100){
				this.abort.hide();
			}
		}
		this.setAbort = function(jqxhr){
			var sb = this.statusbar;
			this.abort.click(function(){
				jqxhr.abort();
				sb.hide();
			});
		}
	}

	var handleFileUpload = function (files, obj) {
		$(obj).find('.fileupload-buttonbar').html('');
		var e = 0;
		for (var i = 0; i < files.length; i++) {
			var fd = new FormData();
			fd.append('files', files[i]);

			var status = new createStatusbar($(obj).find('.fileupload-buttonbar')); //Using this we can set progress.
			status.setFileNameSize(files[i].name, files[i].size);
			var response = sendFileToServer(fd, status);
			if(response.hasOwnProperty('error_text')){
				error_alert('File '+files[i].name +' (' + response.error_text + ')', false, '.modal-content' );
				e++;
			}
		}
		if(e!=files.length){
			if(files.length>1){
				mediaDialog($('#resource_types_tabs li.active').attr('data-type'), 'list_object');
			}else{
				mediaDialog($('#resource_types_tabs li.active').attr('data-type'), 'update', response.resource_id );
			}
		}else{
			mediaDialog($('#resource_types_tabs li.active').attr('data-type'), 'add');
		}
	}

	var obj = $("div.fileupload_drag_area");

	obj.on('dragenter', function (e) {
		e.stopPropagation();
		e.preventDefault();
	});
	obj.on('dragover', function (e) {
		$(this).css('border', '2px dotted #F19013');
		e.stopPropagation();
		e.preventDefault();
	});
	obj.on('drop', function (e) {

		$(this).css('border', '2px dotted #F19013');
		e.preventDefault();
		var files = e.originalEvent.dataTransfer.files;

		//We need to send dropped files to Server
		handleFileUpload(files, obj);
	});
	$(document).on('dragenter', function (e) {
		e.stopPropagation();
		e.preventDefault();
	});
	$(document).on('dragover', function (e) {
		e.stopPropagation();
		e.preventDefault();
		obj.css('border', '2px dotted #F19013');
	});
	$(document).on('drop', function (e) {
		obj.css('border', '1px dashed grey');
		e.stopPropagation();
		e.preventDefault();
	});

	$('input[name="files\[\]"]').on('change', function () {
		obj.css('border', '2px dotted #F19013');
		var files = this.files;
		//We need to send dropped files to Server
		handleFileUpload(files, obj);
	});

});

</script>