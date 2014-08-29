<?php
echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'rl_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax',
				'title' => $text_resource_library ));
?>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/fileupload/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/fileupload/jquery.fileupload-ui.js"></script>

<script type="text/javascript">
var urls = {
	resource_library:'<?php echo $rl_resource_library; ?>',
	resources:'<?php echo $rl_resources; ?>',
	resource_single:'<?php echo $rl_resource_single; ?>',
	map:'<?php echo $rl_map; ?>',
	unmap:'<?php echo $rl_unmap; ?>',
	del:'<?php echo $rl_delete; ?>',
	download: '<?php echo $rl_download; ?>',
	upload: '<?php echo $rl_upload; ?>',
	resource:'<?php echo HTTP_DIR_RESOURCE; ?>'
	},
	default_type = '<?php echo $default_type["type_name"]; ?>';	

/*
	Main resource library modal
*/
var mediaDialog = function (type, action, id, field, wrapper_id) {
    window.selectField = field;
    window.wrapper_id = wrapper_id;

	//reset content of modal
    $('#rl_modal .modal-body').html('');
    var src = urls.resource_library+'&action='+action+'&type='+type;
    if (id) {
        src += '&resource_id=' + id;
    }
	
	reloadModal(src);
};

var reloadModal = function (URL) {
	//main ajax call to load rl content
    $.ajax({
        url:URL,
        type:'GET',
        dataType:'html',
        success:function (html) {
        	var $md = $('#rl_modal');
        	var mdb = '#rl_modal .modal-body';
        	//remove all open tooltips bootstrap work around
        	$('.tooltip.in').remove();
			$(mdb).html('');
		    $(mdb).html(html);
			//if modal is not yet open, open and initilize close event
			if ($("#rl_modal").length>0 || !$("#rl_modal").data('bs.modal').isShown) {
			    $(mdb).css({height:'560'});
				$md.modal('show');
				$md.unbind('hidden.bs.modal').on('hidden.bs.modal', function () {
			        //reload original media list to show new selections
	    		    //not for URL mode
	    		    $(mdb).html('');
	        		<?php if($mode != 'url') { ?>
	        		<?php 	foreach ($types as $type) { ?>
	            		loadMedia('<?php echo $type['type_name']?>');
	        		<?php 	} ?>
	        		<?php } ?>
				});
			}
			//bind evend in the modal
			bindCustomEvents(mdb);
			bind_rl(mdb);
        },
        error:function (jqXHR, textStatus, errorThrown) {
			error_alert('<div class="error" align="center"><b>' + textStatus + '</b>  ' + errorThrown + '</div>');
        },
		complete: function() {
		}
    });	
}

