<?php echo $header; ?>
<div id="stepbar">
    <div class="tl"><div class="tr"><div class="tc"></div></div></div>
    <div class="cl"><div class="cr"><div class="cc">
      <div class="heading">Installation Steps: </div>
      <div class="step">1: License</div>
      <div class="step">2: Compatibility Check</div>
      <div class="step_current">3: Configuration</div>
      <div class="step">4: Data Load</div>
      <div class="step">5: Finished</div>
    </div></div></div>
    <div class="bl"><div class="br"><div class="bc"></div></div></div>
</div>
<?php if ( !empty($error['warning']) ) { ?>
<div class="warning"><?php echo $error['warning']; ?></div>
<?php } ?>

<div class="main_content">    
  <div class="contentBox">
    <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
      <div class="heading">
        Configuration
      </div>
    </div></div></div>
    <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    
      <p id="hint">Configuring Your AbanteCart database...</p>
      <div class="section">
        <table width="100%">
          <tr id="progress">
            <td width="100%" align="center"><img src="<?php echo $progressbar; ?>" /></td>
          </tr>
	      <tr id="error" style="display: none;">
            <td class="warning" width="100%"></td>
          </tr>
        </table>
      </div>

    </div></div></div>
    <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
  </div>
</div>
<?php echo $footer; ?>

<script language="JavaScript">
	var step = 2;
	function install() {
		$.ajax({  type: 'POST',
		   		  url: '<?php echo $url; ?>&progress='+step,
				  success: function(data) {
						if(data == 50){
							step=3;
							$('#hint').html('Configuring Your AbanteCart...');
							install();
						}else if (data == 100) {
							window.location = '<?php echo $redirect; ?>';
						}else{
							$('#progress').hide();
							$('#error').show();
							var text = "Sorry, but error occured during installation:</br>";
							text += data;
							$('#error > td').html(text);

						}
				}
			});
		}
	install();
</script>