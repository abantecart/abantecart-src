<b style="margin-bottom: 3px; display: block;"><?php echo $text_credit_card; ?></b>
<div id="paypal" style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
  <table width="100%">
    <tr>
      <td><?php echo $entry_cc_owner; ?></td>
      <td><?php echo $cc_owner; ?></td>
    </tr>
    <tr>
      <td><?php echo $entry_cc_type; ?></td>
      <td><?php echo $cc_type; ?></td>
    </tr>
    <tr>
      <td><?php echo $entry_cc_number; ?></td>
      <td><?php echo $cc_number; ?></td>
    </tr>
    <tr>
      <td><?php echo $entry_cc_start_date; ?></td>
      <td><?php echo $cc_start_date_month; ?> / <?php echo $cc_start_date_year. '<br/>' .$text_start_date; ?></td>
    </tr>
    <tr>
      <td><?php echo $entry_cc_expire_date; ?></td>
      <td><?php echo $cc_expire_date_month; ?> / <?php echo $cc_expire_date_year; ?></td>
    </tr>
    <tr>
      <td><?php echo $entry_cc_cvv2; ?></td>
      <td><?php echo $cc_cvv2; ?></td>
    </tr>
    <tr>
      <td><?php echo $entry_cc_issue; ?></td>
      <td><?php echo $cc_issue. '<br/>' .$text_issue; ?></td>
    </tr>
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
