<?php if (!empty($error['warning'])) { ?>
<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<a name="top"></a>

<div class="contentBox">
    <div class="cbox_tl">
        <div class="cbox_tr">
            <div class="cbox_tc">
                <div class="heading icon_title_product"><?php echo $form_title; ?></div>
                <?php echo $product_tabs ?>
                <div class="toolbar">
                    <?php if (!empty ($help_url)) : ?>
                    <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img
                        src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="cbox_cl">
        <div class="cbox_cr">
            <div class="cbox_cc">

                <?php echo $summary_form; ?>
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
                                    <tr>
                                        <td><?php echo ${'entry_' . $name}; ?></td>
                                        <td>
                                            <?php echo $field; ?>
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

<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/ui/jquery.ui.datepicker.js"></script>
<script type="text/javascript"><!--
$(document).ready(function () {
    $('.date').datepicker({dateFormat:'<?php echo $js_date_format?>'});
});
//--></script>