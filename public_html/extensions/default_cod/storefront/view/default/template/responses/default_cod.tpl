<div class="form-group action-buttons">
    <div class="col-md-12">
    	<button id="checkout_btn" onclick="confirmSubmit();" class="btn btn-orange pull-right" title="<?php echo $button_confirm->text ?>">
    	    <i class="fa fa-check"></i>
    	    <?php echo $button_confirm->text; ?>
    	</button>
    	<a id="<?php echo $button_back->name ?>" href="<?php echo $back; ?>" class="btn btn-default" title="<?php echo $button_back->text ?>">
    	    <i class="fa fa-arrow-left"></i>
    	    <?php echo $button_back->text ?>
    	</a>
    </div>
</div>
<script type="text/javascript"><!--
function confirmSubmit() {
	$('body').css('cursor','wait');
	$.ajax({
		type: 'GET',
		url: '<?php echo $this->html->getURL('extension/default_cod/confirm');?>',
		beforeSend: function() {
			$('.alert').remove();
			$('.action-buttons').hide(); 
			$('.action-buttons').before('<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin"></i> <?php echo $text_wait; ?></div>');
		},		
		success: function() {
			location = '<?php echo $continue; ?>';
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert(textStatus + ' ' + errorThrown);
			$('.wait').remove();
			$('.action-buttons').show();
		}				
	});
}
//--></script>
