<?php if ( !empty($error['warning']) ) { ?>
<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
<?php } ?>
<a name="top"></a>
<?php echo $resources_scripts ?>
<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_setting"><?php echo $heading_title; ?></div>
    <div class="toolbar">	
    	<?php echo $form_language_switch; ?>
	    <div class="buttons">
			<div class="flt_left align_left"><?php echo $text_edit_store; ?><?php echo $store_selector; ?></div>
      		<?php if ($edit_settings) { ?>
	      	<div class="flt_left">&nbsp;&nbsp;<?php echo $edit_settings_button; ?></div>
      		<?php } ?>
      		<?php if ($delete) { ?>
	      	<div class="flt_left">&nbsp;&nbsp;<?php echo $delete_store_button; ?></div>
      		<?php } ?>
			<div class="flt_left">&nbsp;&nbsp;<?php echo $new_store_button; ?></div>	
    	</div>
  </div>
  </div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
	<?php echo $form['form_open']; ?>
		<?php foreach ($form['fields'] as $section => $fields) { ?>
        <div class="fieldset">
          <div class="heading"><?php echo $form_title; ?></div>
          <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
          <div class="cont_left"><div class="cont_right"><div class="cont_mid">
            <table class="form">
            <?php foreach ($fields as $name => $field) { ?>
			<tr>
				<td><?php echo ${'entry_'.$name}; ?></td>
				<td <?php echo ( $name=='description'? 'class="ml_ckeditor"' : ''  )?> >
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
jQuery(function($){
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
    filebrowserWindowWidth : '920',
    filebrowserWindowHeight : '520',
	language: '<?php echo $language_code; ?>'
});
$('.ml_ckeditor').find('div').removeClass('mask2');
//--></script>
