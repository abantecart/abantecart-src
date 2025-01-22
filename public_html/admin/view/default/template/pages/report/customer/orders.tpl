<?php
/** @see public_html/admin/view/default/template/common/action_confirm.tpl */
include($tpl_common_dir . 'action_confirm.tpl'); ?>
<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
            <?php
            /** @see public_html/admin/view/default/template/common/grid_search_form.tpl */
            include($tpl_common_dir . 'grid_search_form.tpl');?>
		</div>
		<?php
        /** @see public_html/admin/view/default/template/common/content_buttons.tpl */
        include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>
</div>