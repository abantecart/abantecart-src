<?php if (!empty($error['warning'])) { ?>
<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
    <div class="cbox_tl">
        <div class="cbox_tr">
            <div class="cbox_tc">
                <div class="heading icon_title_country"><?php echo $heading_title; ?></div>
                <div class="heading-tabs">
                    <a href="<?php echo $details ?>" <?php echo ($active == 'details' ? 'class="active"' : '') ?> ><span><?php echo $tab_details ?></span></a>
                    <?php if (!empty($locations)) { ?>
                    <a href="<?php echo $locations ?>" <?php echo ($active == 'locations' ? 'class="active"' : '') ?> ><span><?php echo $tab_locations ?></span></a>
                    <?php } ?>
                </div>
                <div class="toolbar">
                    <?php if (!empty ($help_url)) : ?>
                    <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img
                        src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
                    <?php endif; ?>
                    <?php echo $form_language_switch; ?>
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
                                    <tr>
                                        <td><?php echo ${'entry_' . $name}; ?></td>
                                        <td>
                                            <?php echo $field; ?>
                                            <?php if (!empty($error[$name])) { ?>
                                            <div class="field_err"><?php echo $error[$name]; ?></div>
                                            <?php } ?>
											<?php if($name=='zone'){?>
												<br/><br/>
												<a onclick="selectAll();"><?php echo $text_select_all; ?></a> /
												<a onclick="unselectAll();"><?php echo $text_unselect_all; ?></a>
												<br/>
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
var zone_id = '<?php echo $zone_id; ?>';
jQuery(function ($) {

    getZones = function (country_id) {
        if (!country_id) {
            return false;
        }

        $.ajax(
            {
                url:'<?php echo $common_zone; ?>&country_id=' + country_id + '&zone_id=0',
                type:'GET',
                dataType:'json',
                success:function (data) {
                    result = data;
                    showZones(data);
                },
                error:function (req, status, msg) {
                }
            });
    }

    showZones = function (data) {
        var options = '';

        $.each(data['options'], function (i, opt) {
			if(i!=0){
            options += '<label for="check_' + i + '">'
				+'<div class="afield acheckbox"><span>'
				+'<input id="check_'+i+'" type="checkbox" value="'+i+'" name="zone_id[]" style="opacity: 0;" />'
				+'</span></div>' + opt.value + '</label>';
			}

        });

        $('div.scrollbox'). html(options).each(function(){
		$("div.scrollbox input").aform({triggerChanged: true, showButtons: false });
		});

    }
    if(!$('#cgFrm_zone_id\\\[\\\]').val()){
        getZones($('#cgFrm_country_id').val());
    }
    $('#cgFrm_country_id').change(function () {
        getZones($(this).val());
        $('#cgFrm_zone_id\\\[\\\]').val('').change();

    });
});

function selectAll()
{
	$('input[name*=\'zone_id\[\]\']').attr('checked', 'checked');
	$('div.scrollbox').find('.afield').addClass('checked');
}

function unselectAll()
{
	$('input[name*=\'zone_id\[\]\']').removeAttr('checked');
	$('div.scrollbox').find('.afield').removeClass('checked');
}
//--></script>
