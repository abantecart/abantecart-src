<h4 class="heading4"><?php echo $text_credit_card; ?>:</h4>

<form id="cashflows" class="form-horizontal validate-creditcard">
<?php echo $this->getHookVar('payment_table_pre'); ?>
	<div class="form-group form-inline">
	    <label class="col-sm-4 control-label"><?php echo $entry_cc_number; ?></label>
	    <div class="col-sm-5 input-group">
	    	<?php echo $cc_number; ?>
	    </div>
	    <span class="help-block"></span>
	</div>
	<div class="form-group form-inline">
	    <label class="col-sm-4 control-label"><?php echo $entry_cc_expire_date; ?></label>
	    <div class="col-sm-3 input-group">
	    	<?php echo $cc_expire_date_month; ?>
	    </div>
	    <div class="col-sm-2 input-group">
	    	<?php echo $cc_expire_date_year; ?>
	    </div>
	    <span class="help-block"></span>
	</div>
	<div class="form-group ">
	    <label class="col-sm-6 control-label"><?php echo $entry_cc_cvv2; ?> <a onclick="openModalRemote('#ccModal', '<?php echo $cc_cvv2_help_url; ?>')" href="Javascript:void(0);"><?php echo $entry_cc_cvv2_short; ?></a></label>
	    <div class="input-group col-sm-3">
	    	<?php echo $cc_cvv2; ?>
	    </div>
	    <span class="help-block"></span>
	</div>

<?php echo $this->getHookVar('payment_table_post'); ?>

	<div class="form-group action-buttons text-center">
	    <a id="<?php echo $back->name ?>" href="<?php echo $back->href; ?>" class="btn btn-default mr10" title="<?php echo $back->text ?>">
	    	<i class="fa fa-arrow-left"></i>
	    	<?php echo $back->text ?>
	    </a>
	    <button id="<?php echo $submit->name ?>" class="btn btn-orange" title="<?php echo $submit->text ?>" type="submit">
	        <i class="fa fa-check"></i>
	        <?php echo $submit->text; ?>
	    </button>
	</div>
	
</form>

<!-- Modal -->
<div id="ccModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ccModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3><?php echo $entry_what_cvv2; ?></h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_close; ?></button>
	</div>
</div>
</div>
</div>  

<script type="text/javascript"><!--
//validate submit
$('form').submit(function(event) {
	event.preventDefault();
	if( !$.aCCValidator.validate($('form.validate-creditcard')) ){
		return false;
	} else {
		confirmSubmit();
	}
});

function confirmSubmit() {		
	$.ajax({
		type: 'POST',
		url: '<?php echo $this->html->getURL('extension/default_cashflows/send');?>',
		data: $('#cashflows :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('.alert').remove();
			$('#cashflows .action-buttons').hide(); 
			$('#cashflows .action-buttons').before('<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin"></i> <?php echo $text_wait; ?></div>');
		},
		success: function(data) {
			if (!data) {
				$('.wait').remove();
				$('#cashflows .action-buttons').show(); 
				$('#cashflows').before('<div class="alert alert-danger"><i class="fa fa-bug"></i> <?php echo $error_unknown; ?></div>');
			} else {					  			
				if (data.error) {
					$('.wait').remove();
					$('#cashflows .action-buttons').show(); 
					$('#cashflows').before('<div class="alert alert-warning"><i class="fa fa-exclamation"></i> '+data.error+'</div>');
				}	
				if (data.success) {			
					location = data.success;
				}
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			$('.wait').remove();
			$('#cashflows .action-buttons').show(); 
			$('#cashflows').before('<div class="alert alert-danger"><i class="fa fa-exclamation"></i> '+textStatus+' '+errorThrown+'</div>');
		}				
	});
}
//--></script>