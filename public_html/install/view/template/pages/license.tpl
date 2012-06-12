<?php echo $header; ?>
<div id="stepbar">
    <div class="tl"><div class="tr"><div class="tc"></div></div></div>
    <div class="cl"><div class="cr"><div class="cc">
      <div class="heading">Installation Steps: </div>
      <div class="step_current">1: License</div>
      <div class="step">2: Compatibility Check</div>
      <div class="step">3: Configuration</div>
      <div class="step">4: Data Load</div>
      <div class="step">5: Finished</div>
    </div></div></div>
    <div class="bl"><div class="br"><div class="bc"></div></div></div>
</div>
  
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="main_content">
  
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
  <div class="contentBox flt_right">
    <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
      <div class="heading">
        License
        <div class="buttons"><a onclick="document.getElementById('form').submit()" class="btn_standard"><?php echo $button_continue; ?></a></div>
      </div>
    </div></div></div>
    <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
      
      <div id="license">
        <div class="tl"><div class="tr"><div class="tc"></div></div></div>
        <div class="cl"><div class="cr"><div class="cc">
          <div class="license_text"><?php echo $text; ?>
          </div>
        </div></div></div>
        <div class="bl"><div class="br"><div class="bc"></div></div></div>
      </div>
      
      <div class="action_bar">
      	<div class="agree_field"><?php echo $checkbox_agree; ?><label for="form_agree">I agree to the license</label></div>
      	<div class="align_right"><a onclick="document.getElementById('form').submit()" class="btn_standard"><?php echo $button_continue; ?></a></div>
      </div>
    </div></div></div>
    <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
  </div>
  </form>
</div>
<?php echo $footer; ?>