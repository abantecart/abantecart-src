<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php if ( isset($results) ) { ?>

<?php if ( isset($results['sql']) && $results['sql'] ){ ?>
<div class="success alert alert-success">
	<?php echo $text_test_completed . $count_test_sqls; ?>.&nbsp;
	<a id="show_results" href="javascript:void(0);"><b><?php echo $text_show_details; ?></b></a>
</div>
<?php } ?>

<?php if ( !empty($results) ) { ?>
<div class="success alert alert-success"><?php echo $text_loaded . $count_loaded . '. ' . $text_updated . $count_updated . '. ' . $text_created . $count_created . '. ' . $text_errors . $count_errors; ?></div>
<?php } ?>
<?php if ( is_array($results['error']) ){ ?>
<div class="warning alert alert-error alert-danger">
	<?php echo $text_some_errors; ?> <a id="show_errors" href="javascript:void(0);"><b><?php echo $text_show_details; ?></b></a>
</div>
<div id="error_results" style="margin:20px; width: 800px; display: none;">
	<?php foreach ($results['error'] as $val) { ?>
	<div class="warning alert alert-error alert-danger"><?php echo $val; ?></div>
	<?php } ?>
</div>
<?php }
} ?>

<ul class="nav nav-tabs nav-justified nav-profile">
	<?php foreach($tabs as $tab){ ?>
	<li <?php echo ( $active == $tab ? 'class="active"' : '' ) ?>>
	<a href="<?php echo ${'link_'.$tab}; ?>"><span><?php echo ${'tab_'.$tab}; ?></span></a></li>
	<?php } ?>
	<?php echo $this->getHookVar('import_export_tabs'); ?>
</ul>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left"></div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form_open; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">

		<label class="h4 heading"><?php echo $import_wizard_title; ?></label>
<?php if($text_import_notes){ ?>
		<p><br /><?php echo $text_import_notes; ?> <br /><br /></p>
<?php } ?>

		<div class="alert alert-info"><?php echo sprintf($text_records_to_be_loaded, $request_count); ?></div>
		<table class="table table-striped table-bordered import-table">
			<thead>
				<td class="col-md-3"><b><?php echo $text_import_file_col; ?></b></td>
				<td class="col-md-3"><b><?php echo $text_import_file_data; ?></b></td>
			</thead>
	<?php	foreach($cols as $i => $col) {	?>
			<tr id="row_<?php echo $i?>">
				<td class="col-md-3">
					<?php echo $col ?>
					<input type="hidden" name="import_col[<?php echo $i ?>]" value="<?php echo $col ?>">
				</td>
				<td class="col-md-3"><?php echo $data[$i]; ?></td>
			</tr>
	<?php  } ?>
		</table>

	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary task_run"
					data-run-task-url="<?php echo $form['build_task_url'] ?>"
					data-complete-task-url="<?php echo $form['complete_task_url'] ?>"
					data-abort-task-url="<?php echo $form['abort_task_url'] ?>"
					data-task-title="<?php echo $text_import_task_title ?>">
				<i class="fa fa-paper-plane-o fa-fw"></i> <?php echo $text_load; ?>
			</button>
			<button class="btn btn-primary">
				<i class="fa fa-clock-o fa-fw"></i> <?php echo $form['schedule']->text; ?>
			</button>
			<a href="<?php echo $reset_url; ?>" class="btn btn-default">
				<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</a>
		</div>
	</div>

	</form>

</div>
