<?php
/** @see public_html/admin/view/default/template/common/action_confirm.tpl */
include($tpl_common_dir . 'action_confirm.tpl'); ?>
<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">

            <div class="btn-group mr5 pull-left">
                <a class="actionitem btn btn-primary lock-on-click tooltips"
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
	</div>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>
</div>
<script type="application/javascript">
    $("#product_grid_go").on('click', function () {
        //get all selected rows based on multiselect
        var ids = $('#product_grid').jqGrid('getGridParam', 'selarrrow');
        //get single selected row
        ids.push($('#product_grid').jqGrid('getGridParam', 'selrow'));
        if (!ids.length) {
            return;
        }

        if ($('#product_grid_selected_action').val() == 'relate') {
            var form_data = $('#product_grid_form').serializeArray();
            form_data.push({name: 'id', value: ids});
            form_data.push({name: 'oper', value: 'relate'});
            $.ajax({
                url: '<?php echo $relate_selected_url; ?>',
                type: 'POST',
                data: form_data,
                success: function (msg) {
                    if (msg == '') {
                        jQuery('#product_grid').trigger("reloadGrid");
                        success_alert('<?php js_echo($text_success_relation_set);?>',true);
                    } else {
                        alert(msg);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(textStatus + ": " + errorThrown);
                }
            });
        }

        if ($('#product_grid_selected_action').val() == 'create_collection') {
            //Get unique ids
            ids = ids.filter((value, index, self) => {
                return self.indexOf(value) === index;
            })
            window.open('<?php echo $create_collection_url; ?>'+'&'+$.param({products: ids}), '_blank')
        }
    });
</script>