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
    <div id="copyright"><?php echo $text_powered_by?>
	    <a href="http://www.abantecart.com" onclick="window.open(this.href);return false;" title="Ideal OpenSource E-commerce Solution">AbanteCart</a><br />
	    <?php echo $text_copy; ?></div>
  </div>
  <!--
AbanteCart is open source software and you are free to remove the Powered By AbanteCart if you want, but its generally accepted practise to make a small donatation.
Please donate via PayPal to donate@abantecart.com
//-->