<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_blocks"><?php echo $heading_title; ?></div>
	<div class="toolbar">
		<?php echo $form_language_switch; ?>
	<?php if($button_details){	?>
	<div class="tools">
		<a class="btn_standard" href="<?php echo $detail_link; ?>"><?php echo $button_details; ?></a>
	</div>
	<?php } ?>
	</div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

	<?php echo $form['form_open']; ?>
	<div class="fieldset">
	  <div class="heading"><?php echo $form_title; ?></div>
	  <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
	  <div class="cont_left"><div class="cont_right"><div class="cont_mid">

          <table class="form">
            <?php foreach ($form['fields'] as $name => $field) { ?>
				<tr>
					<td><?php echo $form[ 'text' ][$name]; ?></td>
					<td class="<?php echo ($name=='description'? 'ml_ckeditor': (in_array($name,array('status','daterange')) ? '' : 'ml_field')); ?>">
						<?php echo $field; ?>
						<?php if (!empty($error[$name])) { ?>
							<div class="field_err"><?php echo $error[$name]; ?></div>
						<?php } ?>
					</td>
				</tr>
			<?php }   ?>
          </table>

  </div></div></div>
      <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
	</div>
	<div class="buttons align_center">
	  <button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
	  <a class="btn_standard" href="<?php echo $cancel; ?>" ><?php echo $form['cancel']; ?></a>
    </div>
	</form>

  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>
<div id="confirm_unmap_dialog" title="Confirm resource(s) Unmap" style="display:none"><?php echo $text_confirm_unmap ?></div>
<div id="confirm_del_dialog" title="Confirm resource(s) Delete" style="display:none"><?php echo $text_confirm ?></div>
<?php echo $resources_scripts; ?>
<script type="text/javascript">
$(document).ready(function() {
	setRLparams();
	loadSubform();
});


function setRLparams(){
	var banner_id = '<?php echo $banner_id ?>';
	urls.resource_library = '<?php echo $rl_rl_path; ?>&object_id='+banner_id;
	urls.resources = '<?php echo $rl_resources_path; ?>&object_id='+banner_id;
	urls.unmap = '<?php echo $rl_unmap_path; ?>&object_id='+banner_id;
	urls.banner_id = banner_id;
}

// override rl js-script function
var loadMedia = function() {
	var type="image";
    $.ajax({
        url: urls.resources,
        type: 'GET',
        data: { type : type },
        dataType: 'json',
        success: function(json) {

	        var html = '';
            $(json.items).each(function(index, item){
                var src = '<img src="' + item['thumbnail_url'] + '" title="' + item['name'] + '" />';
                if ( type == 'image' && item['resource_code']  ) {
                    src = item['thumbnail_url'];
                }
                html += '<span id="image_row' + item['resource_id'] + '" class="image_block" style="text-align:center;">\
                <a class="resource_edit" type="' + type + '" id="' + item['resource_id'] + '">' + src + '</a><br />\
                '+( item['mapped'] > 1 ? '' : '<a class="btn_action resource_delete" id="' + item['resource_id'] + '"><span class="icon_s_delete"><span class="btn_text"><?php echo $button_delete ?></span></span></a>')+'\
                <a class="btn_action resource_unmap" id="' + item['resource_id'] + '"><span class="icon_s_unmap"><span class="btn_text"><?php echo $button_unmap ?></span></span></a>\
                <a class="btn_action resource_edit" type="' + type + '" id="' + item['resource_id'] + '"><span class="icon_s_edit"><span class="btn_text"><?php echo $button_edit ?></span></span></a>\
                </span>';
            });
            html += '<span class="image_block"><a class="resource_add" type="' + type + '"><img src="<?php echo $template_dir.'/image/icons/icon_add_media.png'; ?>" alt="<?php echo $text_add_media; ?>"/></a></span>';

	        $('#rl_'+urls.banner_id).html(html);
	        if($(json.items).length){
		       $('a.resource_edit').unbind('click');
				$('a.resource_edit').click( function(){
					mediaDialog( $(this).prop('type'), 'update', $(this).prop('id'));
				return false;
			})
	        }
	        $('a.resource_add').unbind('click');
			$('a.resource_add').click( function(){
				mediaDialog( $(this).prop('type'), 'add', $(this).prop('id'));
				return false;
			});
        },
        error: function(jqXHR, textStatus, errorThrown){
            $('#type_'+type).show();
            $('#rl_'+urls.banner_id).html('<div class="error" align="center"><b>'+textStatus+'</b>  '+errorThrown+'</div>');
        }
    });

}

var loadSubform = function (){
	if($('#BannerFrm_banner_type').val()=='2'){
		$('#BannerFrm_target_url, #BannerFrm_blank').attr("disabled","disabled").parents('tr').hide();
	}else{
		$('#BannerFrm_target_url, #BannerFrm_blank').removeAttr("disabled").parents('tr').show();
	}
	$.ajax({
        url: '<?php echo $subform_url ?>',
        type: 'GET',
        data: { 'type' : $('#BannerFrm_banner_type').val() },
        success: function(html) {
	        $('table.form tr.subform').each( function (){
		        $(this).remove();
	        })
	        $('table.form').append(html);
	        if($('#rl_<?php echo $banner_id ?>')){
				loadMedia( 'image' );
			}

	        if($('#BannerFrm_description').length){
				$('#BannerFrm_description').parents('.afield').removeClass('mask2');
		        if(CKEDITOR.instances['BannerFrm_description']){
		            CKEDITOR.remove( CKEDITOR.instances['BannerFrm_description'] );
		        }
				CKEDITOR.replace('BannerFrm_description',{
						height: '400px',
						filebrowserBrowseUrl : false,
						filebrowserImageBrowseUrl : '<?php echo $rl; ?>',
						filebrowserWindowWidth : '920',
						filebrowserWindowHeight : '520',
						language: '<?php echo $language_code; ?>',
						startupMode: 'source'
					});
			}
        }
	});
}

$('#BannerFrm_banner_type').change(loadSubform);

$('#BannerFrm_banner_group_name\\\[0\\\]').change( function(){
	$(this).val() == 'new' ? $('#BannerFrm_banner_group_name\\\[1\\\]').show().parents('.aform').show() : $('#BannerFrm_banner_group_name\\\[1\\\]').hide().parents('.aform').hide();
	!$('#BannerFrm_banner_group_name\\\[1\\\]').is(':visible') ? $('#BannerFrm_banner_group_name\\\[1\\\]').val('<?php echo $new_group_hint; ?>') : null;
});
$('#BannerFrm_banner_group_name\\\[1\\\]').click( function(){
	$(this).val() == '<?php echo $new_group_hint; ?>' ? $(this).val('') : null;
});

</script>