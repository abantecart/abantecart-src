<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
<meta http-equiv="x-ua-compatible" content="IE=8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo $template_dir; ?>javascript/jquery/ui/themes/ui-lightness/ui.all.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $template_dir; ?>stylesheet/stylesheet.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $template_dir; ?>stylesheet/button.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $template_dir; ?>stylesheet/resource.css"/>
<script type="text/javascript" src="<?php echo $ssl ? 'https': 'http'?>://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/aform.js"></script>
</head>
<body>
<div id="container" >
    <div id="column_left">
        <div class="search_box">
            <div class="cl"><div class="cr"><div class="cc">
                <form id="searchform" action="<?php echo $search_action; ?>" method="post">
                    <span class="icon_search">&nbsp;</span>
                    <input type="text" name="search" value="<?php echo $text_search; ?>"
                        onFocus="if(this.value == '<?php echo $text_search; ?>') {this.value = '';}"
                        onBlur="if (this.value == '') {this.value = '<?php echo $text_search; ?>';}" />
                    <a href="#" class="btn_search btn_standard"><?php echo $button_go; ?></a>
                </form>
            </div></div></div>
        </div>

	    <div class="language">
		    <?php echo $language; ?>
	    </div>
        <div id="types">
            <div class="sub_title"><?php echo $text_type; ?></div>
            <ul>
                <?php foreach ($types as $t){ ?>
                <?php if ($mode == '' || ( $mode == 'url' && $default_type == $t['type_name'] ) ){ ?>
                <li>
                    <a href="<?php echo $t['type_name']; ?>" <?php echo ( $default_type == $t['type_name'] ? 'class="selected"' : '') ?> >
                        <?php echo $t['type_name']; ?>
                    </a>
                </li>
                <?php }} ?>
            </ul>
        </div>

        <a id="add_resource" class="btn_standard add_resource"><?php echo $button_add_resource; ?></a>
        <span id="add_resource_msg" ></span>
        <a id="done_resource" class="btn_standard"><?php echo $button_done; ?></a>
    </div>
    <div id="column_right_wrapper">
        <ul class="tabs">
            <?php if ($mode == '') : ?>
            <li><a class="selected" href="#column_right" id="object"><?php echo $object_title; ?></a></li>
            <?php endif; ?>
            <li style="float: right; margin-right:15px;"><a <?php if ($mode == 'url') { echo 'class="selected"'; } ?> href="#column_right" id="library"><?php echo $heading_title; ?></a></li>
        </ul>
        <a href="#" id="button_save_order" class="btn_standard"><?php echo $button_save_order; ?></a>
        <div id="column_right"></div>
    </div>
    <?php if ($mode == '') : ?>
    <div id="multiactions">
        <?php echo $text_with_selected ?>
        <select name="actions" >
            <option value=""><?php echo $text_select ?></option>
            <option value="map"><?php echo $text_map; ?></option>
            <option value="unmap"><?php echo $text_unmap; ?></option>
            <option value="delete"><?php echo $button_delete; ?></option>
        </select>&nbsp;<a style="vertical-align: middle; margin-top: -1px;" id="perform_action" class="btn_standard"><?php echo $button_go_actions ?></a>
    </div>
    <?php endif; ?>
    <div id="pagination"></div>
</div>

