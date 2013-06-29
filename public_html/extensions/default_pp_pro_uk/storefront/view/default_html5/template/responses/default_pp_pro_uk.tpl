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
				<?php echo $cc_start_date_month; ?> / <?php echo $cc_start_date_year. ' ' .$text_start_date; ?>
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
				<?php echo $cc_issue. ' ' .$text_issue; ?>
				<span class="help-inline"></span>
			</div>
		</div>
		
		<div class="control-group action-buttons">
	    	<div class="controls">
	    		<button id="<?php echo $submit->name ?>" class="btn btn-orange pull-right" title="<?php echo $submit->text ?>" type="submit">
	    		    <i class="icon-ok icon-white"></i>
	    		    <?php echo $submit->text; ?>
	    		</button>
				<a id="<?php echo $back->name ?>" href="<?php echo $back->href; ?>" class="btn mr10" title="<?php echo $back->text ?>">
				    <i class="icon-arrow-left"></i>
				    <?php echo $back->text ?>
				</a>
		    </div>
		</div>
				
	</fieldset>
</div>

<script type="text/javascript"><!--
function confirmSubmit() {
	$.ajax({
		type: 'POST',
		url: 'index.php?rt=extension/default_pp_pro_uk/send',
		data: $('#paypal :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#paypal_button').attr('disabled', 'disabled');
			
			$('#paypal .action-buttons').before('<div class="wait alert alert-info"><img src="<?php echo $template_dir; ?>image/loading_1.gif" alt="" /> <?php echo $text_wait; ?></div>');
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
