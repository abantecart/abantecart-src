<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php if ( isset($results) ) { ?>

<?php if ( isset($results['sql']) ): ?>
<div class="success alert alert-success">
	<?php echo $text_test_completed . $count_test_sqls; ?>.&nbsp;
	<a id="show_results" href="javascript:void(0);"><b><?php echo $text_show_details; ?></b></a>
</div>
<div id="test_results" style="margin:20px; width: 800px; display: none;">
	<?php foreach($results['sql'] as $msg): ?>
	<p><?php echo $msg; ?></p>
	<?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ( !empty($results) ) { ?>
<div class="success alert alert-success"><?php echo $text_loaded . $count_loaded . '. ' . $text_updated . $count_updated . '. ' . $text_created . $count_created . '. ' . $text_errors . $count_errors; ?></div>
<?php } ?>
<?php if ( is_array($results['error']) ): ?>
<div class="warning alert alert-error alert-danger">
	<?php echo $text_some_errors; ?> <a id="show_errors" href="javascript:void(0);"><b><?php echo $text_show_details; ?></b></a>
</div>
<div id="error_results" style="margin:20px; width: 800px; display: none;">
	<?php foreach ($results['error'] as $val) { ?>
	<div class="warning alert alert-error alert-danger"><?php echo $val; ?></div>
	<?php } ?>
</div>
<?php endif; ?>
<?php } ?>

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
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form_open; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">

		<label class="h4 heading"><?php echo $import_wizard_title; ?></label>

		<p><br /><?php echo $text_import_wizard_notes; ?> <br /><br /></p>

		<table class="table table-striped table-bordered import-table">
			<thead>
				<td class="col-md-3"><b><?php echo $text_import_file_col; ?></b></td>
				<td class="col-md-3"><b><?php echo $text_import_file_data; ?></b></td>
				<td class="col-md-4">
					<div class="form-group">
						<div class="input-group">
							<select class="form-control" name="table">
								<option name=""><?php echo $text_destination_col; ?></option>
								<?php
								foreach($tables as $tname => $tcols) {
								?>
								<option value="<?php echo $tname ?>" <?php if($tname == $post['table']) echo "selected"; ?>>
								&nbsp;&nbsp;<?php echo $tname ?>
								</option>
								<?php
							}
						?>
							</select>
						</div>
					</div>
				</td>
				<td class="col-md-2"><b><?php echo $text_import_update_on; ?></b></td>
			</thead>
		<?php
			foreach($cols as $i => $col) {
		?>
			<tr>
				<td class="col-md-3">
					<?php echo $col ?>
					<input type="hidden" name="import_col[<?php echo $i ?>]" value="<?php echo $col ?>">
				</td>
				<td class="col-md-3"><?php echo $data[$i]; ?></td>
				<td class="col-md-4 table-field">
					<?php foreach ($tables as $table_name => $tbl_data) { ?>
					<div class="field_selector <?php echo $table_name; ?>_field hidden">
						<div class="form-group">
							<div class="input-group">
								<select class="form-control" name="<?php echo $table_name; ?>_fields[<?php echo $i ?>]"  disabled="disabled">
									<option name=""> - - </option>
									<?php
									foreach($tbl_data["columns"] as $cname => $det) {
										$selected = '';
										if($cname == $post[$table_name."_fields"][$i]) {
											$selected = 'selected';
										}
										echo "<option value=\"$cname\" data-update=\"{$det["update"]}\" $selected>{$det["title"]}</option>";
									}
									?>
								</select>
							</div>
						</div>
					</div>
					<?php } ?>
				</td>
				<td class="col-md-2 update-field">
					<div class="form-group field_updater hidden">
						<div class="input-group">
							<input type="checkbox" name="update_col[<?php echo $i ?>]" <?php if($post['update_col'][$i]) { echo 'checked="checked"'; } ?>>
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
			<button class="btn btn-primary lock-on-click">
				<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<button class="btn btn-default" type="reset">
				<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</button>
		</div>
	</div>

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
		updateCheckbox($elm);
	});
	<?php } ?>

	$('#show_results').click(function () {
		$('#test_results').slideToggle();
	})

	$('#show_errors').click(function () {
		$('#error_results').slideToggle();
	});

	var load_table_fields = function (table_name) {
		if(!table_name) {
			return;
		}
		//first remove all others.
		$('.table-field .field_selector').addClass('hidden');
		$('.table-field .field_selector').attr('disabled','disabled');
		$('.update-field .field_updater').addClass('hidden');
		//now show selected table
		$('.table-field .' + table_name + '_field').removeClass('hidden');
		//check update
		$('.table-field .' + table_name + '_field select').each( function () {
			$elm = $(this);
			$elm.removeAttr('disabled');
			updateCheckbox($elm);
		});
	};

	var updateCheckbox = function ($elm) {
		$selected = $elm.find("option:selected");
		if($selected.data('update')){
			var $update = $elm.closest('tr').find('.update-field');
			$update.find('.field_updater').removeClass('hidden');
		}
	};

</script>