<?php
/** @see public_html/admin/view/default/template/common/action_confirm.tpl */
include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr5 pull-left">
				<a class="btn btn-primary tooltips" href="<?php echo $insert; ?>" title="<?php echo_html2view($button_add); ?>">
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
<script type="text/javascript">
    $('#category_grid_wrapper a.grid_action_expand').click(function(){
        var new_url = '<?php echo $grid_url; ?>&'+$(this).attr('rel');
        $('#category_grid')
            .jqGridHistory('setGridParam',{url:new_url})
            .trigger("reloadGrid");
        return false;
    });

	var grid_ready = function(data) {
		var url = '<?php echo $embed_url?>';
		$('#category_grid tr[role="row"]').each(function () {
			if ($(this).attr('id')) {
				url += '&category_id[]=' + $(this).attr('id');
			}
		});
		$('a[data-target="#embed_modal"]').attr('href', url);

	}
</script>