var saveRL = function (URL, postdata) {
	var rid;
	//main ajax call to load rl content
    $.ajax({
        url:URL,
        data: postdata,
        type:'POST',
        dataType:'json',
		async: false,
        success:function (new_rl_id) {
			rid = new_rl_id;
        	success_alert('<?php echo $text_success; ?>',true, '.modal-content');
        },
        error:function (jqXHR, textStatus, errorThrown) {
			error_alert(
				'<div class="error" align="center"><b>' + textStatus + '</b>  ' + errorThrown + '</div>',
				false,
				'.modal-content'
			);
        }
    });
	return rid;
}

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
                var src = '<img class="img-responsive" src="' + item['thumbnail_url'] + '" title="' + item['name'] + '" />';
                if (type == 'image' && item['resource_code']) {
                    src = item['thumbnail_url'];
                }
                html += '<div class="col-xs-3 col-sm-3 col-md-2">';
                html += '<div class="thumbnail" id="image_row' + item['resource_id'] + '" >\
                <a class="btn resource_edit" data-type="' + type + '" data-rl-id="' + item['resource_id'] + '">' + src + '</a></div>';

				html += '<div class="caption center">';


			   html += '<a class="btn resource_edit tooltips" ' +
						  'data-type="' + type + '" ' +
						  'data-rl-id="' + item['resource_id'] + '" ' +
						  'data-original-title="<?php echo $button_edit ?>"><i class="fa fa-edit"></i></a>' +
						'<a class="btn resource_unmap tooltips" ' +
							'data-rl-id="' + item['resource_id'] + '" ' +
							'data-original-title="<?php echo $button_unmap; ?>" ' +
							'data-confirmation="delete" ' +
							'data-confirmation-text="<?php echo $text_confirm_unmap ?>" ' +
							'onclick="unmap_resource('+item['resource_id']+');"><i class="fa fa-unlink"></i></a>';



                html += '</div></div></div>';
            });
            
            html += '<div class="col-xs-3 col-sm-3 col-md-2"><div class="thumbnail">';
            html += '<a class="btn resource_add tooltips transparent" data-type="' + type + '" data-original-title="<?php echo $text_add_media ?>"><img src="<?php echo $template_dir . '/image/icons/icon_add_media.png'; ?>" alt="<?php echo $text_add_media; ?>" width="100" /></a>'; 
            html += '</div></div>';

			$('#type_' + type + ' div.type_blocks').html(html);
        },
        error:function (jqXHR, textStatus, errorThrown) {
            $('#type_' + type).show();
            $('#type_' + type + ' td.type_blocks').html('<div class="error" align="center"><b>' + textStatus + '</b>  ' + errorThrown + '</div>');
        },
		complete: function() {
			bindCustomEvents('#type_' + type);
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
                <a class="resource_edit" data-type="' + type + '" data-rl-id="' + item['resource_id'] + '">' + src + '</a><br />';
            if (item['resource_id']) {
                html += ( item['mapped'] > 1
						? '' : '<a class="btn_action resource_delete" data-rl-id="' + item['resource_id'] + '"><span class="icon_s_delete"><span class="btn_text"><?php echo $button_unlink ?></span></span></a>') + '\
					<a class="btn_action resource_edit" data-type="' + type + '" data-rl-id="' + item['resource_id'] + '"><span class="icon_s_edit"><span class="btn_text"><?php echo $button_edit ?></span></span></a>';
            }
            html += '</span>';
            $('#' + wrapper_id + '.type_blocks').html(html);
            // click events
            $('#' + wrapper_id + '_' + item['resource_id'] + ' a.resource_edit').unbind('click');
            $('#' + wrapper_id + '_' + item['resource_id'] + ' a.resource_edit').click(function () {
                var action = item['resource_id'] ? 'update' : 'add';
                mediaDialog($(this).attr('data-type'), action, item['resource_id'], field, wrapper_id);
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
                        $('form').attr('changed', 'true');
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

    $(document).on('click', 'a.resource_add', function () {
        mediaDialog($(this).attr('data-type'), 'add');
        return false;
    });

    $(document).on("click", 'a.resource_edit', function () {
        mediaDialog($(this).attr('data-type'), 'update', $(this).attr('data-rl-id'));
        return false;
    });

    $(document).on("click", '#resource', function () {
        mediaDialog($(this).attr('data-type'), 'update', $(this).attr('data-rl-id'));
        return false;
    });

    $(document).on("click", '#object', function () {
        mediaDialog($(this).attr('data-type'), 'list_object', $(this).attr('data-rl-id'));
        return false;
    });

    $(document).on("click", '#library', function () {
        mediaDialog($(this).attr('data-type'), 'list_library', $(this).attr('data-rl-id'));
        return false;
    });

    $(document).on("click", '.rl_pagination a', function () {
		if( !$(this).parent().hasClass('disabled') ){
        	reloadModal($(this).attr('href'));
		}
        return false;
    });

	$(document).on('click','button.rl_add_file',function(){
		$('#choose_resource_type').fadeOut("normal", function(){
			$('#add_form, #file_subform').fadeIn("normal");
		});
	});
	$(document).on('click','button.rl_add_code',function(){
		$('#choose_resource_type').fadeOut("normal", function(){
			$('#add_form, #code_subform, #add_resource_buttons').fadeIn("normal");
		});
	});
    
});

function unmap_resource ( rl_id ) {
	if (rl_id==null || rl_id == '') {
		return false;
	}
    $.ajax({
        url:urls.unmap + '&resource_id=' + rl_id + '&type=' + $('#library').attr( 'data-type'),
        type:'GET',
        dataType:'json',
		async: false,
        success:function (json) {
            if (json) {
                $('#image_row'+rl_id).parent().remove();
            }
			if($("#rl_modal").length>0 && $("#rl_modal").data('bs.modal').isShown){
				success_alert('Unlinked successfully', true, '.modal-content');
			}else{
            	success_alert('Unlinked successfully', true);
			}
        }
    });
    return false;
} 

function delete_resource ( rl_id ) {
	if (rl_id==null || rl_id == '') {
		return false;
	}
    $.ajax({
        url:urls.del + '&resource_id=' + rl_id,
        type:'GET',
        dataType:'json',
		async: false,
        success:function (json) {
            if (json) {
                $('#image_row'+rl_id).parent().remove();
            }
			if( $("#rl_modal").hasOwnProperty('data') && $("#rl_modal").data('bs.modal').isShown ){
					var type = $('#RlFrm input[name=type]').val();
					mediaDialog(type, 'list_library');
					success_alert('Deleted successfully', true);
			}else{
				success_alert('Deleted successfully', true, '.modal-content');
			}
        }
    });
    return false;
}

<?php } ?>

