<?php if($saved_cc_list) { ?>
<div class="saved_cards">
<form id="stripe_saved_cc" class="validate-creditcard">
<h4 class="heading4"><?php echo $text_saved_credit_card; ?></h4>

	<div class="form-group form-inline">
		<span class="subtext"><?php echo $entry_billing_address; ?>: <?php echo $payment_address; ?>...</span>
		<div class="col-sm-2 input-group">
			<a href="<?php echo $edit_address; ?>" class="btn btn-default btn-sm">
				<i class="fa fa-edit fa-fw"></i>
				<?php echo $entry_edit; ?>
			</a>
		</div>
	</div>

	<div class="form-group form-inline">
	    <div class="col-sm-6 input-group">
	    	<?php echo $saved_cc_list; ?>
	    </div>
		<div class="col-sm-2 input-group">
			<a id="delete_card" class="btn btn-default btn-sm" title="<?php echo $text_delete_saved_credit_card; ?>">
				<i class="fa fa-trash-o fa-fw"></i>
				<?php echo $text_delete_saved_credit_card; ?>
			</a>
	    </div>
		<div class="col-sm-2 input-group">
			<a id="new_card" class="btn btn-info btn-sm" title="<?php echo $text_new_credit_card; ?>">
				<i class="fa fa-plus fa-fw"></i>
				<?php echo $text_new_credit_card; ?>
			</a>
		</div>
	</div>

	<div class="form-group action-buttons text-center">
		<a id="<?php echo $back->name ?>" href="<?php echo $back->href; ?>" class="btn btn-default mr10">
			<i class="fa fa-arrow-left"></i>
			<?php echo $back->text ?>
		</a>
		<button id="<?php echo $submit->name ?>" class="btn btn-orange" title="<?php echo $submit->text ?>" type="submit">
			<i class="fa fa-check"></i>
			<?php echo $submit->text; ?>
		</button>
	</div>
	
</form>

</div>

<div class="enter_card" style="display:none;">
<?php } else { ?>

<div class="enter_card">
<?php } ?>
<form id="stripe" class="validate-creditcard">
<h4 class="heading4"><?php echo $text_credit_card; ?></h4>

	<?php echo $this->getHookVar('payment_table_pre'); ?>

	<div class="form-group form-inline">
		<span class="subtext"><?php echo $entry_billing_address; ?>: <?php echo $payment_address; ?>...</span>
		<div class="col-sm-2 input-group">
			<a href="<?php echo $edit_address; ?>" class="btn btn-default btn-sm">
				<i class="fa fa-edit fa-fw"></i>
				<?php echo $entry_edit; ?>
			</a>
		</div>
	</div>

	<div class="form-group ">
	    <label class="col-sm-4 control-label"><?php echo $entry_cc_owner; ?></label>
	    <div class="col-sm-7 input-group">
	    	<?php echo $cc_owner; ?>
	    </div>
	    <span class="help-block"></span>
	</div>
	<div class="form-group form-inline">
	    <label class="col-sm-4 control-label"><?php echo $entry_cc_number; ?></label>
	    <div class="col-sm-4 input-group">
	    	<?php echo $cc_number; ?>
	    </div>
	    <?php if($save_cc) { ?>
	    <div class="input-group col-sm-2 ml10">
	    	<label>
	    	<a data-toggle="tooltip" data-original-title="<?php echo $entry_cc_save_details; ?>"><?php echo $entry_cc_save; ?></a>
	    	</label>
	    	<?php echo $save_cc; ?>
	    </div>	    
	    <?php } ?>
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
	<div class="form-group form-inline">
	    <label class="col-sm-6 control-label"><?php echo $entry_cc_cvv2; ?> <a onclick="openModalRemote('#ccModal', '<?php echo $cc_cvv2_help_url; ?>')" href="Javascript:void(0);"><?php echo $entry_cc_cvv2_short; ?></a></label>
	    <div class="input-group col-sm-3">
	    	<?php echo $cc_cvv2; ?>
	    </div>
	    <span class="help-block"></span>
	</div>

	<?php echo $this->getHookVar('payment_table_post'); ?>
	
	<div class="form-group action-buttons text-center">
		<a id="<?php echo $back->name ?>" href="<?php echo $back->href; ?>" class="btn btn-default mr10">
			<i class="fa fa-arrow-left"></i>
			<?php echo $back->text ?>
		</a>
		<button id="<?php echo $submit->name ?>" class="btn btn-orange" title="<?php echo $submit->text ?>" type="submit">
			<i class="fa fa-check"></i>
			<?php echo $submit->text; ?>
		</button>
	</div>
	
</form>

</div>

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

$('#enter_card').hover(function() {
	$(this).tooltip('show');
});

//validate submit
$('#stripe').submit(function(event) {
	event.preventDefault();
	var $form = $(this);
	if( !$.aCCValidator.validate($form) ){
		return false;
	} else {
		confirmSubmit($form, '<?php echo $this->html->getURL('extension/default_stripe/send');?>');
	}
});

function confirmSubmit($form, url) {		

	$.ajax({
		type: 'POST',
		url: url,
		data: $form.find(':input'),
		dataType: 'json',		
		beforeSend: function() {
			$('.alert').remove();
			$form.find('.action-buttons').hide(); 
			$form.find('.action-buttons').before('<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin fa-fw"></i> <?php echo $text_wait; ?></div>');
		},
		success: function(data) {
			if (!data) {
				$('.wait').remove();
				$form.find('.action-buttons').show(); 
				$form.before('<div class="alert alert-danger"><i class="fa fa-bug fa-fw"></i> <?php echo $error_unknown; ?></div>');
			} else {					  			
				if (data.error) {
					$('.wait').remove();
					$form.find('.action-buttons').show(); 
					$form.before('<div class="alert alert-warning"><i class="fa fa-exclamation fa-fw"></i> '+data.error+'</div>');
				}	
				if (data.success) {			
					location = data.success;
				}
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			$('.wait').remove();
			$form.find('.action-buttons').show(); 
			$form.before('<div class="alert alert-danger"><i class="fa fa-exclamation fa-fw"></i> '+textStatus+' '+errorThrown+'</div>');
		}				
	});
}
//--></script>