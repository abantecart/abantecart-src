<?php if ($minimum_notmet) { ?>
<div class="alert alert-error alert-danger">
  <strong><?php echo $minimum_notmet; ?></strong>
</div>
<?php } ?>

<div class="checkout_details"><?php echo $text_instructions; ?><br />
  <?php echo $instructions; ?>
  <br />
  <br />
  <?php echo $text_payment; ?>
</div>

<a class="btn btn-default pull-left" href="<?php echo $back; ?>">
     <i class="fa fa-arrow-left"></i>
    <?php echo $button_back; ?>
</a>
<?php if (!$minimum_notmet) { ?>	
<a id="checkout" class="btn ml10 pull-right btn-orange">
    <i class="fa fa-ok fa-white"></i>
    <?php echo $button_confirm; ?>
</a>
<?php } ?>

<script type="text/javascript"><!--
$('#checkout').click(function() {
	$('body').css('cursor','wait');
	$.ajax({ 
		type: 'GET',
		url: '<?php echo $this->html->getURL('extension/default_banktransfer/confirm');?>',
		success: function() {
			goTo('<?php echo $continue; ?>');
		}		
	});
});
//--></script>