var bind_rl = function ( elm ) {
	var $obj;
	if (elm) {
		$obj = $(elm);
	} else {
		$obj = $(document).find('html');	
	}

	//bind form action if any
	bindAform($("input, checkbox, select, textarea", 'form'));

    $obj.find('.thmb').hover(function(){
      var t = $(this);
      t.find('.ckbox').show();
      t.find('.rl-group').show();
    }, function() {
      var t = $(this);
      if(!t.closest('.thmb').hasClass('checked')) {
        t.find('.ckbox').hide();
        t.find('.rl-group').hide();
      }
    });
    
    $obj.find('.ckbox').each(function(){
      var t = $(this);
      var parent = t.parent();
      if(t.find('input').is(':checked')) {
        t.show();
        parent.find('.rl-group').show();
        parent.addClass('checked');
      }
    });
    
    $obj.find('.thmb .ckbox').click(function(){
      var $t = $(this);
      $t.closest('.thmb').toggleClass('checked');
      enable_menu($obj, true);
      //togle checkbox
      $t.find('input:checkbox').attr('checked', function(idx, oldProp) {
      		enable_menu($obj, false);
            return !oldProp;
      });      
    });
    
    $obj.find('#rl_selectall').click(function(){
      if(this.checked) {
        $('.thmb').each(function(){
          $(this).find('input:checkbox').attr('checked',true);
          $(this).addClass('checked');
          $(this).find('.ckbox, .rl-group').show();
        });
        enable_menu($obj, true);
      } else {
        $('.thmb').each(function(){
          $(this).find('input:checkbox').attr('checked',false);
          $(this).removeClass('checked');
          $(this).find('.ckbox, .rl-group').hide();
        });
        enable_menu($obj, false);
      }
    });    

    $obj.find('.rl_link').click(function(){
		var tab = active_tab();
		var tab_id = tab.attr('id');
		var rl_id;
		if( tab_id == 'resource' ){
			rl_id = tab.attr('data-rl-id');
		}else{
			rl_id = $(this).attr('data-rl-id');
		}

		if(rl_id<1 || rl_id=='undefined'){ return false;}

		var type = $('#library').attr( 'data-type');

		$.ajax({
		        url:urls.map + '&resource_id=' + rl_id + '&type=' + type,
		        type:'GET',
		        dataType:'json',
			    async: false,
		        success:function (json) {
		            if (json) {
		                $('#image_row'+rl_id).parent().remove();
		            }
					if($("#rl_modal").length>0 && $("#rl_modal").data('bs.modal').isShown){
						success_alert('Linked successfully', true, '.modal-content');
					}else{
		            	success_alert('Linked successfully', true);
					}
		        }
		    });

		if(tab_id =='resource'){
			mediaDialog(type, 'update', rl_id);
		}else{
			tab.click();
		}
		return false;
	});

    $obj.find('.rl_link_multiple').click(function(){
		multi_action('map');
		return false;
	});

    $obj.find('.rl_unlink').click(function(){
		var rl_id = $(this).attr('data-rl-id');
		if(rl_id<1 || rl_id=='undefined'){ return false;}
		unmap_resource(rl_id);
		var tab = active_tab();

		if(tab.attr('id')=='resource'){
			mediaDialog( $(this).attr('data-type'), 'update', rl_id);
		}else{
			tab.click();
		}

		return false;
	});

	$obj.find('.rl_unlink_multiple').click(function(){
		multi_action('unmap');
		return false;
	});

    $obj.find('.rl_save').click(function(){
		//save rl details. 
		var datastring = $("#RlFrm").serialize();
		var type = $('#RlFrm input[name=type]').val();
		if( type == undefined ){
			type = $('#RlFrm #rl_types').val();
		}
	    var src = urls.resource_library+'&type='+type;
	    var rid = $('#RlFrm input[name=resource_id]').val();
    	if (rid) {
        	src += '&resource_id=' + rid+'&action=save';
    	}else{
			src += '&action=add';
		}
		var new_rid = saveRL(src, datastring);
		if(rid){

		}else{
			rid = new_rid;
		}
		mediaDialog(type, 'update', rid);	// поменять на релоад
		return false;
	});

	$obj.find('.rl_save_multiple').click(function(){

		var thmbs = $('div.thmb.checked');
		if(thmbs.length==0){ return false; }

		var postdata = thmbs.find( "input[name^='sort_order']" ).serialize();
		//save rl details.
		var type = $('#library').attr( 'data-type');
		var src = urls.resource_library+'&action=multisave'+'&type='+type;
		//main ajax call to load rl content
		$.ajax({
			url:src,
			data: postdata,
			type:'POST',
			dataType:'html',
			async: false,
			success:function (html) {
				success_alert('<?php echo $text_success; ?>',true, '.modal-content');
			},
			error:function (jqXHR, textStatus, errorThrown) {
				error_alert(
					'<div class="error" align="center"><b>' + textStatus + '</b>  ' + errorThrown + '</div>',
					false,
					'.modal-content'
				);
			}
		});
		$('#object').click(); // reload modal with object's resources
		return false;
	});

    $obj.find('.rl_reset').click(function(){
		//reset rl details. 
		var type = $('#RlFrm input[name=type]').val();
	    var rid = $('#RlFrm input[name=resource_id]').val();
        mediaDialog(type, 'update', rid);	// поменять на релоад
		return false;
	});

   	<?php
   	//NOTE! all events for deleting of resource see inside resource_library.tpl and resource_library_edit.tpl in attribute "onclick"
   	// It maden so because user must to confirm action by another js-click-event
   	?>

    $obj.find('.rl_download').click(function(){
		var rl_id = $(this).attr('data-rl-id');
		var url = urls.download + '&resource_id=' + rl_id;

		var hiddenIFrameID = 'hiddenDownloader',
			iframe = document.getElementById(hiddenIFrameID);
		if (iframe === null) {
			iframe = document.createElement('iframe');
			iframe.id = hiddenIFrameID;
			iframe.style.display = 'none';
			document.body.appendChild(iframe);
		}
		iframe.src = url;

		return false;
	});

    $obj.find('.rl_edit').click(function(){
		mediaDialog(type, 'update', $(this).attr('data-rl-id'));	// поменять на релоад
		return false;
	});
	
	$('#add_resource').click(function(){
        mediaDialog($(this).attr('data-type'), 'add');
		return false;
	});

	$('#rlsearchform').submit(function(){
		var keyword = $(this).find('input[name=search]').val();
		var type = $(this).find('select[name=rl_types] option:selected').text();
		var url = $(this).attr('action')+'&keyword='+keyword+'&type='+type;
		reloadModal(url);
		return false;
	});
	
	//hook to switch language
	$('#content_language_form').submit(function(){
   		var $inputs = $('#content_language_form :input');
	    var url = urls.resource_library;
    	$inputs.each(function() {
    		if ( url.indexOf(this.name+'='+$(this).val()) <= 0 ) {
    			url += '&'+this.name+'='+$(this).val();
    		}
    	});
        reloadModal(url);
		return false;	
	});

	$('#resource_types_tabs a').click(function(){
		$('#resource_types_tabs li.active').removeClass('active');
		$(this).parents('li').addClass('active');
		return false;
	});
}


