<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<ul class="nav nav-tabs nav-justified nav-profile">
	<li>
	<a href="<?php echo $manufacturer_edit ?>"><span><?php echo $tab_edit; ?></span></a></li>
	<li class="active">
	<a href="<?php echo $manufacturer_layout ?>"><span><?php echo $tab_layout; ?></span></a></li>
	<?php echo $this->getHookVar('extension_tabs'); ?>
</ul>

<?php
include($tpl_common_dir . 'page_layout_form.tpl');
?>