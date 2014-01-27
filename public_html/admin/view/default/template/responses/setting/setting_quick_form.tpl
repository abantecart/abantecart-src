<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="clr_both" style="text-align: right; margin: 0;"><?php echo $store_selector; ?></div>
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
                    <table style="width: auto;" class="form">
                        <?php foreach ($form['fields'] as $name => $field) { ?>
                            <tr>
                                <td><?php echo ${'entry_' . $name}; ?></td>
                                <td class="ml_field">
                                    <?php echo $field.($name=='template' ? '<div id="template"></div>' : ''); ?>
                                    <?php if (!empty($error[$name])) { ?>
                                    <div class="field_err"><?php echo $error[$name]; ?></div>
                                    <?php }  ?>
                                </td>
                            </tr>
                        <?php } //foreach ($form['fields'] as $name => $field)  ?>
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
        <button type="submit" class="btn_standard button_loader"><?php echo $form['submit']; ?></button>
        <a class="btn_standard"><?php echo $form['cancel']; ?></a>
    </div>
    </form>
</div>
<?php echo $form_collector;

if (!$active) {     ?>
<div id="dialog"></div>
<?php } ?>
<script type="text/javascript">
$('#template').load('<?php echo $template_image; ?>&template=' + encodeURIComponent($('select[name=\'config_storefront_template\']').attr('value')));
    $('#qsFrm_config_storefront_template').change(function () {
        $('#template').load('<?php echo $template_image; ?>&template=' + encodeURIComponent(this.value))
    });

</script>