var enable_menu = function ($obj, enable) {

	if(enable) {
	  $obj.find('.itemopt').removeClass('disabled');
	} else {
	  
	  var ch = false;
	  $obj.find('.thmb').each(function(){
	    if($(this).hasClass('checked'))
	      ch = true;
	  });
	  
	  if(!ch)
	    $obj.find('.itemopt').addClass('disabled');
	}
}   

var active_tab = function(){
	return $('#rl_container ul.nav>li.active');
}

var multi_action = function(action){
	var thmbs = $('div.thmb.checked');
	if(thmbs.length==0){ return false; }
	if(action != 'map' && action != 'unmap' && action != 'delete'){ return false; }

	var postdata = '';
	thmbs.each(function(){
		postdata = postdata + action+'[]=' + $(this).attr('data-rl-id') + '&';
	});

	//save rl details.
	var type = $('#library').attr( 'data-type');
	var src = urls.resource_library+'&action=multisave'+'&type='+type;
	//main ajax call to load rl content
	$.ajax({
		url:src,
		data: postdata,
		type:'POST',
		dataType:'html',
		async: false,
		success:function (html) {
			success_alert('<?php echo $text_success; ?>',true, '.modal-content');
		},
		error:function (jqXHR, textStatus, errorThrown) {
			try{
				var err = $.parseJSON(jqXHR.responseText);
				if (err.hasOwnProperty("error_text")) {
					var errors = err.error_text;
					var errlist = typeof errors === 'string' ? [errors] : errors;

					if(errlist.length>0){
						for(var k in errlist){
							error_alert( errlist[k], false, '.modal-content' );
						}
					}
				}
			}catch(e){
				error_alert(jqXHR.responseText,	false, '.modal-content' );
			}


		}
	});

	active_tab().click(); // reload modal with object's resources
}




</script>