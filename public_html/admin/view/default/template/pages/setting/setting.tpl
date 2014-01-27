<?php if (!empty($error['warning'])) { ?>
<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if (!empty($success)) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<?php echo $resources_scripts ?>
<div class="contentBox">
    <div class="cbox_tl">
        <div class="cbox_tr">
            <div class="cbox_tc">
                <div class="heading icon_title_setting"><?php echo $heading_title; ?></div>
                <div class="heading-tabs">
                    <a href="<?php echo $link_all; ?>"><span><?php echo $tab_all; ?></span></a>
                    <a href="<?php echo $link_details; ?>" <?php echo ($active == 'details' ? 'class="active"' : '') ?> ><span><?php echo $tab_details; ?></span></a>
                    <a href="<?php echo $link_general; ?>" <?php echo ($active == 'general' ? 'class="active"' : '') ?> ><span><?php echo $tab_general; ?></span></a>
                    <a href="<?php echo $link_checkout; ?>" <?php echo ($active == 'checkout' ? 'class="active"' : '') ?> ><span><?php echo $tab_checkout; ?></span></a>
                    <a href="<?php echo $link_appearance; ?>" <?php echo ($active == 'appearance' ? 'class="active"' : '') ?> ><span><?php echo $tab_appearance; ?></span></a>
                    <a href="<?php echo $link_mail; ?>" <?php echo ($active == 'mail' ? 'class="active"' : '') ?> ><span><?php echo $tab_mail; ?></span></a>
                    <a href="<?php echo $link_api; ?>" <?php echo ($active == 'api' ? 'class="active"' : '') ?> ><span><?php echo $tab_api; ?></span></a>
                    <a href="<?php echo $link_system; ?>" <?php echo ($active == 'system' ? 'class="active"' : '') ?> ><span><?php echo $tab_system; ?></span></a>
                </div>
                <div class="toolbar">
                    <?php if (!empty ($help_url)) : ?>
                    <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img
                        src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
                    <?php endif; ?>
                    <?php echo $form_language_switch; ?>
                    <div class="buttons">
                        <div class="flt_left align_left"><?php echo $text_edit_store_settings; ?> <?php echo $store_selector; ?></div>
                        <div class="flt_left">&nbsp;&nbsp;<?php echo $edit_store_button; ?></div>
                        <div class="flt_left">&nbsp;&nbsp;<?php echo $new_store_button; ?></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="cbox_cl">
        <div class="cbox_cr">
            <div class="cbox_cc">
                <?php echo $form['form_open']; ?>
                <div class="fieldset">
                    <div class="heading"><?php echo $form_title; ?></div>
                    <div class="top_left">
                        <div class="top_right">
                            <div class="top_mid"></div>
                        </div>
                    </div>
                    <div class="cont_left">
                        <div class="cont_right">
                            <div class="cont_mid">
                                <table class="form">
                                    <?php foreach ($form['fields'] as $name => $field) { ?>
                                    <tr id="<?php echo 'row_' . $name; ?>">
                                        <td><?php echo ${'entry_' . $name}; ?></td>
                                        <td <?php echo ($name == 'description' ? 'class="ml_ckeditor"' : '')?> >
                                            <?php
                                            switch ($name) {
                                                case 'logo':
                                                    echo $logo . $field;
                                                    break;
                                                case 'icon':
                                                    echo $icon . $field;
                                                    break;
                                                case 'template':
                                                    echo $field . '<div id="template"></div>';
                                                    break;
                                                case 'template_debug':
                                                    echo $field;
                                                    if ($storefront_debug_url) {
                                                        echo '( <a onClick="window.open(\'' . $storefront_debug_url . '\');">' . $text_front . '</a> |
							        <a onClick="window.open(\'' . $admin_debug_url . '\');">' . $text_admin . '</a> )';
                                                    }
                                                    break;
                                                default:
                                                    echo $field;
                                            }
                                            ?>
                                            <?php if (!empty($error[$name])) { ?>
                                            <div class="field_err"><?php echo $error[$name]; ?></div>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php }  ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="bottom_left">
                        <div class="bottom_right">
                            <div class="bottom_mid"></div>
                        </div>
                    </div>
                </div>
                <!-- <div class="fieldset"> -->

                <div class="buttons align_center">
                    <button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
                    <a class="btn_standard" href="<?php echo $cancel; ?>"><?php echo $form['cancel']; ?></a>
                </div>
                </form>

            </div>
        </div>
    </div>
    <div class="cbox_bl">
        <div class="cbox_br">
            <div class="cbox_bc"></div>
        </div>
    </div>
</div>

<script type="text/javascript"><!--

var zone_id = '<?php echo $settings['config_zone_id']; ?>';

getZones = function (id, country_id, zone) {
    if (!country_id) {
        return false;
    }

    if (!zone) {
        zone = 0;
    }

    $.ajax({
        url:'<?php echo $common_zone; ?>&country_id=' + country_id + '&zone_id=' + zone,
        type:'GET',
        dataType:'json',
        success:function (data) {
            result = data;
            showZones(id, data);
        },
        error:function (req, status, msg) {
        }
    });
}

showZones = function (id, data) {
    var options = '';

    $.each(data['options'], function (i, opt) {
        options += '<option value="' + i + '"';
        if (opt.selected) {
            options += 'selected="selected"';
        }
        options += '>' + opt.value + '</option>'
    });

    var selectObj = $('#' + id);

    selectObj.html(options);
    var selected_name = $('#' + id + ' :selected').text();

    selectObj.parent().find('span').text(selected_name);

}

jQuery(function ($) {

    $(".toolbar select").aform({
        triggerChanged:false
    });

    $('#template').load('<?php echo $template_image; ?>&template=' + encodeURIComponent($('select[name=\'config_storefront_template\']').attr('value')));
    $('#settingFrm_config_storefront_template').change(function () {
        $('#template').load('<?php echo $template_image; ?>&template=' + encodeURIComponent(this.value))
    });

    getZones('settingFrm_config_zone_id', $('#settingFrm_config_country_id').val(), zone_id);

    $('#settingFrm_config_country_id').change(function () {
        getZones('settingFrm_config_zone_id', $(this).val(), 0);
        $('#settingFrm_config_zone_id').val(0).change();
    });

    $('#store_switcher').aform({triggerChanged:false}).width(100);

});
<?php if ($active == 'mail') { ?>
jQuery(function () {
    $(document).ready(mail_toggle);
    $('#settingFrm_config_mail_protocol').change(mail_toggle);

    function mail_toggle() {
        var field_list = {'mail':[], 'smtp':[] };
        field_list.mail[0] = 'mail_parameter';

        field_list.smtp[0] = 'smtp_host';
        field_list.smtp[1] = 'smtp_username';
        field_list.smtp[2] = 'smtp_password';
        field_list.smtp[3] = 'smtp_port';
        field_list.smtp[4] = 'smtp_timeout';
        var show = $('#settingFrm_config_mail_protocol').val();
        var hide = show == 'mail' ? 'smtp' : 'mail';

        for (f in field_list[hide]) {
            $('#row_' + field_list[hide][f]).hide();
        }
        ;
        for (f in field_list[show]) {
            $('#row_' + field_list[show][f]).show();
        }
        ;
    }
});
    <?php } ?>
//--></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/adapters/jquery.js"></script>
<script type="text/javascript"><!--
$(document).ready(function () {
    if ($('#settingFrm_config_description_<?php echo $content_language_id; ?>').length) {
        $('#settingFrm_config_description_<?php echo $content_language_id; ?>').parents('.afield').removeClass('mask2');

        CKEDITOR.replace('settingFrm_config_description_<?php echo $content_language_id; ?>', {
            filebrowserBrowseUrl:false,
            filebrowserImageBrowseUrl:'<?php echo $rl; ?>',
            filebrowserWindowWidth:'920',
            filebrowserWindowHeight:'520',
            language:'<?php echo $language_code; ?>'
        });
    }


});
//--></script>