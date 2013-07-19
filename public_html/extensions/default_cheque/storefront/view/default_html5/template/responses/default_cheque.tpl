<div class="checkout_details"><?php echo $text_payable; ?><br />
  <?php echo $payable; ?><br />
  <br />
  <?php echo $text_address; ?><br />
  <?php echo $address; ?><br />
  <br />
  <?php echo $text_payment; ?>
</div>
  
<div class="control-group action-buttons">
    <div class="controls">
    	<button id="checkout_btn" class="btn btn-orange pull-right" onclick="confirmSubmit();" title="<?php echo $button_confirm->text ?>">
    	    <i class="icon-ok icon-white"></i>
    	    <?php echo $button_confirm->text; ?>
    	</button>
    	<a id="<?php echo $button_back->name ?>" href="<?php echo $back; ?>" class="btn mr10" title="<?php echo $button_back->text ?>">
    	    <i class="icon-arrow-left"></i>
    	    <?php echo $button_back->text ?>
    	</a>
    </div>
</div>

<script type="text/javascript"><!--
function confirmSubmit() {
	$('body').css('cursor','wait');
	$.ajax({ 
		type: 'GET',
		url: 'index.php?rt=extension/default_cheque/confirm',
		beforeSend: function() {
			$('#checkout_btn').attr('disabled', 'disabled');
			
			$('.action-buttons').before('<div class="wait alert alert-info"><img src="<?php echo $template_dir; ?>image/loading_1.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},		
		success: function() {
			$('.wait').remove();
			$('#checkout_btn').removeAttr('disabled');		
			location = '<?php echo $continue; ?>';
		}		
	});
}
//--></script>
