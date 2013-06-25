<h4 class="heading4"><?php echo $text_credit_card; ?>:</h4>

<div id="paypal" class="form-horizontal">

	<fieldset>
		<div class="control-group ">
			<label class="control-label"><?php echo $entry_cc_owner; ?></label>
			<div class="controls">
				<?php echo $cc_owner; ?>
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="control-group ">
			<label class="control-label"><?php echo $entry_cc_type; ?></label>
			<div class="controls">
				<?php echo $cc_type; ?>
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="control-group ">
			<label class="control-label"><?php echo $entry_cc_number; ?></label>
			<div class="controls">
				<?php echo $cc_number; ?>
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="control-group ">
			<label class="control-label"><?php echo $entry_cc_start_date; ?></label>
			<div class="controls ws_nowrap">
				<?php echo $cc_start_date_month; ?> / <?php echo $cc_start_date_year. '<br/>' .$text_start_date; ?>
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="control-group ">
			<label class="control-label"><?php echo $entry_cc_expire_date; ?></label>
			<div class="controls ws_nowrap">
				<?php echo $cc_expire_date_month; ?> / <?php echo $cc_expire_date_year; ?>
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="control-group ">
			<label class="control-label"><?php echo $entry_cc_cvv2; ?></label>
			<div class="controls">
				<?php echo $cc_cvv2; ?>
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="control-group ">
			<label class="control-label"><?php echo $entry_cc_issue; ?></label>
			<div class="controls">
				<?php echo $cc_issue. '<br/>' .$text_issue; ?>
				<span class="help-inline"></span>
			</div>
		</div>
	</fieldset>
</div>


<div class="control-group">
	<div class="controls">
		<div class="span4 mt20 mb40">
			<div class="pull-left"><?php echo $back; ?></div>
			<div class="pull-right"><?php echo $submit; ?></div>
		</div>
	</div>
</div>


<script type="text/javascript"><!--
function confirmSubmit() {
	$.ajax({
		type: 'POST',
		url: 'index.php?rt=extension/default_pp_pro/send',
		data: $('#paypal :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#paypal_button').attr('disabled', 'disabled');
			
			$('#paypal').before('<div class="wait"><img src="<?php echo $template_dir; ?>image/loading_1.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		success: function(data) {
			if (data.error) {
				alert(data.error);
				
				$('#paypal_button').removeAttr('disabled');
			}
			
			$('.wait').remove();
			
			if (data.success) {
				location = data.success;
			}
		}
	});
}
$('#paypal_button').click ( confirmSubmit );
//--></script>
