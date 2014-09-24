<?php if (!empty($error['warning'])) { ?>
	<div class="warning alert alert-error alert-danger"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<?php echo $summary_form; ?>
<?php echo $product_tabs ?>

<div class="tab-content">
	<div class="panel-heading">
		<div class="pull-right">
			<div class="btn-group mr10 toolbar">
				<?php if (!empty ($help_url)) { ?>
					<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip"
					   title="" data-original-title="Help">
						<i class="fa fa-question-circle"></i>
					</a>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<a class="minimize" href="">−</a>
			</div>
			<h4 class="panel-title"><?php echo $tab_files; ?></h4>
		</div>
		<?php echo $form['form_open']; ?>
		<div class="panel-body panel-body-nopadding">
			<table class="table table-striped">
				<thead>
					<tr>
						<td class="center"><?php echo $entry_file_icon; ?></td>
						<td class="center"><?php echo $entry_name; ?></td>
						<td class="center"><?php echo $entry_file_max_downloads; ?></td>
						<td class="center"><?php echo $entry_file_sort_order; ?></td>
						<td class="center"><?php echo $entry_file_status; ?></td>
						<td class="center"><?php echo $column_action; ?></td>
					</tr>
				</thead>
				<tbody>
				<?php
					foreach ($product_files as $file) { ?>
						<tr >
							<td class="center"><?php echo $file['icon']; ?></td>
							<td><?php echo $file['name']; ?></td>
							<td class="center"><?php echo $file['max_downloads']; ?></td>
							<td class="center"><?php echo $file['sort_order']; ?></td>
							<td class="center"><?php echo $file['status']; ?></td>
							<td class="center">
								<a title="<?php echo $file['button_edit']->text; ?>"
								   href="<?php echo $file['button_edit']->href; ?>"
								   class="btn tooltips"
								   data-target="#file_modal" data-toggle="modal"><i class="fa fa-edit fa-lg"></i></a>
								<a title="<?php echo $file['button_delete']->text; ?>"
								   class="btn tooltips" data-confirmation="delete"
								   href="<?php echo $file['button_delete']->href; ?>"><i class="fa <?php echo $file['button_delete']->icon?> fa-lg"></i></a>
								<a title="<?php echo $file['push_to_customers']->text; ?>"
								   class="btn tooltips"
								   href="<?php echo $file['push_to_customers']->href; ?>"><i class="fa <?php echo $file['push_to_customers']->icon?> fa-lg"></i></a>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="panel-footer">
			<div class="row pull-right">
				<div class="col-sm-6 col-sm-offset-0">
					<a href="<?php echo $button_add_file->href; ?>"
					   data-target="#file_modal"
					   data-toggle="modal">
						<button class="btn btn-primary">
							<i class="fa fa-plus"></i> <?php echo $button_add_file->text; ?>
						</button>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'file_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax',
				'js_onload' => "$('#downloadFrm_activate').change(); "

		));
?>