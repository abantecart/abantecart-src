<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading"><?php echo $heading_title; ?></div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div id="cc" class="cbox_cc">
	<div class="ui-progressbar-value" id="progressbar"><img src="<?php echo $template_dir; ?>image/progressbar.gif"/></div>
	<iframe id="marketplace" src="<?php echo $src; ?>" frameBorder="0"></iframe>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>
<script type="text/javascript">
      $("#marketplace").load(function(){
		var hh = document.getElementById('marketplace').contentWindow.document.body.scrollHeight;
		if ( $.browser.msie ) {
			hh += 50;
		}
        $("#marketplace").height(hh);
        $("#progressbar").hide();
      });
</script>