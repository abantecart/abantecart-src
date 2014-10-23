<h4 class="heading4"><?php echo $text_credit_card; ?></h4>
<form id="authorizenet" class="creditcard_box form-horizontal">
    <?php echo $this->getHookVar('payment_table_pre'); ?>
		<div class="form-group <?php if ($error_cc_owner) echo 'has-error'; ?>">
		    <label class="col-sm-5  control-label"><?php echo $entry_cc_owner; ?></label>
		    <div class="col-sm-7 input-group">
		        <?php echo $cc_owner; ?>
		    </div>
		    <span class="help-block"><?php echo $error_cc_owner; ?></span>
		</div>
		<div class="form-group <?php if ($error_cc_number) echo 'has-error'; ?>">
		    <label class="col-sm-5  control-label"><?php echo $entry_cc_number; ?></label>
		    <div class="col-sm-7 input-group">
		        <?php echo $cc_number; ?>
		    </div>
	    	<span class="help-block"><?php echo $error_cc_number; ?></span>
		</div>
		<div class="form-group <?php if ($error_cc_expire_date) echo 'has-error'; ?>">
		    <label class="col-sm-5  control-label"><?php echo $entry_cc_expire_date; ?></label>
			<div class="col-sm-7 input-group form-inline">
				<?php echo $cc_expire_date_month; ?>&nbsp;<?php echo $cc_expire_date_year; ?>
			</div>
	    	<span class="help-block"><?php echo $error_cc_expire_date; ?></span>
		</div>
		<div class="form-group <?php if ($error_cc_cvv2) echo 'has-error'; ?>">
		    <label class="col-sm-5  control-label"><?php echo $entry_cc_cvv2; ?></label>
		    <div class="col-sm-7 input-group">
		        <?php echo $cc_cvv2; ?>  <a href="<?php echo $cc_cvv2_help_url; ?>" target="_new"><?php echo $entry_cc_cvv2_short; ?></a>
		    </div>
	    	<span class="help-block"><?php echo $error_cc_cvv2; ?></span>
		</div>

		<?php echo $this->getHookVar('payment_table_post'); ?>

		<div class="form-group action-buttons">
	    	<div class="col-md-12">
	    		<button id="authorizenet_button" class="btn btn-orange pull-right" title="<?php echo $submit->text ?>" type="submit">
	    		    <i class="fa fa-check"></i>
	    		    <?php echo $submit->text; ?>
	    		</button>
				<a href="<?php echo $back->href; ?>" class="btn btn-default mr10" title="<?php echo $back->text ?>">
				    <i class="fa fa-arrow-left"></i>
				    <?php echo $back->text ?>
				</a>
		    </div>
		</div>

</form>

<script type="text/javascript"><!--

$('#authorizenet_button').click ( function() {
			confirmSubmit();
			return false;
		}
);

function confirmSubmit() {
	$.ajax({
		type: 'POST',
		url: 'index.php?rt=extension/default_authorizenet_aim/send',
		data: $('#authorizenet :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#authorizenet_button').parent().hide();
			$('#authorizenet .action-buttons').before('<div id="wait" class="wait alert alert-info"><img src="<?php echo $template_dir; ?>image/loading_1.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		success: function(data) {
			if (data.error.length>0) {
				alert(data.error);
				$('#authorizenet_button').parent().show();
				$('#wait').remove();
			} else if (data.success) {
				goTo(data.success);
			} else{

			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert(textStatus + ' ' + errorThrown);
			$('#wait').remove();
			$('#authorizenet_button').parent().show();
		}				
	});
}
	$(document).ready(function(){
		$('#cc_expire_date_year').width('50');
		$('#cc_expire_date_month').width('85');
	});
//--></script>