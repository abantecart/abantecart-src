<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $summary_form; ?>

<?php echo $product_tabs ?>

<div id="content" class="tab-content">

	<div class="panel-heading">

		<div class="pull-left form-inline">
		</div>

		<div class="pull-right">

			<div class="btn-group mr10 toolbar">
				<?php echo $form_language_switch; ?>
			</div>

			<div class="btn-group mr10 toolbar">
				<?php if (!empty ($help_url)) : ?>
					<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip"
					   title="" data-original-title="Help">
						<i class="fa fa-question-circle fa-lg"></i>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="panel-body panel-body-nopadding">
    	<?php echo $resources_html ?>
	</div>
</div>

<?php echo $resources_scripts ?>