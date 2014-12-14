<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $summary_form; ?>

<?php echo $product_tabs ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>
		
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
    	<?php echo $resources_html ?>
	</div>
</div>

<?php echo $resources_scripts ?>