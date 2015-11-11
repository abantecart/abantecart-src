<?php if($multivalue['name']){ ?>
<b class="multivalue_hidden_name"><?php echo $multivalue['name'] ?></b>
<?php }
if (!empty($multivalue['description'])) { ?>
<p class="multivalue_hidden_description"><?php echo $multivalue['description'] ?></p>
<?php } ?>
<span class="multivalue_hidden_data">

                        <span class="multivalue_inner">
                            <textarea style="display: none;" id="<?php echo $id ?>_buffer"><?php echo $selected ?></textarea>
                            <textarea name="<?php echo $selected_name ?>" style="display: none;" id="<?php echo $id ?>_selected"><?php echo $selected ?></textarea>

                            <div class="mask1" style="float:left;">
                                <div class="cl">
                                    <div class="cr">
                                        <div class="cc">
                            <span id="<?php echo $id ?>_count_text" class="multivalue count_text">
                                <?php echo $text_selected ?><span id="<?php echo $return_to ?>" class="multivalue count"></span>
                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <span id="<?php echo $id ?>_save_reset" class="multivalue save_reset">
                                <a class="btn_standard"><span title="<?php echo $text_save ?>" class="button1"
                                                              id="btn_save"><span><?php echo $text_save ?></span></span></a>
                                <a class="btn_standard"><span title="<?php echo $text_reset ?>" class="button2"
                                                              id="btn_reset"><span><?php echo $text_reset ?></span></span></a>
                            </span>
                            <span class="abuttons_grp" style="display: inline-block; vertical-align: middle; float: left;">
                                <button class="btn_standard" type="button"
                                        onclick="<?php echo $id; ?>_show_popup('<?php echo $id ?>');"><span
                                    title="<?php echo $text_edit ?>" class="button3"><span><?php echo $text_edit ?></span></span>
                                </button>
                            </span>

                        </span>
   <div id="<?php echo $id ?>_popup_dialog"></div>
</span>


<script type="text/javascript" src="admin/view/default/javascript/jquery/ui/jquery.ui.draggable.js"></script>
<script type="text/javascript">

    $('#<?php echo $id ?>_save_reset').find('a:eq(0)').click(function () {
        $(this).closest('form').submit();
    });

    $('#<?php echo $id ?>_save_reset').find('a:eq(1)').click(function () {
        $('#<?php echo $return_to ?>').html(cnt_ovalue);
        $('#<?php echo $id ?>_selected').html(ovalue);
        $('#<?php echo $id ?>_buffer').html(ovalue);
        $('#<?php echo $return_to ?>').parent().removeClass('multivalue changed');
        <?php echo $js['cancel'];?>;
        $('#<?php echo $id ?>_save_reset').hide();
    });

    <?php echo $id; ?>_show_popup = function (data_id) {
        var content_url = '<?php echo $content_url;?>';
        var postdata = <?php echo ($postvars ? $postvars : '{}'); ?>;
        postdata['selected'] = $('#<?php echo $id ?>_selected').html();

        var $Popup = $('#<?php echo $id ?>_popup_dialog').dialog({
            title:<?php js_echo($title); ?>,
            autoOpen:true,
            bgiframe:false,
            width: <?php echo $popup_width; ?>,
            height: <?php echo $popup_height; ?>,
            draggable:true,
            buttons:{
                "Apply selection":function () {
                    // check changes
                    var nosave = <?php echo ($no_save ? 'true' : 'false'); ?>;
                    $('#<?php echo $id ?>_selected').html($('#<?php echo $id ?>_buffer').html());
                    $('#<?php echo $return_to ?>').html(<?php echo $id; ?>_count_selected('<?php echo $id ?>_selected'));
                    if (ovalue != $('#<?php echo $id ?>_selected').html()) {
                        if (!nosave) {
                            $('#<?php echo $id ?>_save_reset').show();
                            $('#<?php echo $return_to ?>').parents('.mask1').addClass('multivalue changed');
                        }

                    }
                    <?php echo $js['apply'];?>;
                    $(this).dialog('destroy');
                },
                "cancel":function () {
                    <?php echo $js['cancel'];?>;
                    $(this).dialog('destroy');
                }
            },
            modal:true,
            close:function (event) {
                $(this).dialog('destroy');
            }
        });
        // spinner
        $("#<?php echo $id ?>_popup_dialog").html('<div class="progressbar">Loading ...</div>');

        $.ajax({
            url:content_url,
            type:'POST',
            dataType:'html',
            data:postdata,
            success:function (data) {
                $("#<?php echo $id ?>_popup_dialog").html(data);
            }
        });
    }


    <?php echo $id; ?>_count_selected = function (id) {
        if (!$('#' + id)) {
            alert("can't to count selected items from hidden field '" + id + "'!");
            return false;
        }
        var selected = 0;
        var val = $('#' + id).html();
        var data = jQuery.parseJSON(val);

        for (var e in data) {
            if (data[e].hasOwnProperty('status')) {
                if (data[e]['status']) {
                    selected++;
                }
            } else {
                selected++;
            }
        }
        return selected;
    }
    $('#<?php echo $return_to ?>').html(<?php echo $id; ?>_count_selected('<?php echo $id ?>_selected'));
    // old values
    var ovalue = jQuery.parseJSON($('#<?php echo $id ?>_selected').html());

    ovalue = JSON.stringify(ovalue);
    // reformat default json-data for future comparison
    $('#<?php echo $id ?>_selected').html(ovalue);
    var cnt_ovalue = $('#<?php echo $return_to ?>').html();

</script>