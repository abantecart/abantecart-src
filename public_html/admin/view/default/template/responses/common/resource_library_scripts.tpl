<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/ui/jquery.ui.draggable.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/ui/jquery.ui.resizable.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>/javascript/jquery/ui/external/bgiframe/jquery.bgiframe.js"></script>
<script type="text/javascript">
var urls = {
	resource_library:'<?php echo $rl_resource_library; ?>',
	resources:'<?php echo $rl_resources; ?>',
	resource_single:'<?php echo $rl_resource_single; ?>',
	unmap:'<?php echo $rl_unmap; ?>',
	del:'<?php echo $rl_delete; ?>',
	resource:'<?php echo HTTP_DIR_RESOURCE; ?>'
	},
	default_type = '<?php echo $default_type["type_name"]; ?>';	

onSelectClose = function (e, ui) {}

var selectDialog = function (type, field) {
	$('#dialog').remove();
	
	window.selectField = field;
	var src = urls.resource_library + '&type=' + type;
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="' + src + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	$('#dialog iframe').load(function (e) {
	    try {
	        var error_data = $.parseJSON($(this).contents().find('body').html());
	    } catch (e) {
	        var error_data = null;
	    }
	    if (error_data && error_data.error_code) {
	        $('#dialog').dialog('close');
	        httpError(error_data);
	    }
	});
	
	$('#dialog').dialog({
	    title:'<?php echo $text_resource_library; ?>',
	    close:onSelectClose,
	    width:900,
	    height:500,
	    resizable:false,
	    modal:true
	});
};

var mediaDialog = function (type, action, id, field, wrapper_id) {
    window.selectField = field;
    window.wrapper_id = wrapper_id;

    $('#dialog').remove();
    var src = urls.resource_library + '&' + action + '=1&type=' + type;
    if (id) {
        src += '&resource_id=' + id;
    }
    $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px; display:none;"><iframe src="' + src + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
    $('#dialog iframe').load(function (e) {
        try {
            var error_data = $.parseJSON($(this).contents().find('body').html());
        } catch (e) {
				var iframedocument = $(this).contents().get(0);
				var contentType = iframedocument.contentType || iframedocument.mimeType;
            	var error_data = null;
        }
        if ((error_data && error_data.error_code) || contentType=='application/json') {
            $('#dialog').dialog('close');
            httpError(error_data);
        }else{
			$('#dialog').css('display','block');
		}
    });

    $('#dialog').dialog({
        title:'<?php echo $text_resource_library; ?>',
        close:function (event, ui) {
        //reload original media list to show new selections
        //not for URL mode
        <?php if($mode != 'url') { ?>
        <?php 	foreach ($types as $type) { ?>
            loadMedia('<?php echo $type['type_name']?>');
        <?php 	} ?>
        <?php } ?>
        },
        width:900,
        height:500,
        resizable:false,
        modal:true
    });
};

var loadMedia = function (type) {
    $.ajax({
        url:urls.resources,
        type:'GET',
        data:{ type:type },
        dataType:'json',
        success:function (json) {

            if (!json.items.length && type != default_type) {
                $('#type_' + type).hide();
                return;
            }
            $('#type_' + type).show();

            var html = '';
            $(json.items).each(function (index, item) {
                var src = '<img src="' + item['thumbnail_url'] + '" title="' + item['name'] + '" />';
                if (type == 'image' && item['resource_code']) {
                    src = item['thumbnail_url'];
                }
                html += '<span id="image_row' + item['resource_id'] + '" class="image_block">\
                <a class="resource_edit" type="' + type + '" id="' + item['resource_id'] + '">' + src + '</a><br />\
                ' + ( item['mapped'] > 1 ? '' : '<a class="btn_action resource_delete" id="' + item['resource_id'] + '"><span class="icon_s_delete"><span class="btn_text"><?php echo $button_delete ?></span></span></a>') + '\
                <a class="btn_action resource_unmap" id="' + item['resource_id'] + '"><span class="icon_s_unmap"><span class="btn_text"><?php echo $button_unmap ?></span></span></a>\
                <a class="btn_action resource_edit" type="' + type + '" id="' + item['resource_id'] + '"><span class="icon_s_edit"><span class="btn_text"><?php echo $button_edit ?></span></span></a>\
                </span>';
            });
            html += '<span class="image_block"><a class="resource_add" type="' + type + '"><img src="<?php echo $template_dir . '/image/icons/icon_add_media.png'; ?>" alt="<?php echo $text_add_media; ?>"/></a></span>';
				$('#type_' + type + ' td.type_blocks').html(html);
        },
        error:function (jqXHR, textStatus, errorThrown) {
            $('#type_' + type).show();
            $('#type_' + type + ' td.type_blocks').html('<div class="error" align="center"><b>' + textStatus + '</b>  ' + errorThrown + '</div>');
        }
    });

}

