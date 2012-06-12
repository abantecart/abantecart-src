<?php if ( !empty($error['warning']) ) { ?>
<div class="warning"><?php echo $error['warning']; ?></div>
<?php } ?>
<a name="top"></a>
<?php echo $resources_scripts ?>
<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_setting"><?php echo $heading_title; ?></div>
    <div class="toolbar">
		<?php echo $form_language_switch; ?>
	    <div class="buttons"><?php echo $text_edit_store; ?>
      <select id="store_switcher" onchange="location = this.value">
        <?php foreach ($stores as $store) { ?>
        <?php if ($store['store_id'] == $store_id) { ?>
        <option value="<?php echo $store['href']; ?>" selected="selected"><?php echo $store['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $store['href']; ?>"><?php echo $store['name']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
      &nbsp;&nbsp; <a onclick="location = '<?php echo $insert; ?>'" class="button"><span><?php echo $button_add_store; ?></span></a>
      <?php if ($delete) { ?>
      &nbsp;<a onclick="if(confirm('<?php echo $text_delete_confirm; ?>')){location = '<?php echo $delete; ?>';}else{ return false;}" class="button"><span><?php echo $button_delete_store; ?></span></a>
      <?php } ?>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>

  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

	<h2><?php echo $form_title; ?></h2>
	<?php echo $form['form_open']; ?>
		<?php foreach ($form['fields'] as $section => $fields) { ?>
        <div class="fieldset">
          <div class="heading"><?php echo ${'tab_'.$section}; ?></div>
          <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
          <div class="cont_left"><div class="cont_right"><div class="cont_mid">
            <div class="back2top"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>#top">Back to top</a></div>
            <table class="form">
            <?php foreach ($fields as $name => $field) { ?>
			<tr>
				<td><?php echo ${'entry_'.$name}; ?></td>
				<td <?php echo ( $name=='description'? 'class="ml_ckeditor"' : ''  )?> >
					<?php
                    switch($name) {
						case 'config_logo':
							echo $settings['config_logo'] .  $field;
                            break;
						case 'config_icon':
							echo $settings['config_icon'] .  $field;
                            break;
                        case 'template':
                            echo $field.'<div id="template"></div>';
                            break;
                        default:
                            echo $field;
                    }
                    ?>
					<?php if (is_array($error[$name]) && !empty($error[$name][$language_id])) { ?>
						<div class="field_err"><?php echo $error[$name][$language_id]; ?></div>
					<?php } else if (!empty($error[$name])) { ?>
						<div class="field_err"><?php echo $error[$name]; ?></div>
					<?php } ?>
				</td>
			</tr>
            <?php }  ?>
          </table>
	      </div></div></div>
          <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
	    </div><!-- <div class="fieldset"> -->
		<?php }  ?>

	<div class="buttons align_center">
	  <button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
	  <a class="btn_standard" href="<?php echo $cancel; ?>" ><?php echo $form['cancel']; ?></a>
    </div>
	</form>

  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>

<script type="text/javascript"><!--

var zone_id = '<?php echo $config_zone_id; ?>';

getZones = function(id, country_id, zone)
{
	if ( !country_id)
	{
		return false;
	}

	if ( !zone )
	{
		zone = 0;
	}

	$.ajax(
	{
		url: '<?php echo $common_zone; ?>&country_id='+ country_id +'&zone_id='+ zone,
		type: 'GET',
		dataType: 'json',
		success: function(data)
		{
			result = data;
			showZones(id, data);
		},
		error: function(req, status, msg)
		{
		}
	});
}

showZones = function(id, data)
{
	var options = '';

	$.each(data['options'], function(i, opt)
	{
		options += '<option value="'+ i +'"';
		if ( opt.selected )
		{
			options += 'selected="selected"';
		}
		options += '>'+ opt.value +'</option>'
	});

	var selectObj = $('#'+ id);

	selectObj.html(options);
	var selected_name = $('#'+ id +' :selected').text();

	selectObj.parent().find('span').text(selected_name);

}

jQuery(function($){
	$('#template').load('<?php echo $template_image; ?>&template=' + encodeURIComponent($('select[name=\'config_storefront_template\']').attr('value')));
	$('#storeFrm_config_storefront_template').change(function(){
		$('#template').load('<?php echo $template_image; ?>&template=' + encodeURIComponent(this.value))
	});

	getZones('storeFrm_config_zone_id', $('#storeFrm_config_country_id').val(), zone_id);

	$('#storeFrm_config_country_id').change(function(){
		getZones('storeFrm_config_zone_id', $('#storeFrm_config_country_id').val(), 0);
		$('#storeFrm_config_zone_id').val(0).change();
		
	});

	$('#config_logo, #config_icon').click(function(){
		selectDialog('image', $(this).attr('id'));

        return false;
    });

	$('#store_switcher').aform({triggerChanged: false}).width(100);
	$.aform.styleGridForm('#store_switcher');

});
//--></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/adapters/jquery.js"></script>
<script type="text/javascript"><!--
$('#store_description\\[<?php echo $content_language_id; ?>\\]\\[description\\]').parents('.afield').removeClass('mask2');
CKEDITOR.replace('store_description[<?php echo $content_language_id; ?>][description]', {
	filebrowserBrowseUrl : false,
    filebrowserImageBrowseUrl : '<?php echo $rl; ?>',
    filebrowserWindowWidth : '910',
    filebrowserWindowHeight : '500',
	language: '<?php echo $language_code; ?>'
});
$('.ml_ckeditor').find('div').removeClass('mask2');
//--></script>
