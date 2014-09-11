<h4 class="heading4"><?php echo $text_credit_card; ?></h4>
<div id="authorizenet" class="creditcard_box form-horizontal">
	<fieldset>
    <?php echo $this->getHookVar('payment_table_pre'); ?>
		<div class="form-group <?php if ($error_cc_owner) echo 'has-error'; ?>">
		    <label class="control-label"><?php echo $entry_cc_owner; ?></label>
		    <div class="input-group">
		        <?php echo $cc_owner; ?>
		    </div>
		    <span class="help-block"><?php echo $error_cc_owner; ?></span>
		</div>
		<div class="form-group <?php if ($error_cc_number) echo 'has-error'; ?>">
		    <label class="control-label"><?php echo $entry_cc_number; ?></label>
		    <div class="input-group">
		        <?php echo $cc_number; ?>
		    </div>
	    	<span class="help-block"><?php echo $error_cc_number; ?></span>
		</div>
		<div class="form-group <?php if ($error_cc_expire_date) echo 'has-error'; ?>">
		    <label class="control-label"><?php echo $entry_cc_expire_date; ?></label>
		    <div class="input-group">
		        <?php echo $cc_expire_date_month; ?> / <?php echo $cc_expire_date_year; ?>
		    </div>
	    	<span class="help-block"><?php echo $error_cc_expire_date; ?></span>
		</div>
		<div class="form-group <?php if ($error_cc_cvv2) echo 'has-error'; ?>">
		    <label class="control-label"><?php echo $entry_cc_cvv2; ?></label>
		    <div class="input-group">
		        <?php echo $cc_cvv2; ?>  <a class="thickbox" href="<?php echo $cc_cvv2_help_url; ?>" target="_new"><?php echo $entry_cc_cvv2_short; ?></a>
		    </div>
	    	<span class="help-block"><?php echo $error_cc_cvv2; ?></span>
		</div>

		<?php echo $this->getHookVar('payment_table_post'); ?>

		<div class="form-group action-buttons">
	    	<div class="col-md-12">
	    		<button id="authorizenet_button" class="btn btn-orange pull-right" title="<?php echo $submit->text ?>" type="submit">
	    		    <i class="icon-ok icon-white"></i>
	    		    <?php echo $submit->text; ?>
	    		</button>
				<a href="<?php echo $back->href; ?>" class="btn btn-default mr10" title="<?php echo $back->text ?>">
				    <i class="icon-arrow-left"></i>
				    <?php echo $back->text ?>
				</a>
		    </div>
		</div>

	</fieldset>
</div>

<script type="text/javascript"><!--

$('#authorizenet_button').click ( confirmSubmit );

function confirmSubmit() {
	$.ajax({
		type: 'POST',
		url: 'index.php?rt=extension/default_authorizenet_aim/send',
		data: $('#authorizenet :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#authorizenet_button').attr('disabled', 'disabled');
			$('#authorizenet .action-buttons').before('<div class="wait alert alert-info"><img src="<?php echo $template_dir; ?>image/loading_1.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		success: function(data) {
			if (data.error) {
				alert(data.error);
				$('#authorizenet_button').removeAttr('disabled');
			}
			$('.wait').remove();
			if (data.success) {
				location = data.success;
			}
		}
	});
}
//--></script>