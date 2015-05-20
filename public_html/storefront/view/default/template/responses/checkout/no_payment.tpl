<div class="form-group action-buttons">
    <div class="col-md-12">
	    <a id="<?php echo $button_back->text; ?>" href="<?php echo $back; ?>" class="btn btn-default mr10" title="<?php echo $button_back->text; ?>">
	    	<i class="fa fa-arrow-left"></i>
	    	<?php echo $button_back->text ?>
	    </a>
	    <button id="<?php echo $button_confirm->name ?>" class="btn btn-orange lock-on-click pull-right" title="<?php echo $button_confirm->name ?>" type="submit">
	        <i class="fa fa-check"></i>
	        <?php echo $button_confirm->name; ?>
	    </button>
    </div>
</div>

<script type="text/javascript"><!--
$('body').append('<div id="blocker" style="display: none; width: 1667px; height: 1200px; z-index: 1001; background: none repeat scroll 0 0 white; opacity: 0; left: 0; position: absolute; top: 0;"></div>');

$('#checkout').click(function() {
	$('#blocker').show();
	$.ajax({
		type: 'GET',
		url: '<?php echo $this->html->getURL('r/checkout/no_payment/confirm');?>',
		success: function() {
			location = '<?php echo $continue; ?>';
		}		
	});
});
//--></script>
