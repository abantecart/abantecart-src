<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
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
<script type="text/javascript"><!--
jQuery(function($){
    $(".toolbar select").aform({
		triggerChanged: false,
	});
});
//--></script>