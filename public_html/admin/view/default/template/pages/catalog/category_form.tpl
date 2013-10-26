<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<a name="top"></a>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_category"><?php echo $heading_title; ?></div>
<?php if ($update) { ?>
	<div class="heading-tabs">
		<a href="<?php echo $category_form_general ?>" class="active"><span><?php echo $tab_general; ?></span></a>
		<a href="<?php echo $category_form_data ?>"><span><?php echo $tab_data; ?></span></a>
		<a href="<?php echo $category_form_image ?>"><span><?php echo $tab_image; ?></span></a>
		<a href="<?php echo $category_layout ?>" ><span><?php echo $tab_layout; ?></span></a>
		<?php echo $this->getHookVar('extension_tabs'); ?>
	</div>
<?php } ?>
	<div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
        <?php echo $form_language_switch; ?>
    </div>
  </div></div></div>
<div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
	<?php echo $form['form_open']; ?>
    <div class="fieldset">
      <div class="heading"><a id="general"><?php echo $tab_general; ?></a></div>
      <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
      <div class="cont_left"><div class="cont_right"><div class="cont_mid">
        <table class="form">
			<tr>
              <td><?php echo $entry_status; ?></td>
	          <td><?php echo $form['fields']['status']; ?></td>
	        </tr>
			<tr>
              <td><?php echo $entry_category; ?></td>
              <td><?php echo $form['fields']['parent_id']; ?></td>
			</tr>
        	<tr>
              <td><?php echo $entry_name; ?></td>
              <td><?php echo $form['fields']['name']; ?>
                <?php if (isset($error_name[$language_id])) { ?>
                <span class="error"><?php echo $error_name[$language_id]; ?></span>
                <?php } ?></td>
            </tr>
	        <tr>
              <td><?php echo $entry_description; ?></td>
              <td class="ml_ckeditor"><?php echo $form['fields']['description']; ?></td>
            </tr>
        </table>
      </div></div></div>
      <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
    </div>
    
    <div class="fieldset">
      <div class="heading"><a id="data"><?php echo $tab_data; ?></a></div>
      <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
      <div class="cont_left"><div class="cont_right"><div class="cont_mid">
        <div class="back2top"><a href="<?php echo $current_url; ?>#top"><?php echo $text_on_top; ?></a></div>
      	<table class="form">
            <tr>
              <td><?php echo $entry_meta_keywords; ?></td>
              <td><?php echo $form['fields']['meta_keywords']; ?>    </td>
            </tr>
	        <tr>
              <td><?php echo $entry_meta_description; ?></td>
              <td><?php echo $form['fields']['meta_description']; ?> </td>
			</tr>
			<tr>
              <td><?php echo $entry_keyword; ?></td>
	          <td><?php echo $form['fields']['keyword']; ?></td>
			</tr>
			<tr>
			  <td><?php echo $entry_store; ?></td>
              <td><div class="scrollbox small-scrollbox"><?php echo $form['fields']['store']; ?></div></td>
			</tr>
			<tr>
              <td><?php echo $entry_sort_order; ?></td>
	          <td><?php echo $form['fields']['sort_order']; ?></td>            
			</tr>
        </table>
      </div></div></div>
      <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
    </div>
	<span id="tab_media">
    <?php if ( !empty($update) ) { echo $resources_html; } ?>
	</span>
    <div class="buttons align_center">
	  <button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
	  <a class="btn_standard" href="<?php echo $cancel; ?>" ><?php echo $form['cancel']; ?></a>
    </div>
	</form>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>

<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/adapters/jquery.js"></script>
<script type="text/javascript"><!--
$('#editFrm_category_description\\[<?php echo $language_id; ?>\\]\\[description\\]').parents('.afield').removeClass('mask2');

CKEDITOR.replace('editFrm_category_description[<?php echo $language_id; ?>][description]',
{
	filebrowserBrowseUrl : false,
    filebrowserImageBrowseUrl : '<?php echo $rl; ?>',
    filebrowserWindowWidth : '920',
    filebrowserWindowHeight : '520',
	language: '<?php echo $language_code; ?>'
});


$('#editFrm_generate_seo_keyword').click(function(){
	var seo_name = $('#editFrm_category_description\\[<?php echo $language_id; ?>\\]\\[name\\]').val().replace('%','');
	$.get('<?php echo $generate_seo_url;?>&seo_name='+seo_name, function(data){
		$('#editFrm_keyword').val(data).change();
	});
});

//--></script>
<?php if ( !empty($update) ) { echo $resources_scripts; } ?>