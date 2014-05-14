<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>"/>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/fileupload/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/fileupload/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/fileupload/jquery.fileupload-ui.js"></script>

<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css" id="theme">
<link rel="stylesheet" type="text/css"href="<?php echo $template_dir; ?>javascript/jquery/fileupload/jquery.fileupload-ui.css"/>
<link rel="stylesheet" type="text/css"href="<?php echo $template_dir; ?>stylesheet/stylesheet.css"/>
<link rel="stylesheet" type="text/css"href="<?php echo $template_dir; ?>stylesheet/resource_add.css"/>
</head>
<body>


<div id="tabs">
	<ul>
		<li><a href="#fileupload"><?php echo $text_add_file; ?></a></li>
		<li><a href="#code"><?php echo $text_add_code; ?></a></li>
	</ul>

    <div id="fileupload">
        <?php if ($attention) { ?>
        <div class="attention"><?php echo $attention; ?></div>
        <?php } ?>
	    <div class="fileupload-content">
            <table class="files" width="100%" cellpadding="0" cellspacing="0"></table>
            <div class="fileupload-progressbar"></div>
        </div>
        <form action="<?php echo $rl_upload; ?>" method="POST" enctype="multipart/form-data">
            <div class="fileupload-buttonbar">
                <label class="fileinput-button">
                    <span><?php echo $text_upload_files; ?></span>
                    <input type="file" name="files[]" multiple>
                </label>
	            <?php echo $text_drag; ?>
            </div>
        </form>
    </div>

    <div id="code">

        <form method="post" action="<?php echo $rl_add_code; ?>" >
        <table class="files resource-details" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="2" class="sub_title"><?php echo $text_add_code; ?></td>
            </tr>
	        <tr>
                <td></td>
                <td class="message"></td>
            </tr>
            <tr>
		        <td>
                    <?php echo $text_language; ?>
                </td>
		        <td>
                    <select name="language_id">
                    <?php foreach ($languages as $lang_id => $lang_data) { ?>
                        <option <?php echo ( $language_id == $lang_data['language_id'] ? 'selected="selected"' : '' ) ?> value="<?php echo $lang_data['language_id'] ?>">
                            <?php echo $lang_data['name']; ?>
                        </option>
                    <?php } ?>
                    </select>
                </td>
	        </tr>
            <tr>
		        <td><?php echo $text_resource_code; ?><span class="required">*</span></td>
		        <td><textarea name="resource_code"></textarea></td>
	        </tr>
            <tr>
		        <td><?php echo $text_name; ?><span class="required">*</span></td>
		        <td>
                    <input type="text" name="name" value="" /><input id="resource_id" type="hidden" name="resource_id" value="" />
                </td>
	        </tr>
	        <tr>
		        <td><?php echo $text_title; ?></td>
		        <td>
                    <input type="text" name="title" value="" />
		        </td>
	        </tr>
	        <tr>
		        <td><?php echo $text_description; ?></td>
		        <td>
                    <textarea name="description"></textarea>
                </td>
	        </tr>
            <tr>
		        <td></td>
		        <td class="save">
                    <button type="submit" style="float: right;">
                        <img src="<?php echo $template_dir?>image/icons/icon_grid_save.png" alt="<?php echo $button_save; ?>" border="0" /><?php echo $button_save; ?>
                    </button>
                    <div class="flt_right close" style="display: none;">
                        <a href="#"><img src="<?php echo $template_dir?>image/asc.png" alt="" border="0" /></a>
                    </div>
                </td>
	        </tr>
        </table>
        </form>

    </div>

</div>

<script id="template-upload" type="text/x-jquery-tmpl">
    <tr class="template-upload{{if error}} ui-state-error{{/if}}">
	    <td>
        <table width="100%" cellpadding="0" cellspacing="0">
	        <tr>
		        <td class="name"><div>${name}</div></td>
				<td class="size">${sizef}</td>
				{{if error}}
					<td class="error-fileupload" colspan="2"><?php echo $text_error; ?>:
						{{if error === 'maxFileSize'}}<?php echo $error_maxFileSize; ?>
						{{else error === 'minFileSize'}}<?php echo $error_minFileSize; ?>
						{{else error === 'acceptFileTypes'}}<?php echo $error_acceptFileTypes; ?>
						{{else error === 'maxNumberOfFiles'}}<?php echo $error_maxNumberOfFiles; ?>
						{{else}}${error}
						{{/if}}
					</td>
		            <td></td>
				{{else}}
					<td class="progress"><div></div></td>
					<td class="start"><button><?php echo $text_start; ?></button></td>
				{{/if}}
				<td class="cancel"><button><?php echo $text_cancel; ?></button></td>
	        </tr>
        </table>
	    </td>
    </tr>
