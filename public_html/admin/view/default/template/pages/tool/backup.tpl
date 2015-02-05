<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>
	
	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading"><?php echo $tab_backup; ?></label>
		<?php foreach ($form['fields'] as $name => $field) { ?>
			<div class="form-group <?php if (!empty($error[$name])) {
				echo "has-error";
			} ?>">
				<label class="control-label col-sm-3 col-xs-12"
					   for="<?php echo $field->element_id; ?>">
					<?php
					echo ${'entry_' . $name};
					if(${'entry_' . $name.'_size'}){
						echo '&nbsp;('.${'entry_' . $name.'_size'}.')';
					}
					?>
				</label>

				<div class="input-group">
					<?php echo $field; ?>
				</div>
				<?php if (!empty($error[$name])) { ?>
					<span class="help-block field_err"><?php echo $error[$name]; ?></span>
				<?php } ?>
			</div>
			<?php if ($name == 'tables') { ?>
			<div class="form-group">
				<div class="input-group col-sm-offset-3">
						<a class="btn btn-info btn-xs" onclick="selectAll();">
							<i class="fa fa-check-square-o fa-fw"></i>	<?php echo $text_select_all; ?>
						</a>
						<a class="btn btn-default btn-xs" onclick="unselectAll();">
							<i class="fa fa-square-o fa-fw"></i> <?php echo $text_unselect_all; ?>
						</a>
				</div>
			</div>
			<?php } ?>
			
		<?php } ?>

	</div>
	<div class="panel-footer col-xs-12">
		<div class="text-center">
				<button class="btn btn-primary task_run" data-run-task-url="<?php echo $form['build_task_url']?>"
						data-complete-task-url="<?php echo $form['complete_task_url']?>">
					<i class="fa fa-database"></i> <?php echo $form['backup_now']->text; ?>
				</button>
				<button class="btn btn-primary lock-on-click task_schedule" >
					<i class="fa fa-clock-o fa-fw"></i> <?php echo $form['backup_schedule']->text; ?>
				</button>
		</div>
	</div>
	</form>


	<?php echo $restoreform['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading"><?php echo $tab_restore; ?></label>

		<div class="form-group <?php if (!empty($error['file'])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-4 col-xs-12"
				   for="<?php echo $restoreform['file']->element_id; ?>"><?php echo $entry_restore; ?></label>

			<div class="afield col-sm-5">
				<?php echo $restoreform['file']; ?>
			</div>
			<?php if (!empty($error['file'])) { ?>
				<span class="help-block field_err"><?php echo $error['file']; ?></span>
			<?php } ?>
		</div>
	</div>
	<div class="panel-footer col-xs-12">
		<div class="text-center">
				<button class="btn btn-primary lock-on-click">
					<i class="fa fa-undo fa-fw"></i> <?php echo $restoreform['submit']->text; ?>
				</button>
		</div>
	</div>
	</form>

	<?php echo $xmlform['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading"><?php echo $tab_loadxml; ?></label>

		<div class="form-group <?php if (!empty($error['file'])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-4 col-xs-12"
				   for="<?php echo $xmlform['file']->element_id; ?>"><?php echo $entry_loadxml; ?></label>

			<div class="afield col-sm-5">
				<?php echo $xmlform['file']; ?>
			</div>
			<?php if (!empty($error['file'])) { ?>
				<span class="help-block field_err"><?php echo $error['file']; ?></span>
			<?php } ?>
		</div>
	</div>
	<div class="panel-footer col-xs-12">
		<div class="text-center">
				<button class="btn btn-primary lock-on-click">
					<i class="fa fa-upload fa-fw"></i> <?php echo $xmlform['submit']->text; ?>
				</button>
		</div>
	</div>
	</form>
</div>


<script type="text/javascript">
	$(document).ready(function(){
		task_fail_text = '<div class="alert alert-warning" role="alert"><?php echo $text_fail_note; ?></div>';
	});

	function selectAll() {
		$('input[name*=\'table_list\[\]\']').attr('checked', 'checked');
		$('#tables').find('.afield').addClass('checked');
	}
	function unselectAll() {
		$('input[name*=\'table_list\[\]\']').removeAttr('checked');
		$('#tables').find('.afield').removeClass('checked');
	}

</script>