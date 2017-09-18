<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>

</div>

<script type="text/javascript">

	var grid_ready = function(){
		$('.grid_action_run').each(function(){
			var task_id = $(this).parents('tr').attr('id');
			var URL = '<?php echo $run_task_url?>' + '&task_id=' + task_id;
			$(this).click(function(){
				$.ajax({
					url: URL,
					type:'POST',
					success: function(data){
						if(data.result == true) {
							success_alert(<?php js_echo($text_task_started); ?>, true);
						}
					},
					complete: function(){
						$('#tasks_grid').trigger("reloadGrid");
					}
				});

				return false;
			})
		});

		$('.grid_action_restart, .grid_action_continue').each(function(){
			var task_id = $(this).parents('tr').attr('id');
			var URL = '<?php echo $restart_task_url?>' + '&task_id=' + task_id;
			if($(this).hasClass('grid_action_continue')){
				URL += '&continue=1';
			}

			$(this).click(function(){
				$.ajax({
					url: URL,
					type:'POST'
				});
				success_alert(<?php js_echo($text_task_started); ?>, true);
				$('#tasks_grid').trigger("reloadGrid");
				return false;
			})
		});
	};

</script>