</script>
<script id="template-download" type="text/x-jquery-tmpl">
    <tr class="template-download{{if error}} ui-state-error{{/if}}" id="template-download${resource_id}">
        <td>
        <table width="100%" cellpadding="0" cellspacing="0">
	        <tr>
            {{if error}}
                <td class="name">${name}</td>
                <td class="size">${sizef}</td>
                <td class="error-fileupload" colspan="2"><?php echo $text_error; ?>:
                    {{if error === 1}}<?php echo $error_1; ?>
                    {{else error === 2}}<?php echo $error_2; ?>
                    {{else error === 3}}<?php echo $error_3; ?>
                    {{else error === 4}}<?php echo $error_4; ?>
                    {{else error === 5}}<?php echo $error_5; ?>
                    {{else error === 6}}<?php echo $error_6; ?>
                    {{else error === 7}}<?php echo $error_7; ?>
                    {{else error === 'maxFileSize'}}<?php echo $error_maxFileSize; ?>
                    {{else error === 'minFileSize'}}<?php echo $error_minFileSize; ?>
                    {{else error === 'acceptFileTypes'}}<?php echo $error_acceptFileTypes; ?>
                    {{else error === 'maxNumberOfFiles'}}<?php echo $error_maxNumberOfFiles; ?>
                    {{else error === 'uploadedBytes'}}<?php echo $error_uploadedBytes; ?>
                    {{else error === 'emptyResult'}}<?php echo $error_emptyResult; ?>
                    {{else}}${error}
                    {{/if}}
                </td>
            {{else}}
                <td class="preview">
                    {{if thumbnail_url}}<img src="${thumbnail_url}">{{/if}}
                </td>
                <td class="name" width="100%">${name}</td>
                <td class="size">${sizef}</td>
                <td class="edit"><button><img src="<?php echo $template_dir?>image/desc.png" alt="<?php echo $button_edit; ?>" border="0" /></button></td>
            {{/if}}
            </tr>
        </table>
        {{if error }}{{else}}
        <form method="post" action="${resource_detail_url}" style="display:none" id="update_resource${resource_id}">
        <table width="100%" cellpadding="0" cellspacing="0" class="resource-details" >
            <tr>
                <td></td>
                <td class="message"></td>
            </tr>
            <tr>
		        <td>
                    <?php echo $text_language; ?>
                </td>
		        <td>
                    <select name="language_id">
                    <?php foreach ($languages as $lang_id => $lang_data) { ?>
                        <option <?php echo ( $language_id == $lang_data['language_id'] ? 'selected="selected"' : '' ) ?> value="<?php echo $lang_data['language_id'] ?>">
                            <?php echo $lang_data['name']; ?>
                        </option>
                    <?php } ?>
                    </select>
                </td>
	        </tr>
            <tr>
		        <td><?php echo $text_name; ?><span class="required">*</span></td>
		        <td>
                    <input type="text" name="name" value="${name}" />
			        <input id="resource_id" type="hidden" name="resource_id" value="${resource_id}" />
                </td>
	        </tr>
	        <tr>
		        <td><?php echo $text_title; ?></td>
		        <td>
                   <input type="text" name="title" value="${title}" />
		        </td>
	        </tr>
	        <tr>
		        <td><?php echo $text_description; ?></td>
		        <td>
                    <textarea name="description"></textarea>
                </td>
	        </tr>
            <tr>
		        <td></td>
		        <td class="save">
			        <div class="flt_right close">
                        <a href="#"><img src="<?php echo $template_dir?>image/asc.png" alt="" border="0" /></a>
                    </div>
                    <button type="submit" style="float: right;">
                        <img src="<?php echo $template_dir?>image/icons/icon_grid_save.png" alt="<?php echo $button_save; ?>" border="0" /><?php echo $button_save; ?>
                    </button>
                </td>
	        </tr>
        </table>
        </form>
        {{/if}}
        </td>
    </tr>
</script>

