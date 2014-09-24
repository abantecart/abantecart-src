<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
				class="sr-only">Close</span></button>
	<h4 class="modal-title"><?php echo $form_title; ?></h4>
</div>
<div class="modal-body">
<?php echo $resources_scripts;?>
<?php if (!$download_id) { ?>
		<div class="panel-group" id="accordion">
		<?php if($form0){ ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="h4" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
							<?php echo $text_select_shared_downloads; ?>
						</a>
					</h4>
				</div>
				<div id="collapseOne" class="panel-collapse collapse in">
			<?php echo $form0['form_open']; ?>
				<div class="panel-body panel-body-nopadding">
						<?php
							//Logic to cululate fileds width
							$widthcasses = "col-sm-7";
							if ( is_int(stripos($field->style, 'large-field')) ) {
								$widthcasses = "col-sm-7";
							} else if ( is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date')) ) {
								$widthcasses = "col-sm-5";
							} else if ( is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch')) ) {
								$widthcasses = "col-sm-3";
							} else if ( is_int(stripos($field->style, 'tiny-field')) ) {
								$widthcasses = "col-sm-2";
							}
							$widthcasses .= " col-xs-12";
						$name = 'shared';
						?>
					<div class="form-group <? if (!empty($error[$name])) { echo "has-error"; } ?>">
						<label class="control-label col-sm-3 col-xs-12" for="<?php echo $form0['shared']->element_id; ?>"><?php echo $text_select_shared_downloads; ?></label>
						<div class="input-group afield <?php echo $widthcasses; ?> ">
							<?php echo $form0['shared']; ?>
						</div>
						<?php if (!empty($error[$name])) { ?>
						<span class="help-block field_err"><?php echo $error[$name]; ?></span>
						<?php } ?>
					</div>
				</div>
				<div class="panel-footer">
					<div class="row pull-right">
						<div class="col-sm-6 col-sm-offset-0">
							<a href="<?php echo $form0['submit']->href; ?>">
								<button class="btn btn-primary">
									<i class="fa fa-plus"></i> <?php echo $form0['submit']->text; ?>
								</button>
							</a>
						</div>
					</div>
				</div>
			</form>
				</div>
			</div>
		<?php } ?>
			<?php // insert collapses when create new product file to split form to 2 part - create from shared and create new ?>
			<div class="panel panel-default">
				<?php if($form0){?>
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="h4" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"><?php echo $text_new_file;?></a>
					</h4>
				</div>
				<div id="collapseTwo" class="panel-collapse collapse <?php echo !$form0 ? 'in' : ''; ?>">
				<?php }?>
<?php } ?>
				<?php echo $form['form_open']; ?>
					<div class="panel-body panel-body-nopadding">
						<?php foreach ($form['fields'] as $section => $fields) { ?>
						<label class="h4 heading"><?php echo ${'tab_' . $section}; ?></label>
							<?php foreach ($fields as $name => $field) {
								if( $field->type=='hidden' ){ echo $field; continue;	}
								//Logic to cululate fileds width
								$widthcasses = "col-sm-7";
								if ( is_int(stripos($field->style, 'large-field')) ) {
									$widthcasses = "col-sm-7";
								} else if ( is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date')) ) {
									$widthcasses = "col-sm-5";
								} else if ( is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch')) ) {
									$widthcasses = "col-sm-3";
								} else if ( is_int(stripos($field->style, 'tiny-field')) ) {
									$widthcasses = "col-sm-2";
								}
								$widthcasses .= " col-xs-12";
							?>
						<div class="form-group <? if (!empty($error[$name])) { echo "has-error"; } ?>">
							<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
							<div class="input-group afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
								<?php echo $field;
								 if($name=='shared' && $map_list){ ?>
									<div class="list-group">
										<span class="list-group-item disabled"><?php echo $text_shared_with?></span>
									<?php foreach($map_list as $i){?>
										<a href="<?php echo $i['href'];?>" target="_blank" class="list-group-item"><?php echo $i['text']?></a>
									<?php } ?>
									</div>
								<?php } ?>
							</div>
							<?php if (!empty($error[$name])) { ?>
							<span class="help-block field_err"><?php echo $error[$name]; ?></span>
							<?php }
						if($name=='resource' && $preview['path']){ ?>
								<label class="control-label col-sm-3 col-xs-12" for=""></label>
								<dl class="col-sm-12 dl-horizontal clearfix">
									<dt class="date_added"><?php echo $entry_date_added; ?></dt>
									<dd class="date_added"><?php echo $date_added; ?></dd>
									<dt class="date_modified"><?php echo $entry_date_modified; ?></dt>
									<dd class="date_modified"><?php echo $date_modified; ?></dd>
									<dt><?php echo $text_path; ?></dt>
									<dd><a href="<?php echo $preview['href']?>"><i class="fa fa-download"></i> <?php echo $preview['path']; ?></a></dd>
								</dl>
						<?php } ?>
						</div>
						<?php }  ?><!-- <div class="fieldset"> -->
						<?php }  ?>
					</div>
					<div class="panel-footer">
						<div class="center">
							<div class="col-sm-12">
								<button class="btn btn-primary">
									<i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
								</button>
								&nbsp;
								<a class="btn btn-default" data-dismiss="modal" href="<?php echo $cancel; ?>">
									<i class="fa fa-refresh"></i> <?php echo $form['cancel']->text; ?>
								</a>
							</div>
						</div>
					</div>
				</form>
		<?php
		// close parent div for collapses when create
		if(!$download_id){ ?>
				<?php if($form0){?>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php }?>
</div>

<script type="application/javascript">
	$('#downloadFrm_activate').on('change', function () {
		if ($(this).val() != 'order_status') {
			$('#downloadFrm_activate_order_status_id').fadeOut().next('.input-group-addon').fadeOut();

			if($(this).val() == 'before_order'){
				$('#downloadFrm_max_downloads, #downloadFrm_expire_days').parents('.form-group').fadeOut();
			}else{
				$('#downloadFrm_max_downloads, #downloadFrm_expire_days').parents('.form-group').fadeIn();
			}

		} else {
			$('#downloadFrm_activate_order_status_id').fadeIn().next('.input-group-addon').fadeIn();
			$('#downloadFrm_max_downloads, #downloadFrm_expire_days').parents('.form-group').fadeIn();
		}
	});



	$('#downloadFrm').submit(function () {
		$.ajax(
				{   url: '<?php echo $form['form_open']->action; ?>',
					type: 'POST',
					data: $('#downloadFrm').serializeArray(),
					dataType: 'json',
					success: function (data) {
						if (data.result_text != '') {
						<?php
							if(!$download_id){?>
								goTo('<?php echo $file_list_url; ?>');
					<?php   }else{ ?>
							$('#file_modal').scrollTop(0);
							success_alert(data.result_text, true, "#downloadFrm");
							<?php } ?>
						}
					},
					error: function(jqXHR){
						try{
							var err = $.parseJSON(jqXHR.responseText);
						}catch(e){}
						if(err){
							error_alert(err.error_title, false, "#downloadFrm");
						}
					}
				});
		return false;
	});

</script>