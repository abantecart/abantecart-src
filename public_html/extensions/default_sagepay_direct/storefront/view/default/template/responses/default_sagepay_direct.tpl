<h4 class="heading4"><?php echo $text_credit_card; ?></h4>
<div id="sagepay" class="creditcard_box form-horizontal">
	<fieldset>
    <?php echo $this->getHookVar('payment_table_pre'); ?>
		<div class="form-group <?php if ($error_cc_owner) echo 'has-error'; ?>">
		    <label class="control-label"><?php echo $entry_cc_owner; ?></label>
		    <div class="input-group">
		        <input type="text" name="cc_owner" value="" />
		    </div>
		    <span class="help-block"><?php echo $error_cc_owner; ?></span>
		</div>
		
		<div class="form-group <?php if ($error_cc_type) echo 'has-error'; ?>">
		    <label class="control-label"><?php echo $entry_cc_type; ?></label>
		    <div class="input-group">
				<select name="cc_type">
          		<?php foreach ($cards as $card) { ?>
          		<option value="<?php echo $card['value']; ?>"><?php echo $card['text']; ?></option>
          		<?php } ?>
        		</select>
		    </div>
	    	<span class="help-block"><?php echo $error_cc_type; ?></span>
		</div>
		
		<div class="form-group <?php if ($error_cc_number) echo 'has-error'; ?>">
		    <label class="control-label"><?php echo $entry_cc_number; ?></label>
		    <div class="input-group">
		        <input type="text" name="cc_number" value="" />
		    </div>
	    	<span class="help-block"><?php echo $error_cc_number; ?></span>
		</div>

		<div class="form-group <?php if ($error_cc_start_date) echo 'has-error'; ?>">
		    <label class="control-label"><?php echo $entry_cc_start_date; ?></label>
		    <div class="input-group">
		      	<select name="cc_start_date_month" class="input-small">
		          <?php foreach ($months as $month) { ?>
		          <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
		          <?php } ?>
		        </select>
		        /
		        <select name="cc_start_date_year" class="input-small">
		          <?php foreach ($year_valid as $year) { ?>
		          <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
		          <?php } ?>
		        </select>        
		        <?php echo $text_start_date; ?>
		    </div>
	    	<span class="help-block"><?php echo $error_cc_start_date; ?></span>
		</div>

		<div class="form-group <?php if ($error_cc_expire_date) echo 'has-error'; ?>">
		    <label class="control-label"><?php echo $entry_cc_expire_date; ?></label>
		    <div class="input-group">
		      	<select name="cc_expire_date_month" class="input-small">
		          <?php foreach ($months as $month) { ?>
		          <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
		          <?php } ?>
		        </select>
		        /
		        <select name="cc_expire_date_year" class="input-small">
		          <?php foreach ($year_expire as $year) { ?>
		          <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
		          <?php } ?>
		        </select>
		    </div>
	    	<span class="help-block"><?php echo $error_cc_expire_date; ?></span>
		</div>
		
		<div class="form-group <?php if ($error_cc_cvv2) echo 'has-error'; ?>">
		    <label class="control-label"><?php echo $entry_cc_cvv2; ?></label>
		    <div class="input-group">
		        <input type="text" name="cc_cvv2" value="" size="3" class="input-mini" />  <a class="thickbox" href="<?php echo $cc_cvv2_help_url; ?>" target="_new"><?php echo $entry_cc_cvv2_short; ?></a>
		    </div>
	    	<span class="help-block"><?php echo $error_cc_cvv2; ?></span>
		</div>

		<div class="form-group <?php if ($error_cc_issue) echo 'has-error'; ?>">
		    <label class="control-label"><?php echo $entry_cc_issue; ?></label>
		    <div class="input-group">
		        <input type="text" name="cc_issue" class="input-mini" value="" />  <?php echo $text_issue; ?>
		    </div>
	    	<span class="help-block"><?php echo $error_cc_issue; ?></span>
		</div>

		<?php echo $this->getHookVar('payment_table_post'); ?>

		<div class="form-group action-buttons">
	    	<div class="col-md-12">
	    		<button id="sagepay_button" class="btn btn-orange pull-right" type="submit" onclick="confirmSubmit();">
	    		    <i class="fa fa-check"></i>
	    		    <?php echo $button_confirm; ?>
	    		</button>
				<a href="<?php echo str_replace('&', '&amp;', $back); ?>" class="btn btn-default mr10">
				    <i class="fa fa-arrow-left"></i>
				    <?php echo $button_back; ?>
				</a>
		    </div>
		</div>

	</fieldset>
</div>

<script type="text/javascript"><!--
function confirmSubmit() {
	$.ajax({
		type: 'POST',
		url: 'index.php?rt=extension/default_sagepay_direct/send',
		data: $('#sagepay :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#sagepay_button').parent().hide();
			
			$('#sagepay .action-buttons').before('<div class="wait alert alert-info"><img src="<?php echo $template_dir; ?>image/loading_1.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		success: function(data) {
			if (data.ACSURL) {
				$('#3dauth').remove();
				html  = '<form action="' + data.ACSURL + '" method="post" id="3dauth">';
				html += '<input type="hidden" name="MD" value="' + data.MD + '" />';
				html += '<input type="hidden" name="PaReq" value="' + data.PaReq + '" />';
				html += '<input type="hidden" name="TermUrl" value="' + data.TermUrl + '" />';
				html += '</form>';
				
				$('#sagepay').after(html);
				
				$('#3dauth').submit();
			}
			
			if (data.error) {
				alert(data.error);	
				$('.wait').remove();
				$('#sagepay_button').parent().show();
			}
			if (data.success) {
				location = data.success;
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert(textStatus + ' ' + errorThrown);
			$('.wait').remove();	
			$('#sagepay_button').parent().show();;
		}				
	});
}
//--></script>