<div id="edit_frm" style="display:none" >
    <form method="post" action="">
    <div class="resource_image"></div>
    <table class="files resource-details" cellpadding="0" cellspacing="0">
        <tr>
            <td colspan="2" class="sub_title"><?php echo $text_edit_resource ?></td>
        </tr><tr>
            <td></td>
            <td class="message"></td>
        </tr>
        <tr>
            <td>
                <?php echo $text_language; ?>
            </td>
            <td>
                <?php echo $language; ?>
            </td>
        </tr>
        <tr>
            <td><?php echo $text_resource_code; ?></td>
            <td><textarea name="resource_code"></textarea><span class="required">*</span></td>
        </tr>
        <tr>
            <td><?php echo $text_name; ?></td>
            <td>
                <?php foreach ($languages as $lang_id => $lang_data) { ?>
                    <?php if ( $language_id == $lang_data['language_id'] ) { ?>
                        <input type="text" name="name[<?php echo $lang_data['language_id'] ?>]" value="" />
                    <?php } else {?>
                        <input type="text" name="name[<?php echo $lang_data['language_id'] ?>]" value="" style="display:none" />
                    <?php } ?>
                <?php } ?>
                <span class="required">*</span></td>
        </tr>
        <tr>
            <td><?php echo $text_title; ?></td>
            <td>
                <?php foreach ($languages as $lang_id => $lang_data) { ?>
                    <?php if ( $language_id == $lang_data['language_id'] ) { ?>
                        <input type="text" name="title[<?php echo $lang_data['language_id'] ?>]" value="" />
                    <?php } else {?>
                        <input type="text" name="title[<?php echo $lang_data['language_id'] ?>]" value="" style="display:none" />
                    <?php } ?>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td><?php echo $text_description; ?></td>
            <td>
                <?php foreach ($languages as $lang_id => $lang_data) { ?>
                    <?php if ( $language_id == $lang_data['language_id'] ) { ?>
                        <textarea name="description[<?php echo $lang_data['language_id'] ?>]"></textarea>
                    <?php } else { ?>
                        <textarea name="description[<?php echo $lang_data['language_id'] ?>]" style="display:none"></textarea>
                    <?php } ?>
                <?php } ?>
                </td>
        </tr>
        <tr>
            <td>
            </td>
            <td class="save"><button style="float: right;" type="submit"><img src="<?php echo $template_dir?>image/icons/icon_grid_save.png" alt="<?php echo $button_save; ?>" border="0" /><?php echo $button_save; ?></button></td>
        </tr>
        <tr class="border">
            <td><?php echo $text_mapped_to; ?></td>
            <td class="mapped"></td>
        </tr>
        <tr>
            <td><?php echo $text_map; ?></td>
            <td>
        	<?php if($mode != 'url'){ ?>
            	<a class="btn_action resource_unmap" id="map_this"><span class="icon_s_save">&nbsp;<span class="btn_text"><?php echo $button_select_resource; ?></span></span></a>            
			<?php } else { ?>
            	<a class="btn_action resource_unmaps use" rel="0"><span class="icon_s_save">&nbsp;<span class="btn_text"><?php echo $button_select_resource; ?></span></span></a>            
			<?php } ?>
            </td>
        </tr>		
    </table>
    </form>
</div>

<div id="confirm_dialog" title="<?php echo $confirm_title ?>">
  <?php echo $text_confirm ?>
</div>

<div id="resource_details">
    <div class="resource_image"></div>
    <table class="files resource-details" width="510" cellpadding="0" cellspacing="0">
        <tr>
            <td colspan="2" align="right"><a class="close">X</a></td>
        </tr>
        <tr>
            <td colspan="2" class="sub_title"><?php echo $text_resource_details; ?></td>
        </tr>
        <tr>
            <td width="130"><?php echo $text_name; ?></td>
            <td width="380" class="name"></td>
        </tr>
        <tr>
            <td><?php echo $text_description; ?></td>
            <td class="description"></td>
        </tr>
        <tr>
            <td><?php echo $text_mapped_to; ?></td>
            <td class="mapped"></td>
        </tr>
    </table>
</div>


<script type="text/javascript"><!--

function wordwrap( str, int_width, str_break, cut ) {
	var i, j, s, r = str.split("\n");
	if(int_width > 0) for(i in r){
		for(s = r[i], r[i] = ""; s.length > int_width;
			j = cut ? int_width : (j = s.substr(0, int_width).match(/\S*$/)).input.length - j[0].length || int_width,
			r[i] += s.substr(0, j) + ((s = s.substr(j)).length ? str_break : "")
		);
		r[i] += s;
	}
	return r.join("\n");
}

function cut_str( str, length ) {
    if ( str.length < length )
        return str;
    return str.substr(0,length)+'...';
}

function querySt(hu, ji) {
    gy = hu.split("&");
    for (i=0;i<gy.length;i++) {
        ft = gy[i].split("=");
        if (ft[0] == ji) {
            return ft[1];
        }
    }
}

var rl_mode = '<?php echo $mode; ?>';
jQuery(function($){

    var type = '<?php echo $default_type; ?>',
        mode = '<?php echo $mode; ?>',
        types = [],
        object_title = '<?php echo addslashes($object_title); ?>',
        show_object_resource = true,
        page = 1,
        loadedItems,
        selectedItem;

<?php foreach ($types as $t) {
    echo 'types["'.$t['type_name'].'"] = {
        id: "'.$t['type_id'].'",
        name: "'.$t['type_name'].'",
        dir: "'.$t['default_directory'].'"
    };
    ';
} ?>

    var object_name = '<?php echo $object_name; ?>';
    var object_id = '<?php echo $object_id; ?>';
    var urls = {
        upload : '<?php echo $rl_add; ?>',
        resources : '<?php echo $rl_resources; ?>',
        del : '<?php echo $rl_delete; ?>',
        get_resource : '<?php echo $rl_get_resource; ?>',
        get_preview : '<?php echo $rl_get_preview; ?>',
        update_resource : '<?php echo $rl_update_resource; ?>',
        update_sort_order : '<?php echo $rl_update_sort_order; ?>',
        map : '<?php echo $rl_map; ?>',
        unmap : '<?php echo $rl_unmap; ?>',
        resource : '<?php echo HTTP_DIR_RESOURCE; ?>'
    }
    var errors = {
        error_no_type : '<?php echo $error_no_type; ?>',
        error_required_data: '<?php echo $error_required_data; ?>'
    };
    var text = {
        edit: '<?php echo $button_edit; ?>',
        del: '<?php echo $button_delete; ?>',
        button_select_resource: '<?php echo $button_select_resource; ?>',
        map: '<?php echo $text_map; ?>',
        unmap: '<?php echo $text_unmap; ?>',
        text_success: '<?php echo $text_success; ?>',
        text_no_resources: '<?php echo $text_no_resources; ?>',
        text_none: '<?php echo $text_none; ?>',
        text_cant_delete_title: '<?php echo $text_cant_delete_title; ?>',
        text_cant_delete: '<?php echo $text_cant_delete; ?>',
        button_select: '<?php echo $button_select; ?>',
        view_title: '<?php echo $view_title; ?>',
        text_preview: '<?php echo $text_preview; ?>'
    };

    $("#column_right_wrapper .tabs a").click( function(){
        $("#column_right_wrapper .tabs a").removeClass('selected');
        $(this).addClass('selected');
        if ( $(this).attr('id') == 'library' ) {
            show_object_resource = false;
        } else {
            show_object_resource = true;
        }
	    page=1;
        loadResources();
        return false;
    });

    $("#confirm_dialog").dialog({
        draggable: false,
        resizable: false,
        autoOpen: false,
        modal: true
    });

    $('#column_right').ajaxError(function(e, jqXHR, settings, exception){
        hideLoading();
        $(this).html('<div class="error" align="center"><b>'+exception+'</b></div>');
    });

    $('#pagination a').live('click', function(){
        page = querySt($(this).attr('href'), 'page');
        loadResources();
        return false;
    });

    function showLoading(type) {
        if ( type == 'small') {
            $( "#column_right_wrapper" ).prepend( '<div class="rl_loading"></div>' );
        } else
            $( "#column_right" ).html('').addClass('loading_row');
    }
    function hideLoading(type) {
        if ( type == 'small') {
            $( "#column_right_wrapper .rl_loading" ).remove();
        } else
            $( "#column_right" ).removeClass('loading_row');
    }

    $('table.resource-details select[name="language_id"]').live('change',function(){
        var language_id = $(this).val();
	    $(this).prev().html( $(this).find("option:selected").text() );

        var form  = $(this).closest('form');

        $('input[name^="name"]', form).hide();
        $('input[name^="title"]', form).hide();
        $('textarea[name^="description"]', form).hide();

        $('input[name="name['+language_id+']"]', form).show();
        $('input[name="title['+language_id+']"]', form).show();
        $('textarea[name="description['+language_id+']"]', form).show();
    });

    function loadEditForm(json) {

        hideLoading();

        var form = $('#edit_frm form').clone();
        $(form).attr('action', urls.update_resource+'&resource_id='+json.resource_id);
        $('select[name="language_id"]', form).val( $('#language_id').val() );

        $.each(json.name, function(index, item){
            form.find('input[name="name['+index+']"]').val(item);
        });
        $.each(json.title, function(index, item){
            form.find('input[name="title['+index+']"]').val(item);
        });
        $.each(json.description, function(index, item){
            form.find('textarea[name="description['+index+']"]').val(item);
        });

        if (json.resource_code) {
            $('textarea[name="resource_code"]', form).val(json.resource_code);
            $('textarea[name="resource_code"]', form).parent().parent().show();
        } else {
            $('textarea[name="resource_code"]', form).parent().parent().hide();
        }

        var src = '<img src="' + json.thumbnail_url + '" title="' + json.name + '" />';
        if ( type == 'image' && json.resource_code  ) {
            src = json.thumbnail_url;
        }
        $('div.resource_image', form).html(src + '<a target="_preview" href="'+urls.get_preview+'&resource_id='+json.resource_id+'&language_id='+json.language_id+'">'+text.text_preview+'</a>');

        if ( !json.resource_objects ) {
            $('td.mapped', form).html(text.text_none);
        } else {
            var html = '';
            $.each(json.resource_objects, function(index, items){
                if ( items.length ) {
                    html += '<b>'+index+'</b><br/>';
                    $.each(items, function(index, item){
                        html += '<a target="_resource_object" href="'+item['url']+'">'+item['name']+'</a><br/>';
                    });
                    html += '<br/>';
                }
            });
	        html = html=='' ? text.text_none : html;
            $('td.mapped', form).html(html);
        }

        $('#column_right').html(form);
	    
	    if(mode !='url' ){
			$('#map_this').attr('value', json.resource_id );
			$('#map_this').click(function(){
				this.checked=false;
				$.ajax({
					url: urls.map,
					type: 'POST',
					data: {'resources[]':$(this).attr('value')},
					dataType: 'json',
							success: function(json) {
									parent.$('#dialog').dialog('close');
									parent.$('#dialog').remove();
							}
				});
			});
	    }
        $('#multiactions').hide();
        $('#pagination').hide();

    }


    function loadResources() {
        showLoading();
        $('#multiactions').show();
        $('#pagination').show();
        $('#resource_details a.close').click();

        var keyword = $('#searchform input[name="search"]').val();
        if( keyword == '<?php echo $text_search; ?>') {
            keyword = '';
        }

        var resource_data = {
            type : type,
            language_id : $('#language_id').val(),
            keyword : keyword,
            page : page
        }
        if ( show_object_resource ) {
            resource_data.object_name = object_name;
            resource_data.object_id = object_id;
        }

        $.ajax({
            url: urls.resources,
            type: 'GET',
            data: resource_data,
            dataType: 'json',
            success: function(json) {
                hideLoading();
                $('#column_right').html('');
                var html = '';
                loadedItems = json.items;

                if ( !json.items.length ) {
                    html = '<div class="no_resource">'+text.text_no_resources+'<div><a class="btn_standard add_resource"><?php echo $button_add_resource; ?></a></div></div>';
                    $('#column_right').html(html);
                    return;
                }

                $(json.items).each(function(index, item){
                    var src = '<img src="' + item['thumbnail_url'] + '" title="' + item['name'] + '" />';
                    if ( type == 'image' && item['resource_code']  ) {
                        src = item['thumbnail_url'];
                    }
                    html += '<div id="resource' + item['resource_id'] + '" class="resource'+(item['object_name']==object_name && item['object_id']==object_id ? ' select' : '')+'" >\
                    <a class="select" href="' + urls.get_resource + '&resource_id=' + item['resource_id'] + '&language_id=' + item['language_id'] + '">' + src + '\</a>\
                    '+( show_object_resource && mode != 'url' ? '<div class="sort_order" title="sort order"><input type="text" name="sort_order[' + item['resource_id'] + ']" value="' + (item['sort_order'] ? item['sort_order'] : '') + '" rel="'+index+'"  /></div>' : '')+'\
                    '+( mode != 'url' ? '<div class="checkbox"><input type="checkbox" name="resources[]" value="' + item['resource_id'] + '" rel="'+index+'"  /></div>' : '')+'\
                    <br />\
                    <b class="name">' + cut_str(wordwrap(item['name'], 14, '<br/>', true), 32) + '</b>\
                    <div class="buttons">\
                    <a class="edit" rel="' + item['resource_id'] + '" href="' + urls.get_resource + '&resource_id=' + item['resource_id'] + '"><img src="<?php echo $template_dir?>image/icons/icon_s_edit.png" alt="' + text.edit + '" border="0" /></a>\
                    '+( item['mapped'] > 0 ? '' : '<a class="delete" rel="resource' + item['resource_id'] + '" href="' + urls.del + '&resource_id=' + item['resource_id'] + '"><img src="<?php echo $template_dir?>image/icons/icon_s_delete.png" alt="' + text.del + '" border="0" /></a>')+'\
                    '+( mode == 'url' ? '<a class="use" rel="'+index+'" >'+text.button_select_resource+'</a>' : '' )+'\
                    </div>\
                    </div>';
                });
                html += '<div class="clr_both" style="height:30px"></div>';
                $('#column_right').html(html);
                $('#pagination').html(json.pagination);
            }
        });
    }

    $('#searchform').submit( function(){
        page = 1;
        loadResources();
    });
    $('#searchform a.btn_search').click( function(){
        page = 1;
        loadResources();
        return false;
    });
    $('#searchform input').keypress(function(e){
        if(e.which == 13){
            e.preventDefault();
            page = 1;
            loadResources();
        }
    });
    $('#language_id').change(function(){
        page = 1;
	    $('#language_id').prev().html( $(this).find("option:selected").text() );
        loadResources();
        return false;
    });

    $('#column_right input[name^=sort_order]').live('keyup', function(){
        $('#button_save_order').show();
    });

    $('#button_save_order').click(function(){
        $.ajax({
            url: urls.update_sort_order+'&object_name='+object_name+'&object_id='+object_id,
            type: 'POST',
            data: $('input[name^=sort_order]').serializeArray(),
            dataType: 'json',
            success: function(json) {
                loadResources();
            }
        });
        $(this).hide();
        return false;
    });

    $('#column_right a.use').live('click', function(){
        var item = loadedItems[$(this).attr('rel')];

        if(window.opener){
            placeInCKE(item);
            window.self.close();
            return;
        }

        parent.selectResource = item;

        if ( item['resource_code']  ) {
            parent.$('#'+parent.selectField).html(item['resource_code']);
            parent.$('input[name="'+parent.selectField+'"]').val(item['resource_code']);
        } else {
	        parent.loadSingle(type, parent.wrapper_id, item['resource_id'],parent.selectField);
            parent.$('input[name="'+parent.selectField+'"]').val(types[type].dir+item['resource_path']);
        }

        parent.$('#dialog').dialog('close');
		parent.$('#dialog').remove();
    });

    $('#column_right a.delete').live('click', function(){

        var that = this;

        $("#confirm_dialog").dialog('option', 'buttons', {
            "<?php echo $button_delete ?>" : function() {
                $.ajax({
                    url: $(that).attr('href'),
                    type: 'POST',
                    data: { type : type },
                    dataType: 'json',
                    success: function(json) {
                        if (json) {
                            $('#'+$(that).attr('rel')).remove();
                        }
                    }
                });
                $(this).dialog("close");
                $('#resource_details a.close').click();
            },
            "<?php echo $button_cancel ?>" : function() {
                $(this).dialog("close");
            }
        });
        $("#confirm_dialog").dialog('open');

        return false;
    });



    $('#column_right a.edit').live('click', function(){
        showLoading();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            data: {resource_objects: 1, object_name: object_name},
            dataType: 'json',
            success: loadEditForm
        });

        $('#resource_details a.close').click();
        return false;
    });

    $('#perform_action').click(function(){
        var url = '';
        switch( $('#multiactions select').val() ){
            case 'map':
                $.ajax({
                    url: urls.map,
                    type: 'POST',
                    data: $('input[name^=resources]').serializeArray(),
                    dataType: 'json',
                    success: function(json) {
                        $('#object').click();
                    }
                });
                break;
            case 'unmap':
                $.ajax({
                    url: urls.unmap,
                    type: 'POST',
                    data: $('input[name^=resources]').serializeArray(),
                    dataType: 'json',
                    success: function(json) {
                        loadResources();
                    }
                });
                break;
            case 'delete':
                var cant_delete = false;
                $('input[name^=resources]:checked').each(function(index, item){
                    if ( !$('#resource'+$(item).val()+' a.delete').length )
                        cant_delete = true;
                });

                if ( cant_delete ) {
                    $('<div title="'+text.text_cant_delete_title+'">'+text.text_cant_delete+'</div>').dialog(
                        {
                            buttons: {
                                "<?php echo $button_close ?>" : function() {
                                    $(this).dialog("close");
                                }
                            }
                        }
                    );
                } else {
                    $("#confirm_dialog").dialog('option', 'buttons', {
                        "<?php echo $button_delete ?>" : function() {
                            $.ajax({
                                url: urls.del,
                                type: 'POST',
                                data: $('input[name^=resources]').serializeArray(),
                                dataType: 'json',
                                success: function(json) {
                                    loadResources();
                                }
                            });
                            $(this).dialog("close");
                        },
                        "<?php echo $button_cancel ?>" : function() {
                            $(this).dialog("close");
                        }
                    });
                    $("#confirm_dialog").dialog('open');
                }
                break;
            default:
                return;
        }

    });

    $('td.save button').live('click', function(){
        var form  = $(this).closest('form');
        form.find(".message").html('').removeClass('error').removeClass('success');

        var error_required_data = false;
        var required_lang_id = null;
		var code = form.find('textarea[name="resource_code"]:visible');
        if ( code.length && !$(code).val() ) {
			error_required_data = true;
		}
		form.find('input[name^="name"]').each(function(index, item){
            if ( !$(item).val() ) {
                error_required_data = true;
                required_lang_id = $(item).attr('name').slice(5,-1);
            }
		});
		if ( error_required_data ) {
			if (required_lang_id) {
                form.find('select')
                    .val(required_lang_id)
                    .change();
            }
            form.find(".message").html( errors.error_required_data + ' - ' + form.find('option:selected').text() ).addClass('error');

			return false;
		}


        showLoading('small');
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serializeArray(),
            dataType: 'json',
            success: function(json) {
                $(form).find('.message').addClass('success').html( text.text_success );
                hideLoading('small');
            }
        });
        return false;
    });

    $('#types a').click(function(){
        type = $(this).attr('href');
        $('#column_left a').removeClass('selected');
        $(this).addClass('selected');

        //clear error msg
        $('#add_resource_msg').html('');

        //load resources
        loadResources();

        return false;
    });

    $('a.add_resource').live('click', function(){
        if (typeof type == 'undefined') {
            $('#add_resource_msg').html( errors.error_no_type );
            return false;
        }

        $('#multiactions').hide();
        $('#pagination').hide();
        $('#resource_details a.close').click();

        var url = urls.upload + '&type=' + type;
        if ( show_object_resource ) {
            url += '&object_name='+object_name+'&object_id='+object_id;
        }
	    $('#column_right').html('<iframe src="' + url + '" style="padding:0; margin: 10px auto; display: block; width: 648px; height: 370px;" frameborder="no" scrolling="auto"></iframe>');
        $('#column_right iframe').load(function(e){
            try {
                var error_data = $.parseJSON( $(this).contents().find('body').html() );
            } catch(e){
                var error_data = null;
            }
            if ( error_data && error_data.error_code ) {
                parent.$('#dialog').dialog('close');
                parent.httpError(error_data);
            }
        });
    });

    $('#done_resource').click(function(){
<?php if ( $mode == 'url' ) : ?>
        var item = null;

        if ( $('#resource_details').is(':visible') ) {
            item = selectedItem;
        } else if ( $('input[name^=resources]:checked').length ) {
            item = loadedItems[$('input[name^=resources]:checked').first().attr('rel')];
        }

        if ( item ) {
            if(window.opener){
				placeInCKE(item);
				window.self.close();
		        return;
	        }
            parent.selectResource = item;
            if ( item['resource_code']  ) {
                parent.$('#'+parent.selectField).html(item['resource_code']);
                parent.$('input[name="'+parent.selectField+'"]').val(item['resource_code']);
            } else {
                parent.$('#'+parent.selectField).html('<img src="' + item['thumbnail_url'] + '" title="' + item['name'] + '" />');
                parent.$('input[name="'+parent.selectField+'"]').val(types[type].dir+item['resource_path']);
            }
        }

<?php endif; ?>
        parent.$('#dialog').dialog('close');
		parent.$('#dialog').remove();

    });

	function placeInCKE(item){
		if(window.opener.CKEDITOR){
			var dialog = window.opener.CKEDITOR.dialog.getCurrent();
                dialog.getContentElement( 'info','txtUrl').setValue( item.thumbnail_url );
		}
	}

    $('#column_right a.select').live('click', function(){
        showLoading('small');
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            data: {resource_objects: 1, object_name: object_name},
            dataType: 'json',
            success: function(json) {
                hideLoading('small');
                selectedItem = json;
                var src = '<img src="' + json.thumbnail_url + '" title="' + json.name + '" />';
                if ( type == 'image' && json.resource_code  ) {
                    src = json.thumbnail_url;
                }
                $('#resource_details div.resource_image').html(src + '<a target="_preview" href="'+urls.get_preview+'&resource_id='+json.resource_id+'&language_id='+json.language_id+'">'+text.text_preview+'</a>');
                $('#resource_details td.name').html(json.name);
                $('#resource_details td.description').html(json.description);

                $('#resource_details').show();

                if ( !json.resource_objects ) {
                    $('#resource_details td.mapped').html(text.text_none);
                } else {
                    var html = '';
                    $.each(json.resource_objects, function(index, items){
                        if ( items.length ) {
                            html += '<b>'+index+'</b><br/>';
                            $.each(items, function(index, item){
                                html += '<a target="_resource_object" href="'+item['url']+'">'+item['name']+'</a><br/>';
                            });
                            html += '<br/>';
                        }
                    });
                    $('#resource_details td.mapped').html(html);
                }
            }
        });

        return false;
    });

    $('#resource_details a.close').live('click', function(){
        $('#resource_details').hide();
        return false;
    });

    <?php if ($add) : ?>
    $('#add_resource').click();
    <?php elseif ($update) : ?>
        showLoading();
        $.ajax({
            url: urls.get_resource,
            type: 'GET',
            data: {resource_objects: 1, object_name: object_name, resource_id: '<?php echo $resource_id ?>'},
            dataType: 'json',
            success: loadEditForm
        });
    <?php else : ?>
    loadResources();
    <?php endif; ?>
});

$("#language_id").ready( function(){
	$("#language_id").parents('.afield').width($('.search_box').width()) ;
});
$("#language_id").change( function(){
	$("#language_id").parents('.afield').width($('.search_box').width()) ;
});

var $error_dialog = null;
httpError = function(data){
    if ( $error_dialog )
        return;

    $error_dialog = $('<div></div>')
        .html(data.error_text)
        .dialog({
            title: data.error_title,
            modal: true,
            resizable: false,
            buttons: {
                    "Close" : function() { $(this).dialog("close"); }
            },
            close: function(e, ui) {
                switch ( data.error_code ){
                    //app error
                    case 400 :
                        break;
                    //error login
                    case 401 :
                        parent.window.location.reload();
                        break;
                    //error permission
                    case 402 :
                        break;
                    //error not found
                    case 404 :
                        break;
                }
            }
        });
}

jQuery(function($){
	$('<div/>').ajaxError(function(e, jqXHR, settings, exception){
		var error_data = $.parseJSON(jqXHR.responseText);
		httpError(error_data);
	});
});

//--></script>
</body>
</html>