var loadSingle = function (type, wrapper_id, resource_id, field) {

    $("#confirm_del_dialog_" + wrapper_id).dialog({
        draggable:false,
        resizable:false,
        autoOpen:false,
        modal:true
    });
    $.ajax({
        url:urls.resource_single + '&resource_id=' + resource_id,
        type:'GET',
        data:{ type:type },
        dataType:'json',
        success:function (item) {

            if (!item.length && type != default_type) {
                $('#type_' + type).hide();
                return;
            }

            var html = '';
            var src = '<img src="' + item['thumbnail_url'] + '" title="' + item['name'] + '" />';

            if (type == 'image' && item['resource_code']) {
                src = item['thumbnail_url'];
            }
            html += '<span id="' + wrapper_id + '_' + item['resource_id'] + '" class="image_block">\
                <a class="resource_edit" type="' + type + '" id="' + item['resource_id'] + '">' + src + '</a><br />';
            if (item['resource_id']) {
                html += ( item['mapped'] > 1
						? '' : '<a class="btn_action resource_delete" id="' + item['resource_id'] + '"><span class="icon_s_delete"><span class="btn_text"><?php echo $button_unlink ?></span></span></a>') + '\
					<a class="btn_action resource_edit" type="' + type + '" id="' + item['resource_id'] + '"><span class="icon_s_edit"><span class="btn_text"><?php echo $button_edit ?></span></span></a>';
            }
            html += '</span>';
            $('#' + wrapper_id + '.type_blocks').html(html);
            // click events
            $('#' + wrapper_id + '_' + item['resource_id'] + ' a.resource_edit').unbind('click');
            $('#' + wrapper_id + '_' + item['resource_id'] + ' a.resource_edit').click(function () {
                var action = item['resource_id'] ? 'update' : 'add';
                mediaDialog($(this).prop('type'), action, item['resource_id'], field, wrapper_id);
                return false;
            });

            $('#' + wrapper_id + '_' + resource_id + ' .resource_delete').unbind('click');
            $('#' + wrapper_id + '_' + resource_id + ' .resource_delete').click(function () {
                var that = this;
                $("#confirm_del_dialog_" + wrapper_id).dialog('option', 'buttons', {
                    "<?php echo $button_unlink ?>":function () {
                        $(that).parent().remove();
                        //change hidden element and mark ad changed 
                        $('input[name="' + field + '"]').val('').addClass('afield changed');
                        $('form').prop('changed', 'true');
                        loadSingle(type, wrapper_id, '', field);
                        $(this).dialog("close");
                    },
                    "<?php echo $button_cancel ?>":function () {
                        $(this).dialog("close");
                    }
                });
                $("#confirm_del_dialog_" + wrapper_id).dialog('open');
                return false;
            });

        },
        error:function (jqXHR, textStatus, errorThrown) {
            $('#' + wrapper_id).show();
            $('#' + wrapper_id + '.type_blocks').html('<div class="error" align="center"><b>' + textStatus + '</b>  ' + errorThrown + '</div>');
        }
    });
}

<?php if ($mode == '') { ?>
jQuery(function () {

    <?php foreach ($types as $type) { ?>
        loadMedia('<?php echo $type['type_name']?>');
    <?php } ?>

    $('a.resource_add').live('click', function () {
        mediaDialog($(this).prop('type'), 'add');
        return false;
    });


    $("#confirm_del_dialog").dialog({
        draggable:false,
        resizable:false,
        autoOpen:false,
        modal:true
    });
    $('a.resource_delete').live('click', function () {
        var that = this;
        $("#confirm_del_dialog").dialog('option', 'buttons', {
            "<?php echo $button_delete ?>":function () {
                $.ajax({
                    url:urls.del + '&resource_id=' + $(that).prop('id'),
                    type:'GET',
                    dataType:'json',
                    success:function (json) {
                        if (json) {
                            $(that).parent().remove();
                        }
                    }
                });
                $(this).dialog("close");
            },
            "<?php echo $button_cancel ?>":function () {
                $(this).dialog("close");
            }
        });
        $("#confirm_del_dialog").dialog('open');
        return false;
    });


    $("#confirm_unmap_dialog").dialog({
        draggable:false,
        resizable:false,
        autoOpen:false,
        modal:true
    });
    $('a.resource_unmap').live('click', function () {
        var that = this;
        $("#confirm_unmap_dialog").dialog('option', 'buttons', {
            "<?php echo $button_unmap ?>":function () {
                $.ajax({
                    url:urls.unmap + '&resource_id=' + $(that).prop('id'),
                    type:'GET',
                    dataType:'json',
                    success:function (json) {
                        if (json) {
                            $(that).parent().remove();
                        }
                    }
                });
                $(this).dialog("close");
            },
            "<?php echo $button_cancel ?>":function () {
                $(this).dialog("close");
            }
        });
        $("#confirm_unmap_dialog").dialog('open');
        return false;
    });

    $('a.resource_edit').live('click', function () {
        mediaDialog($(this).prop('type'), 'update', $(this).prop('id'));
        return false;
    });
});

    <?php } ?>

</script>