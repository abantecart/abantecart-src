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
            <td width="100%" align="center">
                <div id="progressbar"></div>
	            <div id="process_info"></div>
	            <div id="process_skip"></div>
            </td>
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
	var messages = { 3: 'Writing configuration file ...',
					 4: 'Installing Default Language. This might take a moment ... ',
					error: 'Sorry, but error occurred during installation:</br>'
					};
	var step = 2;
	var stop = false;
	function install() {

		$.ajax({	type: 'POST',
					dataType: 'JSON',
		   		  	url: '<?php echo $url; ?>&runlevel='+step,
				  	success: function(response) {
						if(response.ret_code == 50){
							step=3;
							$('#hint').html(messages[step]);
							if(!stop){
								install();
							}
						}else if(response.ret_code == 100){
							step=4;
							$('#hint').html(messages[step]);
							install();
						}else if (response.ret_code == 150) {
							var step3substeps = response.blocks_list;
							var len = step3substeps['admin'].length + step3substeps['storefront'].length;
							var i=1;
							for(var section in step3substeps ){
								for(var rt in step3substeps[section] ){
									if(stop) break;
									$( "#progressbar" ).progressbar({ value: Math.round(i*100/len) });
									checkstate(section,step3substeps[section][rt]);
									i++;
								}
							}
							if(!stop) {
								window.location = '<?php echo $redirect; ?>';
							}
						}else{
							showError(response);
						}
					},
					error: function(jqXHR, exception) {
						showError(jqXHR.statusText + ": " + jqXHR.responseText);
					}
				});
		}

	function showError(response){
		$('#process_info, #progressbar').hide();
		$('#error').show();
		var text = messages.error;
		text += response;
		$('#hint').html('Error');
		$('#error > td').html(text);
	}

	function checkstate(section,language_block){
		if(stop) return;
		$('#process_info').html(section + ": Loading language data for " + language_block);
		$.ajax({	type: 'POST',
					dataType: 'JSON',
					url: '<?php echo $state_url; ?>',
					data: {'section': section, 'language_block': language_block},
					async: false,
					cache: false,
					error: function(jqXHR, exception) {
					    	stop=true;
					        showError(jqXHR.statusText + ": " + jqXHR.responseText);
					},				  
					success: function(response) {
					    if( response.ret_code != 10 ){
					    	stop=true;
					        showError(response.error);
					    }
					}
		});
	}

	install();
	$(function() {
	        $( "#progressbar" ).progressbar({
	            value: 10
	        });
	    });

</script>