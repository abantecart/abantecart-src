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
		<div class="primary_content_actions pull-left">
			<a class="btn btn-default lock-on-click tooltips" href="<?php echo $reset_url; ?>" title="<?php echo $button_reset; ?>">
				<i class="fa fa-refresh fa-fw"></i>
			</a>

			<a href="#" class="btn btn-default export_map tooltips" data-toggle="modal" data-target="#load_map_modal" title="<?php echo $text_load_map; ?>">
				<i class="fa fa-code fa-fw"></i>
			</a>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form_open; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">

		<label class="h4 heading"><?php echo $import_wizard_title; ?></label>

		<p><br /><?php echo $text_import_wizard_notes; ?> <br /><br /></p>

		<?php if ( $import_ready ) { ?>
		<div class="alert alert-info"><?php echo sprintf($text_records_to_be_loaded, $request_count); ?></div>
		<?php } ?>

		<table class="table table-striped table-bordered import-table">
			<thead>
				<td class="col-md-3"><b><?php echo $text_import_file_col; ?></b></td>
				<td class="col-md-3"><b><?php echo $text_import_file_data; ?></b></td>
				<td class="col-md-4">
					<div class="form-group">
						<div class="input-group">
							<select class="form-control" name="table" <?php if($import_ready) echo " disabled "; ?>>
								<option value=""><?php echo $text_destination_col; ?></option>
								<?php
								if(is_array($tables)) {
								foreach($tables as $tname => $tcols) {
								?>
								<option value="<?php echo $tname ?>" <?php if($tname == $map['table']) echo " selected "; ?>>
								&nbsp;&nbsp;<?php echo $tname ?>
								</option>
								<?php
									}
								}
								?>
							</select>
						</div>
					</div>
				</td>
				<td class="col-md-2"><b><?php echo $text_import_update_on; ?></b></td>
			</thead>
	<?php	foreach($cols as $i => $col) { ?>
			<tr>
				<td class="col-md-3">
					<?php echo $col ?>
					<input type="hidden" name="import_col[<?php echo $i ?>]" value="<?php echo $col ?>">
				</td>
				<td class="col-md-3" style="word-break: break-all;"><?php echo $data[$i]; ?></td>
				<td class="col-md-4 table-field">
					<?php foreach ($tables as $table_name => $tbl_data) { ?>
					<div class="field_selector <?php echo $table_name; ?>_field hidden">
						<div class="form-group">
							<div class="input-group">
								<select class="form-control" name="<?php echo $table_name; ?>_fields[<?php echo $i ?>]"  disabled="disabled">
									<option value=""> - - </option>
									<?php
									foreach($tbl_data["columns"] as $cname => $det) {
										$selected = '';
										if($cname == $map[$table_name."_fields"][$i]) {
											$selected = 'selected';
										}
										//see if we can match colums based on the name
										$col_name = trim(preg_replace('/[0-9]+/', '', $col));
										if(	strtolower($col_name) == $cname
											|| strtolower(preg_replace('/\s+/', '.', $col_name)) == $cname
											|| strtolower($col_name) == $det['alias']
										) {
											$selected = 'selected';
										}
										$sel_title = $det["title"];
										if($det["required"]) {
											$sel_title = "{$sel_title} *required";
										}
										echo "<option value=\"$cname\" data-mvalue=\"{$det["multivalue"]}\" data-split=\"{$det["split"]}\" data-update=\"{$det["update"]}\" $selected>{$sel_title}</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group field_splitter hidden">
							<div class="input-group">
								<label class="control-label"><?php echo $text_import_split; ?></label>
								<input type="text" size="5" name="split_col[<?php echo $i ?>]" value="<?php echo $map['split_col'][$i]; ?>"  disabled="disabled">
							</div>
						</div>
					</div>
					<?php } ?>
				</td>
				<td class="col-md-2 update-field">
					<div class="form-group field_updater hidden">
						<div class="input-group">
							<input type="checkbox" name="update_col[<?php echo $i ?>]" <?php if($map['update_col'][$i]) { echo 'checked="checked"'; } ?>>
						</div>
					</div>
				</td>
			</tr>
		<?php
			}
		?>
		</table>

	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<?php if($import_ready) { ?>
			<button class="btn btn-primary task_run"
					data-run-task-url="<?php echo $form['build_task_url'] ?>"
					data-complete-task-url="<?php echo $form['complete_task_url'] ?>"
					data-abort-task-url="<?php echo $form['abort_task_url'] ?>"
                    data-task-title="<?php echo $text_import_task_title ?>">
				<i class="fa fa-paper-plane-o fa-fw"></i> <?php echo $text_load; ?>
			</button>
			<a href="<?php echo $form['schedule_url']; ?>" class="btn btn-primary">
				<i class="fa fa-clock-o fa-fw"></i> <?php echo $button_schedule_import; ?>
			</a>
			<a href="#" class="btn btn-default export_map" data-toggle="modal" data-target="#export_map_modal">
				<i class="fa fa-code fa-fw"></i> <?php echo $text_export_map; ?>
			</a>
			<a href="<?php echo $back_url; ?>" class="btn btn-default" title="<?php echo $button_back; ?>">
				<i class="fa fa-arrow-left fa-fw"></i>
				<?php echo $button_back ?>
			</a>
			<?php } else { ?>
			<button class="btn btn-primary lock-on-click"
				<i class="fa fa-paper-plane-o fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<a href="#" class="btn btn-default export_map" data-toggle="modal" data-target="#load_map_modal">
				<i class="fa fa-code fa-fw"></i> <?php echo $text_load_map; ?>
			</a>
			<a href="<?php echo $reset_url; ?>" class="btn btn-default">
				<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</a>
			<?php } ?>
		</div>
	</div>

	<?php
	if ($import_ready) {
		echo $this->html->buildElement(
		array(
		'type' => 'modal',
		'id' => 'export_map_modal',
		'modal_type' => 'lg',
		'title' => $title_export_map,
		'content' => $serialized_map,
		));
	} else {
		echo $this->html->buildElement(
		array(
		'type' => 'modal',
		'id' => 'load_map_modal',
		'modal_type' => 'lg',
		'title' => $title_load_map,
		'content' => $form['serialized_map'] . '
		<br /><center>
			<button class="btn btn-primary lock-on-click"
			<i class="fa fa-paper-plane-o fa-fw"></i>'. $form['submit']->text . '
			</button>
		</center>
		',
		));
	}
	?>

	</form>

</div>


<script type="text/javascript">

	$(document).ready(function () {
		$('.aform').show();

		load_table_fields( $('select[name^=\'table\']').val() );
	});

	//process selected table
	$('select[name^=\'table\']').change(function () {
		var table_name = $(this).val();
		load_table_fields(table_name);
	});

	//check update
	<?php foreach ($tables as $table_name => $tbl_data) { ?>
	$('select[name^=\'<?php echo $table_name; ?>_fields\']').change(function () {
		var $elm = $(this);
		updateFields($elm, '<?php echo $table_name; ?>');
	});
	<?php } ?>

    //allow only one update by field
    $(".field_updater input:checkbox").on('click', function() {
        $(".field_updater input:checkbox").not(this).prop('checked', false);
    });

	$('#show_results').click(function () {
		$('#test_results').slideToggle();
	});

	$('#show_errors').click(function () {
		$('#error_results').slideToggle();
	});

	var load_table_fields = function (table_name) {
		if(!table_name) {
			return;
		}
		//first remove all others.
		$('.table-field .field_selector').addClass('hidden');
		$('.update-field .field_updater').addClass('hidden');
		$('.table-field input').attr('disabled','disabled');
		//now show selected table
		$('.table-field .' + table_name + '_field').removeClass('hidden');
		//check update
		$('.table-field .' + table_name + '_field select').each( function () {
			$elm = $(this);
			<?php if(!$import_ready) { ?>
			$elm.removeAttr('disabled');
			<?php } ?>
			updateFields($elm, table_name);
		});
	};

	var updateFields = function ($elm, table_name) {
		$selected = $elm.find("option:selected");
		var $update = $elm.closest('tr').find('.update-field');
		if($selected.data('update')){
			$update.find('.field_updater').removeClass('hidden');
			<?php if($import_ready) { ?>
				$update.find('.field_updater input').attr("disabled", true);
			<?php } ?>

		} else {
			$update.find('.field_updater').addClass('hidden');
			$update.find('.field_updater input').removeAttr('checked');
		}
		var $split = $elm.parents('.field_selector').find('.field_splitter');
		if ($selected.data('split') == '1') {
			<?php if(!$import_ready) { ?>
				$split.find('input').removeAttr('disabled');
			<?php } ?>
			$split.removeClass('hidden');
		} else {
			<?php if(!$import_ready) { ?>
				$split.find('input').attr("disabled", true);
			<?php } ?>
			$split.addClass('hidden');
		}
		//hide from other select for single value fields
		if ($selected.data('multivalue') != '1') {
			checkSelected(table_name, $elm);
		}

	};

	var checkSelected = function (table_name, $elm) {
		$orig_sel = $elm.find("option:selected");
		$elm.closest('.form-group').removeClass("has-error");
		$('.table-field .' + table_name + '_field select option:selected').each(function() {
			$selected = $(this);
			if($elm.is($selected.closest('select'))){
				//skip same element
				return;
			}
			if ($selected.val() && $selected.data('mvalue') != '1') {
				if($selected.val() == $orig_sel.val()) {
					$elm.closest('.form-group').addClass("has-error");
				}
			}
		});
	};

</script>
