<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $category_tabs ?>
<div class="tab-content">

	<div class="panel-heading">
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<?php echo $layoutform; ?>
	</div>

</div>