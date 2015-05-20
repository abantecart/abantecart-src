<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
<?php if ($tax_class_id) { ?>
	<ul class="nav nav-tabs nav-justified nav-profile">
		<?php
		foreach ($tabs as $tab) {
			if ($tab['active']) {
				$classname = 'active';
			} else {
				$classname = '';
			}
			?>
			<li class="<?php echo $classname; ?>"><a <?php echo($tab['href'] ? 'href="' . $tab['href'] . '" ' : ''); ?>><strong><?php echo $tab['text']; ?></strong></a>
			</li>
		<?php } ?>

		<?php echo $this->getHookVar('extension_tabs'); ?>
	</ul>
<?php } ?>
<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
				<a class="actionitem btn btn-primary lock-on-click tooltips" href="<?php echo $insert; ?>" title="<?php echo $button_add; ?>">
				<i class="fa fa-plus fa-fw"></i>
				</a>
			</div>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>

</div>