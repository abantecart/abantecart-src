<?php
/** @see public_html/admin/view/default/template/common/action_confirm.tpl */
include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 pull-left">
				<a class="btn btn-primary lock-on-click tooltips"
                   href="<?php echo $insert; ?>" title="<?php echo_html2view($button_add); ?>">
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

<script type="application/javascript">
	/* run after grid load */
	var grid_ready = function() {
	<?php if($warning_actonbehalf){?>
		$('.grid_action_actonbehalfof').each(function(){

			$(this).attr('data-confirmation', 'delete');
			$(this).attr('data-confirmation-text', <?php js_echo($warning_actonbehalf); ?>);
		});
		<?php } ?>
	}
</script>
