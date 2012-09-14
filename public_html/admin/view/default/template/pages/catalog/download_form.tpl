<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>

<div id="aPopup"><div class="popup_loading"></div></div>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_download"><?php echo $heading_title; ?></div>
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
	  <div class="heading"><?php echo $form_title; ?></div>
	  <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
	  <div class="cont_left"><div class="cont_right"><div class="cont_mid">
		<table class="form">
        <tr>
			<td><?php echo $entry_name; ?></td>
			<td class="ml_field">
				<?php echo $form['fields']['name']; ?>
				<?php if (!empty($error['name'])) { ?>
					<div class="field_err"><?php echo $error['name']; ?></div>
				<?php } ?>
			</td>
		</tr>
        <tr>
          <td><?php echo $entry_filename; ?></td>
          <td>
              <div id="download"><a type="image" class="resource_add">
                <?php if ( !empty( $filename ) ) {
                    echo '<img src="'.HTTP_IMAGE. 'icon_resource_archive.png" /></div>';
                } else {
                    echo '<img src="'. $template_dir.'/image/icons/icon_add_media.png" alt="'. $text_add_media.'"/>';
                } ?></a>
              </div>
              <div id="download_link"><?php if ( !empty($download_link) ) { echo '<a href="'.$download_link.'" target="_blank">'.$mask.'</a>';  } ?></div>
              <?php echo $text_click_to_change; ?>
              <?php echo $form['fields']['download']; ?>
              <?php echo $form['fields']['mask']; ?>
	          <?php if (!empty($error['file'])) { ?>
					<div class="field_err"><?php echo $error['file']; ?></div>
				<?php } ?>
          </td>
        </tr>
        <tr>
          <td><?php echo $entry_remaining; ?></td>
          <td><?php echo $form['fields']['remaining']; ?></td>
        </tr>
        <?php if ($show_update) { ?>
        <tr>
          <td><?php echo $entry_update; ?></td>
          <td><?php echo $form['fields']['update']; ?></td>
        </tr>
        <?php } ?>
      </table>
	  </div></div></div>
      <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
	</div><!-- <div class="fieldset"> -->
	<div class="buttons align_center">
	  <button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
	  <a class="btn_standard" href="<?php echo $cancel; ?>" ><?php echo $form['cancel']; ?></a>
    </div>
	</form>

        </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>

<?php echo $resources_scripts; ?>
<script type="text/javascript"><!--
jQuery(function($){
    $('#download').click(function(){
        selectDialog('archive', $(this).attr('id'));
        return false;
    });
});
onSelectClose = function(e,ui){
    if (typeof selectResource == 'undefined')  return;
    $('input[name="mask"]').val( selectResource.name );
    $('#download_link').html('<a href="<?php echo $rl_get_preview; ?>&resource_id='+selectResource.resource_id+'" target="_blank">'+selectResource.name+'</a>');
}
//--></script>
