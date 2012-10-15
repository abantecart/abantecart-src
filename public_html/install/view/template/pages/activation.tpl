<?php echo $header; ?>
<div id="stepbar">
    <div class="tl"><div class="tr"><div class="tc"></div></div></div>
    <div class="cl"><div class="cr"><div class="cc">
      <div class="heading">Installation Steps: </div>
      <div class="step">1: License</div>
      <div class="step">2: Compatibility Check</div>
      <div class="step">3: Configuration</div>
      <div class="step_current">4: Data Load</div>
      <div class="step">5: Finished</div>
    </div></div></div>
    <div class="bl"><div class="br"><div class="bc"></div></div></div>
</div>
<?php if($salt){?>
	<div class="warning" style="font-size: 16px">
		Keep this secure key in save place.<br>This key is used to encrypt customers's secure data.<br>You will need this key if you move data to different shopping cart installation.<br>
		<h2><?php echo $salt; ?></h2></div>
<?php }  ?>
<div class="main_content">  
  <div class="contentBox">
    <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
      <div class="heading">Store Data Load</div>
    </div></div></div>
    <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

    <div class="snapshots"><div>
        <a href="<?php echo HTTP_SERVER . 'index.php?rt=activation&install_demo'; ?>"><img src="<?php echo $template_dir; ?>image/demo_data.png" alt="" width="128" style="border: 1px solid #DDDDDD; padding: 2px; margin-bottom: 3px;" /></a><br />
        <a href="<?php echo HTTP_SERVER . 'index.php?rt=activation&install_demo'; ?>">Install Demo Data? <br />(Demo Products, Customers, Orders, etc) </a>
    </div></div>
    <div class="snapshots"><div>
        <a href="../<?php echo $admin_path . '&rt=tool/migration/step_one'; ?>"><img src="<?php echo $template_dir; ?>image/import_data.png" alt="" width="128" style="border: 1px solid #DDDDDD; padding: 2px; margin-bottom: 3px;" /></a><br />
        <a href="../<?php echo $admin_path . '&rt=tool/migration/step_one'; ?>">Import Data from other application? <br/> (You can transfer from your existing shopping cart)</a>
    </div></div>

    <div class="clr_both">&nbsp;</div>

    <div align="center"><a class="button "href="<?php echo HTTP_SERVER . 'index.php?rt=finish'; ?>"><img src="<?php echo $template_dir; ?>image/skip_button.png" alt="Continue and skip data load" border="0" /></a></div>
    
    </div></div></div>
    <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
  </div>
</div>
<?php echo $footer; ?>