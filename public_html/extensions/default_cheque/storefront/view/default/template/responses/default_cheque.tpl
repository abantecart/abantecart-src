<div class="checkout_details"><?php echo $text_payable; ?><br />
  <?php echo $payable; ?><br />
  <br />
  <?php echo $text_address; ?><br />
  <?php echo $address; ?><br />
  <br />
  <?php echo $text_payment; ?>
</div>
  
<div class="form-group action-buttons">
    <div class="col-md-12">
    	<button id="checkout_btn" class="btn btn-orange pull-right" onclick="confirmSubmit();" title="<?php echo $button_confirm->text ?>">
    	    <i class="icon-ok icon-white"></i>
    	    <?php echo $button_confirm->text; ?>
    	</button>
    	<a id="<?php echo $button_back->name ?>" href="<?php echo $back; ?>" class="btn btn-default mr10" title="<?php echo $button_back->text ?>">
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
			$('#checkout_btn').parent().hide();			
			$('.action-buttons').before('<div class="wait alert alert-info"><img src="<?php echo $template_dir; ?>image/loading_1.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},		
		success: function() {
			location = '<?php echo $continue; ?>';
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert(textStatus + ' ' + errorThrown);
			$('.wait').remove();	
			$('#checkout_btn').parent().show();
		}				
	});
}
//--></script>
