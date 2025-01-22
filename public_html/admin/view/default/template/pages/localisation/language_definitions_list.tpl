<?php
/** @see public_html/admin/view/default/template/common/action_confirm.tpl */
include($tpl_common_dir . 'action_confirm.tpl'); ?>
<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 pull-left">
				<a id="insert_btn" class="actionitem btn btn-primary tooltips"
				   data-toggle="modal" data-target="#ld_modal"
				   href="<?php echo $insert; ?>" title="<?php echo_html2view($button_add); ?>">
				<i class="fa fa-plus fa-fw"></i>
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

<?php
echo $this->html->buildElement(
		[
            'type'        => 'modal',
            'id'          => 'ld_modal',
            'modal_type'  => 'lg',
            'data_source' => 'ajax'
        ]
);
?>
<script type="text/javascript">
	function grid_ready(data){
		$('.grid_action_edit').each( function () {
			$(this).attr('data-toggle','modal').attr('data-target','#ld_modal');
		});

		$('#lang_definition_grid tr').each(function(){
			if(!data.hasOwnProperty('userdata') || !data['userdata'].hasOwnProperty('section')){
				return false;
			}
			var value = data['userdata']['section'][$(this).attr('id')];

			if(value==1){
				$(this).find('td[aria-describedby="lang_definition_grid_block"]').prepend('<i class="fa fa-lock" title="<?php echo $entry_section.': '.$text_admin;?>"></i>&nbsp;');
			}else{
				$(this).find('td[aria-describedby="lang_definition_grid_block"]').prepend('<i class="fa fa-globe" title="<?php echo $entry_section.': '.$text_storefront;?>"></i>&nbsp;');
			}
		});
	}
</script>