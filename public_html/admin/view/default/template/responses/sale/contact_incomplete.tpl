<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php  echo $text_incomplete_tasks; ?></h4>
</div>
<div class="tab-content">
	<div class="panel-body panel-body-nopadding">
		<div class="form-group">
		<?php
		$fields = array('task_id', 'starter_name', 'date_added', 'last_time_run', 'subject', 'message', 'sent' );
		if ($tasks) {
			foreach ($tasks as $task){
				foreach ($task as $key => $item){
					if(!in_array($key,$fields)){
						continue;
					}
					$text = $this->language->get('text_' . $key);
					if($key=='sent'){
						$text = '';
					}
					echo '<dl class="dl-horizontal"><dt>' . $text . '</dt><dd>' . $item . '</dd></dl>';

				}
				echo '<dl class="dl-horizontal"><dt></dt><dd>
					<a class="restart_task btn btn-primary task_run"
						data-run-task-url="'. $restart_task_url.'&task_id='.$task['task_id'].'"
						data-complete-task-url="'. $complete_task_url.'"
						data-abort-task-url="'. $abort_task_url.'">
						<i class="fa fa-play-circle fa-lg"></i> '.$text_restart.'</a>
					<a class="remove_task btn btn-danger"
						data-task_id="'.$task['task_id'].'"
						onclick="removeTask(this);"
						data-confirmation="delete">
						<i class="fa fa-minus-circle fa-lg"></i> '.$text_remove.'</a>
				</dd></dl><br>';
			}
		}
?>
		</div>

	</div>

</div>

