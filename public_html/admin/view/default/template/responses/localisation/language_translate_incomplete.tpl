<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php  echo $text_incomplete_tasks; ?></h4>
</div>
<div class="tab-content">
	<div class="panel-body panel-body-nopadding">
		<div class="form-group">
		<?php
		$fields = array('task_id', 'title', 'starter_name', 'date_added', 'last_time_run');
		if ($tasks) {
			foreach ($tasks as $task){
				foreach ($fields as $key){
					if(!in_array($key,$fields)){
						continue;
					}
					echo '<dl class="dl-horizontal"><dt>' . $this->language->get('text_' . $key) . '</dt><dd>' . $task[$key] . '</dd></dl>';
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
				</dd></dl>';
			}
		}
?>
		</div>

	</div>

</div>