<script type="text/javascript">
jQuery(function($){

    var type = '<?php echo $type; ?>';

    var errors = {
        error_required_data: '<?php echo $error_required_data; ?>'
    };

    var text = {
        text_success: '<?php echo $text_success; ?>'
    };

    $( "#tabs" ).tabs();

    $('td.edit button').live('click', function(){
        $(this).closest('table').next().toggle();
        return false;
    });

    $('div.close a').live('click', function(){
        $(this).closest('form').toggle();
        return false;
    });

    $('select[name="language_id"]').live('change',function(){
        var language_id = $(this).val();
        var form  = $(this).closest('form');
	    var resource_id = form.find('input[name="resource_id"]').val();
	    if(resource_id){
		    $.ajax({
			    url: '<?php echo $rl_get_info;?>',
			    type: 'GET',
			    data: {'language_id': language_id, 'resource_id': resource_id},
			    dataType: 'json',
			    success: function(json) {
				    if ( json.error ) {
					    form.find(".message").html( json.error ).addClass('error');
					    return;
				    }

				    form.find('input[name="name"]').val(json.name);
				    form.find('input[name="title"]').val(json.title);
				    form.find('textarea[name="description"]').val(json.description);
			    }
		    });
	    }
    });

    $('td.save button').live('click', function(){
        var form  = $(this).closest('form');
        form.find(".message").html('').removeClass('error').removeClass('success');

        var error_required_data = false;
        var required_lang_id = null;
		var code = form.find('textarea[name="resource_code"]');
        if ( code.length && !$(code).val() ) {
			error_required_data = true;
		}
		if(!form.find('input[name="name"]').val() ) {
                error_required_data = true;
        }

		if ( error_required_data ) {
            form.find(".message").html( errors.error_required_data + ' - ' + form.find('option:selected').text() ).addClass('error');
			$('body').parent('iframe').scrollTop(0);
			return false;
		}

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serializeArray(),
            dataType: 'json',
            success: function(json) {
				if ( json.error ) {
					form.find(".message").html( json.error ).addClass('error');
					return;  
				}

                if ( json.add_code ) {
                    var edit_frm = form.clone();
                    $('td.sub_title', edit_frm).parent().remove();
                    $(edit_frm).attr('action', json.resource_detail_url);

                    var src = '<img src="' + json.thumbnail_url + '" title="' + json.name + '" />';
                    if ( type == 'image' && json.resource_code  ) {
                        src = json.thumbnail_url;
                    }

                    var tbl = $('<table class="files" width="100%" cellpadding="0" cellspacing="0">\
                        <tr>\
                            <td class="preview" >'+src+'</td>\
                            <td class="name" width="100%">'+json.name+'</td>\
                            <td class="name" width="100%">'+json.name+'</td>\
		                    <td class="edit"><button><img src="<?php echo $template_dir?>image/desc.png" alt="<?php echo $button_edit; ?>" border="0" /></button></td>\
                        </tr>\
                    </table>');

                    tbl.insertBefore(form);
                    edit_frm.insertBefore(form).hide();
                    edit_frm.find('div.close').show();

                    edit_frm.find('textarea[name="resource_code"]').val(json.resource_code);
                    edit_frm.find('input[name="name"]').val(json.name);
                    edit_frm.find('input[name="title"]').val(json.title);
                    edit_frm.find('textarea[name="description"]').val(json.description);
                    edit_frm.find('input[name="resource_id"]').val(json.resource_id);

                    form.find("select, input, textarea").val('');
                } else {
                    form.find('.message').addClass('success').html( text.text_success );
                }
            }
        });
        return false;
    });

    $('#fileupload').fileupload({
        autoUpload: true,
        singleFileUploads: true
    });
	
	$('#fileupload').bind('fileuploaddone', function (e, data) {

		if(parent.rl_mode!='url' || data['result'][0].error ){ return; }
		
		var item =	data['result'][0];
		var types = [];
		<?php
		    foreach ($types as $t) {
		    	echo 'types["'.$t['type_name'].'"] = {
		    						id: "'.$t['type_id'].'",
		    						name: "'.$t['type_name'].'",
		    						dir: "'.$t['default_directory'].'"};';
		    } 
		?>
		
		if(parent.window.opener){
		    if(parent.window.opener.CKEDITOR){ 
		    	var dialog = parent.window.opener.CKEDITOR.dialog.getCurrent();
		    		dialog.getContentElement( 'info','txtUrl').setValue( item.thumbnail_url );
		    }
		    parent.window.self.close();
		    return;
		}
		
		//Adding 
		parent2 = parent.parent;
		parent2.selectResource = item;
		parent2.$('#' + parent.parent.selectField).html('<img src="' + item['thumbnail_url'] + '" title="' + item['name'] + '" />');
		parent2.loadSingle(type, parent2.wrapper_id, item['resource_id'], parent2.selectField);
		//change hidden element and mark ad changed 
		parent2.$('input[name="'+ parent2.selectField + '"]').val(types[type].dir + item['resource_path']).addClass('afield changed');
		parent2.$('form').prop('changed', 'true');
		
		parent2.$('#dialog').dialog('close');
		parent2.$('#dialog').remove();
		
	});
    // Open download dialogs via iframes,
    // to prevent aborting current uploads:
    $('#fileupload .files a:not([target^=_blank])').live('click', function (e) {
        e.preventDefault();
        $('<iframe style="display:none;"></iframe>')
            .prop('src', this.href)
            .appendTo('body');
    });

});
</script>
</body>
</html>