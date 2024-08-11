<?php
include($tpl_common_dir.'action_confirm.tpl'); ?>
<?php echo $setting_tabs ?>

    <div id="content" class="panel panel-default">
        <div class="panel-heading col-xs-12">
            <div class="primary_content_actions pull-left">
                <div class="btn-group mr10 toolbar">
            <?php
                    if (!empty($search_form)) { ?>
                        <form id="<?php echo $search_form['form_open']->name; ?>"
                              method="<?php echo $search_form['form_open']->method; ?>"
                              name="<?php echo $search_form['form_open']->name; ?>"
                              class="form-inline"
                              role="form">
                            <div class="form-group">
                                <label class="control-label">
                                    <?php echo $text_edit_store_settings; ?>
                                </label>
                                <div class="input-group input-group-sm">
                                    <?php echo $search_form['fields']['store_selector']; ?>
                                </div>
                            </div>
                        </form>
                    <?php
                    } ?>
                </div>
            </div>
            <?php
            include($tpl_common_dir.'content_buttons.tpl'); ?>
        </div>

        <div class="panel-body panel-body-nopadding tab-content col-xs-12">
            <?php
            echo $listing_grid; ?>
        </div>

    </div>

<?php
echo $this->html->buildElement(
    [
        'type'        => 'modal',
        'id'          => 'setting_modal',
        'name'        => 'setting_modal',
        'modal_type'  => 'lg',
        'data_source' => 'ajax',
    ]
);
?>

    <script type="text/javascript">
        var grid_ready = function (data) {
            $('.grid_action_edit').each(function () {
                if ($(this).is('[href*=appearance]') || $(this).is('[href*=im]')) {
                    var id = $(this).parents('tr').attr('id');
                    var new_href = data.userdata.href[id] + '&active=' + $(this).attr('rel');
                    $(this).attr('href', new_href).attr('target', '_blank');
                } else {
                    $(this).attr('data-toggle', 'modal').attr('data-target', '#setting_modal');
                }
            });

            $('td[aria-describedby="setting_grid_value"], td[aria-describedby="setting_grid_value"] button').click(function () {
                $(this).parents('tr').find('.grid_action_edit').click();
            });

        }

        $('#store_switcher').change(function () {
            goTo(<?php js_echo($store_edit_url);?>, 'store_id=' + $(this).val());
        });

    </script>
<?php
if ($resources_scripts) {
    echo $resources_scripts;
} ?>