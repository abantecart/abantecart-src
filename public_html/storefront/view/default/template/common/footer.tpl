  <div class="f_section1">
	<div class="f_section0_left"><div class="f_section0_right"><div class="f_section0_mid"></div></div></div>
    <div class="f_section1_left"><div class="f_section1_right"><div class="f_section1_mid">
      <div class="b_block flt_left">
        <ul id="bottom_menu">
          <li><a href="<?php echo $home; ?>"><?php echo $text_home; ?></a></li>
          <?php if (!$logged) { ?>
          <li><a href="<?php echo $login; ?>"><?php echo $text_login; ?></a></li>
          <?php } else { ?>
          <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
          <?php } ?>
          <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
          <li><a href="<?php echo $cart; ?>"><?php echo $text_cart; ?></a></li>
          <li><a href="<?php echo $checkout; ?>"><?php echo $text_checkout; ?></a></li>
        </ul>
      </div>
<?php echo ${$credit_cards}.${$donate}; ?>
    </div></div></div>
  </div>
  <!-- footer blocks placeholder -->
    <?php foreach ($children_blocks as $k => $block) {
	            if($block == $donate || $block==$credit_cards) continue; // skip donation block
	  ?>
      <?php if ($k == count($children_blocks)-1 ) { ?>
      <div class="footer_block flt_right"><?php echo ${$block}; ?></div>
      <?php } else { ?>
      <div class="footer_block flt_left"><?php echo ${$block}; ?></div>
      <?php } ?>
    <?php } ?>
    <!-- footer blocks placeholder (EOF) -->
    <div class="clr_both"></div>
  <div class="f_section2">
    <div id="copyright">
    	<?php echo $text_project_label?>
		<br />
	    <?php echo $text_copy; ?>
	 </div>
  </div>
  <!--
AbanteCart is open source software and you are free to remove the Powered By AbanteCart if you want, but its generally accepted practise to make a small donatation.
Please donate via PayPal to donate@abantecart.com
//-->


<?php if ($google_analytics) {
	$ga_data = $this->registry->get('google_analytics_data');
	?>
	<script type="text/javascript">

		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '<?php echo $google_analytics;?>']);
		_gaq.push(['_trackPageview']);

		<?php if($ga_data){?>
		_gaq.push(['_set', 'currencyCode', '<?php echo $ga_data['currency_code'];?>']);
		_gaq.push(['_addTrans',
			'<?php echo $ga_data['transaction_id'];?>',
			'<?php echo $ga_data['store_name'];?>',
			'<?php echo $ga_data['total'];?>',
			'<?php echo $ga_data['tax'];?>',
			'<?php echo $ga_data['shipping'];?>',
			'<?php echo $ga_data['city'];?>',
			'<?php echo $ga_data['state'];?>',
			'<?php echo $ga_data['country'];?>'
		]);
		_gaq.push(['_trackTrans']);
		<?php }?>

		(function () {
			var ga = document.createElement('script');
			ga.type = 'text/javascript';
			ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(ga, s);
		})();

	</script>

<?php } ?>

<!-- Placed at the end of the document so the pages load faster -->
<?php foreach ($scripts_bottom as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>