<h4 class="heading4"><?php echo $text_credit_card; ?>:</h4>

<?php echo $form_open; ?>
	<?php echo $this->getHookVar('payment_table_pre'); ?>
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
		<?php echo $cc_type; ?>
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
	<?php if( $accepted_cards['Maestro'] ) { ?>
	<div class="form-group ">
		<label class="col-sm-6 control-label"><?php echo $entry_cc_start_date; ?></label>
		<div class="controls ws_nowrap">
			<?php echo $cc_start_date_month; ?> / <?php echo $cc_start_date_year. '&nbsp;' .$text_start_date; ?>
		</div>
		<span class="help-block"></span>
	</div>
	<?php } ?>
	<div class="form-group ">
		<label class="col-sm-5 control-label"><?php echo $entry_cc_cvv2; ?></label>
		<div class="col-sm-2 input-group">
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
		<button id="<?php echo $submit->name ?>" class="btn btn-orange lock-on-click" title="<?php echo $submit->text ?>" type="submit">
			<i class="fa fa-check"></i>
			<?php echo $submit->text; ?>
		</button>
	</div>
	
</form>

<script type="text/javascript">
jQuery(document).ready(function() {
	var submitSent = false;
	//validate submit
	$('form').submit(function(event) {
		event.preventDefault();
		if(submitSent !== true) {
			submitSent = true;
			if( !$.aCCValidator.validate($(this)) ){
				submitSent = false;
				try { resetLockBtn(); } catch (e){}
				return false;
			} else {
				var $form = $(this);
				confirmSubmit($form);
				return false;
			}
		}
	});
	
	function confirmSubmit($form) {
		$.ajax({
			type: 'POST',
			url: '<?php echo $action; ?>',
			data: $form.serialize(),
			dataType: 'json',
			beforeSend: function() {
				$('.alert').remove();
				$('#paypal .action-buttons').hide(); 
				$('#paypal .action-buttons').before('<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin"></i> <?php echo $text_wait; ?></div>');
			},
			success: function(data) {
				if (!data) {
					$('.wait').remove();
					$('#paypal .action-buttons').show(); 
					$('#paypal').before('<div class="alert alert-danger"><i class="fa fa-bug"></i> <?php echo $error_unknown; ?></div>');
					submitSent = false;
					try { resetLockBtn(); } catch (e){}
				} else {
					if (data.error) {
						$('.wait').remove();
						$('#paypal .action-buttons').show(); 
						$('#paypal').before('<div class="alert alert-warning"><i class="fa fa-exclamation"></i> '+data.error+'</div>');
						submitSent = false;
						$form.find('input[name=csrfinstance]').val(data.csrfinstance);
						$form.find('input[name=csrftoken]').val(data.csrftoken);
						try { resetLockBtn(); } catch (e){}
					}
					if (data.success) {
						location = data.success;
					}
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$('.wait').remove();
				$('#paypal .action-buttons').show(); 
				$('#paypal').before('<div class="alert alert-danger"><i class="fa fa-exclamation"></i> '+textStatus+' '+errorThrown+'</div>');
				submitSent = false;
				$form.find('input[name=csrfinstance]').val(data.csrfinstance);
				$form.find('input[name=csrftoken]').val(data.csrftoken);
				try { resetLockBtn(); } catch (e){}
			}
		});
	}
});
</script>