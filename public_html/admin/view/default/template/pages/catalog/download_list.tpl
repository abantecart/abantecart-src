<?php
/** @see public_html/admin/view/default/template/common/action_confirm.tpl */
include($tpl_common_dir . 'action_confirm.tpl'); ?>
<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 pull-left">
				<a class="actionitem btn btn-primary tooltips"  href="<?php echo $button_insert->href; ?>"
					data-target="#download_modal" data-toggle="modal" title="<?php echo_html2view($button_add); ?>">
				<i class="fa fa-plus"></i>
				</a>
			</div>
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
            'id'          => 'download_modal',
            'modal_type'  => 'lg',
            'data_source' => 'ajax',
            'js_onload'   => "$('#downloadFrm_activate').change(); bindCustomEvents('#downloadFrm');"
        ]
);
?>
<script type="text/javascript">
	var grid_ready = function(){
		$('.grid_action_edit[data-toggle!="modal"]').each(function(){
			$(this).attr('data-toggle','modal'). attr('data-target','#download_modal');
		});
	}
</script>