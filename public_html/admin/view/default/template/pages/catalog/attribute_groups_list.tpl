<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_length"><?php echo $heading_title; ?></div>
      <div class="toolbar">
  		<?php if ( !empty ($help_url) ) : ?>
  	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
  	    <?php endif; ?>
  	    <?php echo $form_language_switch; ?>
  	    <div class="buttons">
        <a class="btn_toolbar" title="<?php echo $button_insert; ?>" href="<?php echo $insert; ?>">
  		<span class="icon_add">&nbsp;</span>
  	  </a>
      </div></div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    <?php echo $listing_grid; ?>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>