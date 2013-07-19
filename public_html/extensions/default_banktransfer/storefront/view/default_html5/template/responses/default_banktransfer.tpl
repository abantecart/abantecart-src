<?php if ($minimum_notmet) { ?>
<div class="alert alert-error">
  <strong><?php echo $minimum_notmet; ?></strong>
</div>
<?php } ?>

<div class="checkout_details"><?php echo $text_instructions; ?><br />
  <?php echo $instructions; ?>
  <br />
  <br />
  <?php echo $text_payment; ?>
</div>

<a class="btn pull-left" href="<?php echo str_replace('&', '&amp;', $back); ?>">
     <i class="icon-arrow-left"></i>
    <?php echo $button_back; ?>
</a>
<?php if (!$minimum_notmet) { ?>	
<a id="checkout" class="btn ml10 pull-right btn-orange">
    <i class="icon-ok icon-white"></i>
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
