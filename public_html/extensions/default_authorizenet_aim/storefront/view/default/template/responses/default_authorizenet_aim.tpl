<b style="margin-bottom: 3px; display: block;"><?php echo $text_credit_card; ?></b>
<div id="authorizenet" style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
  <table width="100%">
    <?php echo $this->getHookVar('payment_table_pre'); ?>
	<tr>
      <td><?php echo $entry_cc_owner; ?></td>
      <td><?php echo $cc_owner; ?></td>
    </tr>
    <tr>
      <td><?php echo $entry_cc_number; ?></td>
      <td><?php echo $cc_number; ?></td>
    </tr>
    <tr>
      <td><?php echo $entry_cc_expire_date; ?></td>
      <td><?php echo $cc_expire_date_month; ?> / <?php echo $cc_expire_date_year; ?></td>
    </tr>
    <tr>
      <td><?php echo $entry_cc_cvv2; ?> <a class="thickbox"  href="<?php echo $cc_cvv2_help_url; ?>"><?php echo $entry_cc_cvv2_short; ?></a>:</td>
      <td><?php echo $cc_cvv2; ?></td>
    </tr>
	<?php echo $this->getHookVar('payment_table_post'); ?>
  </table>
</div>
<div class="buttons">
  <table>
    <tr>
      <td align="left"><?php echo $back; ?></td>
      <td align="right"><?php echo $submit; ?></td>
    </tr>
  </table>
</div>
<script type="text/javascript"><!--
function confirmSubmit() {
	$.ajax({
		type: 'POST',
		url: 'index.php?rt=extension/default_authorizenet_aim/send',
		data: $('#authorizenet :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#authorizenet_button').attr('disabled', 'disabled');
			$('#authorizenet').before('<div class="wait"><img src="<?php echo $template_dir; ?>image/loading_1.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		success: function(data) {
			if (data.error) {
				alert(data.error);
				$('#authorizenet_button').attr('disabled', '');
			}
			$('.wait').remove();
			if (data.success) {
				location = data.success;
			}
		}
	});
}
$('#authorizenet_button').click ( confirmSubmit );
//--></script>