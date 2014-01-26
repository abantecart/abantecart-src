<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="clr_both" style="width: 100%; text-align: right; margin: 0;"><?php echo $form_language_switch; ?></div>
<div class="contentBox">
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
                        <?php if ($name == 'language_definition_id') { ?>

                            <?php } else if (is_array($field)) { ?>
                            <?php foreach ($field as $lang_id => $f) { ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo $languages[$lang_id]['image']; ?>"
                                             alt="<?php echo $languages[$lang_id]['name']; ?>"/>
                                        <?php echo ${'entry_' . $name}; ?>
                                    </td>
                                    <td class="ml_field">
                                        <?php echo $f; ?>
                                        <?php echo $form['fields']['language_definition_id'][$lang_id]; ?>
                                        <?php if (!empty($error[$name][$lang_id])) { ?>
                                        <div class="field_err"><?php echo $error[$name][$lang_id]; ?></div>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            <?php } else { ?>
                            <tr>
                                <td><?php echo ${'entry_' . $name}; ?></td>
                                <td class="ml_field">
                                    <?php echo $field; ?>
                                    <?php if (!empty($error[$name])) { ?>
                                    <div class="field_err"><?php echo $error[$name]; ?></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
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
        <button type="submit" class="btn_standard button_loader" ><?php echo $form['submit']; ?></button>
        <a class="btn_standard" ><?php echo $form['cancel']; ?></a>
    </div>
    </form>
</div>
<?php echo $form_collector; ?>
<?php
if (!$language_definition_id) {
    ?>
<div id="dialog"></div>
<script type="text/javascript">
    $('#definitionQFrm_block, #definitionQFrm_language_key').blur(function () {
        if ($('#definitionQFrm_language_key').val() == '') {
            return false;
        }
        $.ajax(
            {   url:'<?php echo $check_url; ?>',
                type:'POST',
                data:$('#definitionQFrm').serializeArray(),
                dataType:'json',
                success:function (data) {
                    if (data.error.length > 0) {
                        $('#dialog').html(data.error);
                        $('#dialog').dialog({
                            title:'Error!',
                            width:300,
                            height:200,
                            resizable:false,
                            modal:false,
                            buttons:{ "close":function () {
                                $(this).dialog("destroy");
                            }    }
                        });
                    }
                },
                error:function (req, status, msg) {
                }
            });

    });
        <?php if ($ajax_wrapper_id && $ajax_reload_url) { ?>
    $('.language_box a.flag').click(function () {
        $.getJSON('<?php echo $ajax_reload_url;?>&content_language_code=' + $(this).attr('code'),
            function (response) {
                $('#<?php echo $ajax_wrapper_id?>').html(response.html);
            });
    });

        <?php } ?>
</script>

<?php } ?>