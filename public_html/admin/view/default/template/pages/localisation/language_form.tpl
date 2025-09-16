<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
            <div class="btn-group">
                <a class="btn btn-white tooltips back-to-grid tooltips" data-table-id="language_grid" href="<?php echo $list_url; ?>" data-toggle="tooltip"
                   data-original-title="<?php echo_html2view($text_back_to_list); ?>">
                    <i class="fa fa-arrow-left fa-lg"></i>
                </a>
            </div>
		</div>

		<?php
		if($incomplete_tasks_url){
			$common_content_buttons[] = '<a class="btn btn-danger"
											href="'.$incomplete_tasks_url.'"
											data-toggle="modal"
											data-target="#incomplete_tasks_modal"
											title="'.$text_incomplete_tasks.'">
											<i class="fa fa-exclamation-triangle fa-lg"></i> '.$text_incomplete_tasks.'</a>';
			echo $this->html->buildElement(
					array('type' => 'modal',
							'id' => 'incomplete_tasks_modal',
							'title' => $text_incomplete_tasks,
							'data_source' => 'ajax'));
		}
		include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading"><?php echo $language_edit_title; ?></label>
			<?php foreach ($form['fields'] as $name => $field) {

				//Logic to calculate fields width

				$widthCssClasses = "col-sm-7";
				if ( str_contains($field->style, 'medium-field') || str_contains($field->style, 'date') ) {
					$widthCssClasses = "col-sm-5";
				} else if ( str_contains($field->style, 'small-field') || str_contains($field->style, 'btn_switch') ) {
					$widthCssClasses = "col-sm-3";
				} else if ( str_contains($field->style, 'tiny-field') ) {
					$widthCssClasses = "col-sm-2";
				}
				$widthCssClasses .= " col-xs-12";
			?>
		<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield <?php echo $widthCssClasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
				<?php echo $field; ?>
			</div>
		    <?php if (!empty($error[$name])) { ?>
		    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
		    <?php } ?>
		</div>
			<?php }  ?><!-- <div class="fieldset"> -->
	</div>
	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $cancel; ?>">
			<i class="fa fa-arrow-left fa-fw"></i> <?php echo $form['cancel']->text; ?>
			</a>
		</div>
	</div>
	</form>

<?php if($form2){ ?>
	<?php echo $form2['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading"><?php echo $load_language_title; ?></label>
			<?php foreach ($form2['fields'] as $name => $field) {
				if($field->type == 'hidden'){
					echo  $field;
					continue;
				}
				//Logic to calculate fields width
				$widthCssClasses = "col-sm-7";
                if ( str_contains($field->style, 'medium-field') || str_contains($field->style, 'date') ) {
					$widthCssClasses = "col-sm-5";
				} else if ( str_contains($field->style, 'small-field') || str_contains($field->style, 'btn_switch') ) {
					$widthCssClasses = "col-sm-3";
				} else if ( str_contains($field->style, 'tiny-field') ) {
					$widthCssClasses = "col-sm-2";
				}
				$widthCssClasses .= " col-xs-12";
			?>
		<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield <?php echo $widthCssClasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
				<?php echo $field; ?>
			</div>
		    <?php if (!empty($error[$name])) { ?>
		    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
		    <?php } ?>
		</div>
			<?php }  ?><!-- <div class="fieldset"> -->
		<div role="alert" class="alert alert-warning fade in">
			<i class="fa fa fa-exclamation-triangle fa-fw"></i> <strong><?php echo $load_language_note; ?></strong>
		</div>
		<?php if($override_text_note){ ?>
			<div class="info alert alert-warning"><i class="fa fa fa-exclamation-triangle fa-fw"></i> <?php echo $override_text_note; ?></div>
		<?php } ?>
	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
		  <button class="btn btn-primary task_run"
		        data-run-task-url="<?php echo $form2['build_task_url']?>"
		  		data-complete-task-url="<?php echo $form2['complete_task_url']?>">
		  <i class="fa fa-save"></i> <?php echo $form2['load_data']->text; ?>
		  </button>
		</div>
	</div>
	</form>
<?php } ?>

</div>

<script language="JavaScript" type="application/javascript">
	function removeTask(elm){
		$.ajax({
			type: "POST",
			url: '<?php echo $form['abort_task_url'];?>',
			data: {task_id: $(elm).attr('data-task_id')},
			datatype: 'json',
			complete: function(){
				location.reload();
			}
		});
	}

	//Language locale
	$('#languageFrm_locale').on('focus', function () {
		if ($(this).val().length > 0) {
			return null;
		}
		var code = $('#languageFrm_code').val().toLowerCase();
		var upper_code = code.toUpperCase();
		if (code.length == 0) {
			return null;
		}
		var locale = code+'_'+upper_code+'.UTF-8,'+code+'_'+upper_code+','+code+'-'+code+','+$('#languageFrm_name').val().toLowerCase().replace('default_', '');
		$(this).val(locale);
	});

	$(document).on('click', 'a.restart_task', function(){
		$('#incomplete_tasks_modal').modal('hide');
	});
</script>