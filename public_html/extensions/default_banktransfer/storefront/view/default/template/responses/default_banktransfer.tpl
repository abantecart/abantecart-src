<?php if ($minimum_notmet) { ?>
<div class="warning alert alert-error"><?php echo $minimum_notmet; ?></div>
<?php } ?>

<div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;"><?php echo $text_instructions; ?><br />
  <?php echo $instructions; ?><br />
  <br />
  <?php echo $text_payment; ?></div>
<div class="buttons">
  <table>
    <tr>
      <td align="left"><a onclick="location = '<?php echo str_replace('&', '&amp;', $back); ?>'" class="button"><span><?php echo $button_back; ?></span></a></td>
<?php if (!$minimum_notmet) { ?>
      <td align="right"><a id="checkout" class="button"><span><?php echo $button_confirm; ?></span></a></td>
<?php } ?>
    </tr>
  </table>
</div>
<script type="text/javascript"><!--
$('#checkout').click(function() {
	$('body').css('cursor','wait');
	$.ajax({ 
		type: 'GET',
		url: 'index.php?rt=extension/default_banktransfer/confirm',
		success: function() {
			location = '<?php echo $continue; ?>';
		}		
	});
});
//--></script>
