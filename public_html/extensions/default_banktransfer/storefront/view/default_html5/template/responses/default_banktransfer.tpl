<?php if ($minimum_notmet) { ?>
<div class="alert alert-error">
  <strong><?php echo $minimum_notmet; ?></strong>
</div>
<?php } ?>

<div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;"><?php echo $text_instructions; ?><br />
  <?php echo $instructions; ?>
  <br />
  <br />
  <?php echo $text_payment; ?>
</div>

<a class="btn btn-orange pull-left" onclick="location = '<?php echo str_replace('&', '&amp;', $back); ?>'">
     <i class="icon-arrow-left icon-white"></i>
    <?php echo $button_back; ?>
</a>
<?php if (!$minimum_notmet) { ?>	
<a id="checkout" class="btn ml10 pull-right">
    <i class="icon-shopping-cart"></i>
    <?php echo $button_confirm; ?>
</a>
<?php } ?>

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
