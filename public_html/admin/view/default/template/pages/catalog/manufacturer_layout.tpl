<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<ul class="nav nav-tabs nav-justified nav-profile">
	<li>
	<a href="<?php echo $manufacturer_edit ?>"><span><?php echo $tab_edit; ?></span></a></li>
	<li class="active">
	<a href="<?php echo $manufacturer_layout ?>"><span><?php echo $tab_layout; ?></span></a></li>
	<?php echo $this->getHookVar('extension_tabs'); ?>
</ul>
<div class="tab-content">

	<div class="panel-heading">

			<div class="pull-right">
			    <div class="btn-group mr10 toolbar">
                    <?php if (!empty ($help_url)) : ?>
                    <a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip" title="" data-original-title="Help">
                    <i class="fa fa-question-circle fa-lg"></i>
                    </a>
                    <?php endif; ?>
			    </div>
			</div>

	</div>
	<div class="panel-body panel-body-nopadding">
		<?php echo $layoutform; ?>
	</div>
</div>
