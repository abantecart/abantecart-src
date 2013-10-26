<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_setting"><?php echo $heading_title; ?></div>
	<div class="heading-tabs">
        <a href="<?php echo $link_all; ?>" class="active"><span><?php echo $tab_all; ?></span></a>
        <a href="<?php echo $link_details; ?>" ><span><?php echo $tab_details; ?></span></a>        
        <a href="<?php echo $link_general; ?>" ><span><?php echo $tab_general; ?></span></a>
        <a href="<?php echo $link_checkout; ?>" ><span><?php echo $tab_checkout; ?></span></a>
        <a href="<?php echo $link_appearance; ?>" ><span><?php echo $tab_appearance; ?></span></a>
        <a href="<?php echo $link_mail; ?>" ><span><?php echo $tab_mail; ?></span></a>
        <a href="<?php echo $link_api; ?>" ><span><?php echo $tab_api; ?></span></a>
        <a href="<?php echo $link_system; ?>" ><span><?php echo $tab_system; ?></span></a>
	</div>
	<div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
		<div class="buttons">
			<div class="flt_left align_left"><?php echo $text_edit_store_settings; ?> <?php echo $store_selector; ?></div>
			<div class="flt_left">&nbsp;&nbsp;<?php echo $new_store_button; ?></div>	
		</div>
	</div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    <?php echo $listing_grid; ?>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>
<div id="edit_dialog" style="overflow:hidden;"></div>
<script type="text/javascript"><!--

$(".toolbar select").aform({
    triggerChanged: false
});

function openEditDiag(id) {
    var $Popup = $('#edit_dialog').dialog({
        autoOpen:true,
        modal:true,
        bgiframe:false,
        width: 650,
        height:'auto',
        maxHeight: 700,
        draggable:true,
        modal:true,
        close:function (event) {
            $(this).dialog('destroy');
			CKEditor('destroy');
			$('#setting_grid').trigger("reloadGrid");;

		}
    });


    // spinner
    $("#edit_dialog").html('<div class="progressbar">Loading ...</div>');

    $.ajax({
        url:'<?php echo $dialog_url; ?>&target=edit_dialog',
        type:'GET',
        dataType:'json',
        data:{active:id},
        success:function (data) {
            if(data.html==''){
                $('#edit_dialog').dialog("close");
                return;
            }
            $("#edit_dialog").html(data.html).dialog('option', 'title', data.title);

            $('#store_switcher').aform({ triggerChanged: false })
            .live('change',function () {
                $.getJSON('<?php echo $dialog_url; ?>'+'&active='+ id +'&target=edit_dialog&store_id=' + $(this).val(),
                    function (response) {
                        $('#edit_dialog').html(response.html);
						CKEditor('add');
                    });
            });

			CKEditor('add');

            $('#cgFrm_cancel').live('click',function(){
                $('#edit_dialog').dialog("close");
            });
        }
    });

	function CKEditor(mode){
		var settings = [];
		settings[0] = 'cgFrm_config_description_<?php echo $content_language_id; ?>';
		settings[1] = 'cgFrm_config_meta_description';

		for( var k in settings ){

			if($('#'+settings[k]).length>0){
				if(mode=='add'){
					$('#'+settings[k]).parents('.afield').removeClass('mask2');
					$('#'+settings[k]).parents('td').removeClass('ml_field').addClass('ml_ckeditor');

					CKEDITOR.replace(settings[k], {
						filebrowserBrowseUrl:false,
						filebrowserImageBrowseUrl:'<?php echo $rl; ?>',
						filebrowserWindowWidth:'920',
						filebrowserWindowHeight:'520',
						language:'<?php echo $language_code; ?>'
					});
					$("#edit_dialog").dialog('option', 'width', '800');
				}else{
					var editor = CKEDITOR.instances[settings[k]];
					if (editor) { editor.destroy(true); }
				}
			}
		}
	}
}


//--></script>

<?php if($resources_scripts){
    echo $resources_scripts;
}
?>