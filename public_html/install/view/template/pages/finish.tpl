<?php echo $header; ?>
<div id="stepbar">
    <div class="tl"><div class="tr"><div class="tc"></div></div></div>
    <div class="cl"><div class="cr"><div class="cc">
      <div class="heading">Installation Steps: </div>
      <div class="step">1: License</div>
      <div class="step">2: Compatibility Check</div>
      <div class="step">3: Configuration</div>
      <div class="step">4: Data Load</div>
      <div class="step_current">5: Finished</div>
    </div></div></div>
    <div class="bl"><div class="br"><div class="bc"></div></div></div>
</div>
<div class="warning"><?php echo $message; ?></div>
<div class="main_content">  
  <div class="contentBox">
    <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
      <div class="heading">Installation Completed</div>
    </div></div></div>
    <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    
    <p>Congratulations! You have successfully installed AbanteCart eCommerce application. See access to your store front and control panel sections below. Bookmark or remember your control panel link and keep it save.</p>
    <p>Thank you for this choice, and enjoy many features AbanteCart has to offer. Your comments and contributions are very welcome. 
    <p><a href="http://www.abantecart.com" target="_abante">Project Homepage</a>&nbsp;&nbsp;<a href="http://docs.abantecart.com" target="_blank">Documentation</a>&nbsp;&nbsp;<a href="http://forum.abantecart.com" target="_abante">Support Forums</a></p>
    <p>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="YUDUXTP8GED58">
		<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="Donate to AbanteCart Project and Help build better eCommerce application">
		<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</form>
	</p>
    <div class="snapshots"><div>
        <a href="../"><img src="<?php echo $template_dir; ?>image/storefront.png" alt="" width="250" style="border: none;" /></a><br />
        <a href="../">Your Online Shop</a>
    </div></div>
    <div class="snapshots"><div>
        <a href="../<?php echo $admin_path ?>"><img src="<?php echo $template_dir; ?>image/admin.png" alt="" width="250" style="border: none;" /></a><br />
        <a href="../<?php echo $admin_path ?>">Login to your Control Panel</a>
    </div></div>
    <div class="clr_both">&nbsp;</div>
    
    </div></div></div>
    <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
  </div>
</div>
<?php echo $footer; ?>