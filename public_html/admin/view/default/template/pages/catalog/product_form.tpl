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
                    <?php echo $form_language_switch; ?>
					<div class="buttons">
						<a class="btn_toolbar" title="<?php echo $text_clone; ?>" href="<?php echo $clone_url; ?>">
							<span class="icon_grid_clone">&nbsp;</span>
						</a>
					</div>                    
                </div>
            </div>
        </div>
    </div>
    <div class="cbox_cl">
        <div class="cbox_cr">
            <div class="cbox_cc">

                <?php echo $summary_form; ?>
                <?php echo $form['form_open']; ?>
                <?php foreach ($form['fields'] as $section => $fields) { ?>
                <div class="fieldset">
                    <div class="heading"><?php echo ${'tab_' . $section}; ?></div>
                    <div class="top_left">
                        <div class="top_right">
                            <div class="top_mid"></div>
                        </div>
                    </div>
                    <div class="cont_left">
                        <div class="cont_right">
                            <div class="cont_mid">
                                <table class="form">
                                    <?php foreach ($fields as $name => $field) { ?>
                                    <tr>
                                        <td><?php echo ${'entry_' . $name}; ?></td>
                                        <td <?php echo ($name == 'description' ? 'class="ml_ckeditor"' : '')?> >
                                            <?php echo $field; ?>
                                            <?php if (is_array($error[$name]) && !empty($error[$name][$language_id])) { ?>
                                            <div class="field_err"><?php echo $error[$name][$language_id]; ?></div>
                                            <?php } else if (!empty($error[$name])) { ?>
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
                </div><!-- <div class="fieldset"> -->
                <?php }  ?>

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

<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/adapters/jquery.js"></script>
<script type="text/javascript"><!--

$(document).ready(function () {
    var array = ['#productFrm_price',
        '#productFrm_cost',
        '#productFrm_shipping_price',
        '#productFrm_length',
        '#productFrm_width',
        '#productFrm_height',
        '#productFrm_weight'];

});

$('#productFrm_generate_seo_keyword').click(function(){
	var seo_name = $('#productFrm_product_description\\[name\\]').val().replace('%','');
	$.get('<?php echo $generate_seo_url;?>&seo_name='+seo_name, function(data){
		$('#productFrm_keyword').val(data).change();
	});
});

if (document.getElementById('productFrm_product_description[description]'))
    $('#productFrm_product_description\\[description\\]').parents('.afield').removeClass('mask2');
CKEDITOR.replace('productFrm_product_description[description]',
    {
        filebrowserBrowseUrl:false,
        filebrowserImageBrowseUrl:'<?php echo $rl; ?>',
        filebrowserWindowWidth:'920',
        filebrowserWindowHeight:'520',
        language:'<?php echo $language_code; ?>'
    }
);
//--></script>