<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<a name="top"></a>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_product"><?php echo $heading_title; ?></div>
	<?php echo $product_tabs ?>
	  <div class="toolbar">
	    <?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
      </div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

	<?php echo $summary_form; ?>

		<div class="fieldset">
		  <div class="heading"><a id="tab_image"><?php echo $tab_extensions; ?></a></div>
		  <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
		  <div class="cont_left"><div class="cont_right"><div class="cont_mid">


		  </div></div></div>
		  <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
		</div>

  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>