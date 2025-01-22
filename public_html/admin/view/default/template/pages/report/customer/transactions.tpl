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
<?php echo $this->html->buildElement(
		[
            'type'        => 'modal',
            'id'          => 'transaction_modal',
            'modal_type'  => 'lg',
            'data_source' => 'ajax'
        ]
);
?>
<script type="text/javascript">
	var updateViewButtons = function(){
		$('.grid_action_view[data-toggle!="modal"]').each(function(){
			$(this).attr('data-toggle','modal'). attr('data-target','#transaction_modal');
		});
	};
</script>