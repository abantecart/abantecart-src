<?php
/** @see public_html/admin/view/default/template/common/action_confirm.tpl */
include($tpl_common_dir . 'action_confirm.tpl'); ?>
<ul class="nav nav-tabs nav-justified nav-profile">
<?php
	foreach ($tabs as $tab) {
        $classname = $tab['active'] ? 'active' : ''; ?>
		<li class="<?php echo $classname; ?>">
			<a <?php echo($tab['href'] ? 'href="' . $tab['href'] . '" ' : ''); ?>><strong><?php echo $tab['text']; ?></strong></a>
		</li>
	<?php }
    echo $this->getHookVar('extension_tabs'); ?>
</ul>
<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
            <div class="btn-group mr10 pull-left">
                <a class="btn btn-primary tooltips" title="<?php echo_html2view($button_add); ?>"
                   href="<?php echo $insert_href; ?>" data-toggle="modal" data-target="#transaction_modal">
                    <i class="fa fa-plus"></i>
                </a>
            </div>
			<div class="btn-group mr10 pull-left">
			    <a class="btn btn-white disabled"><?php echo $balance; ?></a>
			    <?php if($button_orders_count){ ?>
                    <a target="_blank" class="btn btn-white tooltips"
                       href="<?php echo $button_orders_count->href; ?>"
                       data-toggle="tooltip" title="<?php echo_html2view($button_orders_count->title); ?>"
                       data-original-title="<?php echo_html2view($button_orders_count->title); ?>"><?php echo $button_orders_count->text; ?></a>
			    <?php } ?>
			    <a target="_blank"
			       class="btn btn-white tooltips"
			       href="<?php echo $actas->href; ?>"
			       data-toggle="tooltip"
			       title="<?php echo_html2view($actas->text); ?>"
			    <?php
                //for additional store show warning about login in that store's admin (because of crossdomain restriction)
                if($warning_actonbehalf){ ?>
                    data-confirmation="delete" data-confirmation-text="<?php echo_html2view($warning_actonbehalf);?>"
                <?php } ?>
			       data-original-title="<?php echo_html2view($actas->text); ?>"><i class="fa fa-male"></i></a>
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