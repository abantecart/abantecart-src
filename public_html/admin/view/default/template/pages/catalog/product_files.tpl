<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $summary_form; ?>
<?php echo $product_tabs ?>

<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
				<a class="btn btn-primary tooltips" href="<?php echo $button_add_file->href; ?>" data-target="#file_modal" data-toggle="modal" title="<?php echo $button_add_file->text; ?>">
				<i class="fa fa-plus"></i>
				</a>
			</div>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
	
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