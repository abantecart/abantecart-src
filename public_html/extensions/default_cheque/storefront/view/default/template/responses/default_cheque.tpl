<div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;"><?php echo $text_payable; ?><br />
  <?php echo $payable; ?><br />
  <br />
  <?php echo $text_address; ?><br />
  <?php echo $address; ?><br />
  <br />
  <?php echo $text_payment; ?></div>
<div class="buttons">
  <table>
    <tr>
      <td align="left"><?php echo $button_back; ?></td>
      <td align="right"><?php echo $button_confirm; ?></td>
    </tr>
  </table>
</div>
<script type="text/javascript"><!--
$('#back').click(function() {
	location = '<?php echo $back; ?>';
});
$('#checkout').click(function() {
	$('body').css('cursor','wait');
	$.ajax({ 
		type: 'GET',
		url: 'index.php?rt=extension/default_cheque/confirm',
		success: function() {
			location = '<?php echo $continue; ?>';
		}		
	});
});
//--></script>
