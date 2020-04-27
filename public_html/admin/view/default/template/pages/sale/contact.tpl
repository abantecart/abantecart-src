<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
				<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
					<?php
					if ($active == 'email'){
						$icon = 'fa fa-envelope-o';
					} elseif ($active == 'sms'){
						$icon = 'fa fa-mobile';
					} elseif ($protocols[$active]['icon']){
						$icon = $protocols[$active]['icon'];
					} else{
						$icon = '';
					}
					?>
					<i class="<?php echo $icon; ?>"></i>
					<?php echo $protocols[$active]['title']; ?> <span class="caret"></span>
				</button>
				<ul class="choose-protocol dropdown-menu">
					<?php foreach ($protocols as $id => $protocol){
						if ($id == 'email'){
							$icon = 'fa fa-envelope-o';
						} elseif ($id == 'sms'){
							$icon = 'fa fa-mobile';
						} elseif ($protocol['icon']){
							$icon = $protocol['icon'];
						} else{
							$icon = '';
						}
						?>
						<li>
							<a title="<?php echo $protocol['title'] ?>" href="<?php echo $protocol['href'] ?>">
								<i class="<?php echo $icon; ?>"></i> <?php echo $protocol['title'] ?></a>
						</li>
					<?php } ?>
				</ul>
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

		<label class="h4 heading"><?php echo $form_title; ?></label>
		<?php foreach ($form['fields'] as $name => $field) {
			if($field->type=='hidden'){
				echo $field;
				continue;
			}
		//Logic to calculate fields width
		$widthcasses = "col-sm-7";
		if (is_int(stripos($field->style, 'large-field'))){
			$widthcasses = "col-sm-7";
		} else if (is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date'))){
			$widthcasses = "col-sm-5";
		} else if (is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch'))){
			$widthcasses = "col-sm-3";
		} else if (is_int(stripos($field->style, 'tiny-field'))){
			$widthcasses = "col-sm-2";
		}
		$widthcasses .= " col-xs-12";
		?>
		<div class="form-group <?php if (!empty($error[$name])){
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-3 col-xs-12"
			       for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>

			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '') ?>">
				<?php echo $field; ?>
			</div>
			<?php if (!empty($error[$name])){ ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
		<?php } ?><!-- <div class="fieldset"> -->

	</div>

	<div class="panel-footer col-xs-12">
		<div id="presend_attention" class="text-center">

		</div>
		<div class="text-center">
			<button class="btn btn-primary task_run" data-run-task-url="<?php echo $form['build_task_url'] ?>"
			        data-complete-task-url="<?php echo $form['complete_task_url'] ?>"
			        data-abort-task-url="<?php echo $form['abort_task_url'] ?>">
				<i class="fa fa-envelope"></i> <?php echo $form['submit']->text; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $cancel; ?>">
				<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</a>
		</div>
	</div>
	</form>
</div><!-- <div class="tab-content"> -->
<?php echo $resources_scripts; ?>
<script type="text/javascript">
	$(document).ready(function () {
		$('#sendFrm_recipient').change(function () {
			var recipient = $(this).val();
			if (recipient == '' || recipient == 'FALSE') {
				$('#sendFrm_to').removeAttr('disabled').parents('div.form-group').fadeIn(500);
				$('#sendFrm_products').attr('disabled', 'disabled').parents('div.form-group').fadeOut(500);
			} else if (recipient == 'ordered') {
				$('#sendFrm_to').attr('disabled', 'disabled').parents('div.form-group').fadeOut(500);
				$('#sendFrm_products').removeAttr('disabled').parents('div.form-group').fadeIn(500);
			} else {
				$('#sendFrm_to').attr('disabled', 'disabled').parents('div.form-group').fadeOut(500);
				$('#sendFrm_products').attr('disabled', 'disabled').parents('div.form-group').fadeOut(500);
			}

			var senddata = {
				recipient: recipient,
				protocol: $('#sendFrm_protocol').val()
			};
			if(recipient == 'ordered'){
				senddata['products'] = $('#sendFrm_products').chosen().val();
			}

			//not manualy selected
			if (recipient != '' && recipient != 'FALSE') {
				$.ajax({
					type: "POST",
					url: '<?php echo $recipients_count_url;?>',
					data: senddata,
					datatype: 'json',
					beforeSend: function(){
						$('#presend_attention').html('');
					},
					success: function(response){
						if(!response.hasOwnProperty('text')){
							return false;
						}
						if(response.count > 0){
							$('#presend_attention').html('<div class="info alert alert-warning"><i class="fa fa fa-exclamation-triangle fa-fw"></i> ' + response.text +'</div>');
						}else{
							$('#presend_attention').html('<div class="warning alert alert-error alert-danger"><i class="fa fa-thumbs-down fa-fw"></i> ' + response.text +'</div>');
						}
					}
				});
			} else {
				$('#presend_attention').html('');			
			}
		});
		$('#sendFrm_recipient').change();
		$('#sendFrm_products').chosen().change(function(){
			$('#sendFrm_recipient').change();
		});

		$('.choose-protocol li>a').on('click', function () {
			$.ajax({
				type: "POST",
				url: '<?php echo $presave_url;?>',
				data: $('#sendFrm').serializeArray(),
				datatype: 'json'
			});
		});
	});


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

	$(document).on('click', 'a.restart_task', function(){
		$('#incomplete_tasks_modal').modal('hide');
	});

</script>