<h4 class="heading4"><?php echo $text_credit_card; ?></h4>
<form id="sagepay" class="creditcard_box form-horizontal">
	<div class="form-group ">
		<label class="col-sm-5 control-label"><?php echo $entry_cc_owner; ?></label>
		<div class="col-sm-7 input-group">
			<?php echo $cc_owner; ?>
		</div>
		<span class="help-block"></span>
	</div>
	<div class="form-group ">
		<label class="col-sm-5 control-label"><?php echo $entry_cc_type; ?></label>
		<div class="col-sm-7 input-group">
			<?php echo $cc_type; ?>
		</div>
		<span class="help-block"></span>
	</div>
	<div class="form-group ">
		<label class="col-sm-5 control-label"><?php echo $entry_cc_number; ?></label>

		<div class="col-sm-7 input-group">
			<?php echo $cc_number; ?>
		</div>
		<span class="help-block"></span>
	</div>
	<div class="form-group ">
		<label class="col-sm-5 control-label"><?php echo $entry_cc_start_date; ?>
			<small><?php echo $text_start_date; ?></small>
		</label>

		<div class="col-sm-7 input-group">
			<?php echo $cc_start_date_month; ?> <?php echo $cc_start_date_year; ?>
		</div>
		<span class="help-block"></span>
	</div>
	<div class="form-group ">
		<label class="col-sm-5 control-label"><?php echo $entry_cc_expire_date; ?></label>

		<div class="col-sm-7 input-group">
			<?php echo $cc_expire_date_month; ?><?php echo $cc_expire_date_year; ?>
		</div>
		<span class="help-block"></span>
	</div>
	<div class="form-group ">
		<label class="col-sm-5 control-label"><?php echo $entry_cc_cvv2; ?></label>

		<div class="col-sm-7 input-group">
			<?php echo $cc_cvv2; ?><a href="<?php echo $cc_cvv2_help_url; ?>"
			                          target="_new"><?php echo $entry_cc_cvv2_short; ?></a>
		</div>
		<span class="help-block"></span>
	</div>
	<div class="form-group ">
		<label class="col-sm-5 control-label"><?php echo $entry_cc_issue; ?></label>

		<div class="col-sm-7 input-group">
			<?php echo $cc_issue . ' ' . $text_issue; ?>
		</div>
		<span class="help-block"></span>
	</div>

	<div class="form-group action-buttons">
		<div class="col-md-12">
			<button id="sagepay_button" class="btn btn-orange pull-right" type="submit"
			        onclick="confirmSubmit(); return false;">
				<i class="fa fa-check"></i>
				<?php echo $button_confirm; ?>
			</button>
			<a href="<?php echo str_replace('&', '&amp;', $back); ?>" class="btn btn-default mr10">
				<i class="fa fa-arrow-left"></i>
				<?php echo $button_back; ?>
			</a>
		</div>
	</div>
</form>


<script type="text/javascript"><!--

	$(document).ready(function () {
		$('#cc_start_date_year, #cc_expire_date_year').width('50');
		$('#cc_start_date_month, #cc_expire_date_month').width('85');
	});
	function confirmSubmit() {
		$.ajax({
			type: 'POST',
			url: 'index.php?rt=extension/default_sagepay_direct/send',
			data: $('#sagepay :input'),
			dataType: 'json',
			beforeSend: function () {
				$('#sagepay_button').parent().hide();

				$('#sagepay .action-buttons').before('<div class="wait alert alert-info"><img src="<?php echo $template_dir; ?>image/loading_1.gif" alt="" /> <?php echo $text_wait; ?></div>');
			},
			success: function (data) {
				if (data.ACSURL) {
					$('#3dauth').remove();
					html = '<form action="' + data.ACSURL + '" method="post" id="3dauth">';
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
				$('#sagepay_button').parent().show();
			}
		});
	}
	//--></script>
