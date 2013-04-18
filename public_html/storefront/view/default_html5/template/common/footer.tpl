<!-- Footer -->
<footer id="footer">
    <!-- footer blocks placeholder -->
    <section class="footersocial">
    	<div class="container">
      		<div class="row">
      		<div class="span3">
    		<?php echo ${$children_blocks[0]}; ?>
    		</div>
    		<div class="span3">
    		<?php echo ${$children_blocks[1]}; ?>
    		</div>
    		<div class="span3">
    		<?php echo ${$children_blocks[2]}; ?>
    		</div>
    		<div class="span3">
    		<?php echo ${$children_blocks[3]}; ?>
    		</div>
      </div>
    </div>
  </section>
  
  <section class="footerlinks">
    <div class="container">
        <div class="pull-left"> 
		<?php echo ${$children_blocks[4]}; ?>	
         </div>
         <div class="pull-right"> 
		<?php echo ${$children_blocks[5]}; ?>
		</div>
    </div>
  </section>
  <section class="copyrightbottom">
    <div class="container">
      <div class="row">
        <div class="span4 pull-left">
        	<?php echo ${$children_blocks[6]}; ?>
         </div>
         <div class="span4 textright"> 
        	<?php echo ${$children_blocks[7]}; ?>
         </div>
        <div class="span4 textright"> <?php echo $text_powered_by?> <a href="http://www.abantecart.com" onclick="window.open(this.href);return false;" title="Ideal OpenSource E-commerce Solution">AbanteCart</a></div>
      </div>
    </div>
  </section>
  <a id="gotop" href="#">Back to top</a>
</footer>

<?php if (1 == 0) { ?>
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
<?php } ?>  


<!--
AbanteCart is open source software and you are free to remove the Powered By AbanteCart if you want, but its generally accepted practise to make a small donatation.
Please donate via PayPal to donate@abantecart.com
//-->

<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo $this->templateResource('/javascript/bootstrap.js'); ?>"></script>
<script src="<?php echo $this->templateResource('/javascript/respond.min.js'); ?>"></script>
<script src="<?php echo $this->templateResource('/javascript/application.js'); ?>"></script>
<script src="<?php echo $this->templateResource('/javascript/bootstrap-tooltip.js'); ?>"></script>
<script src="<?php echo $this->templateResource('/javascript/bootstrap-modal.js'); ?>"></script>
<script defer src="<?php echo $this->templateResource('/javascript/jquery.fancybox.js'); ?>"></script>
<script defer src="<?php echo $this->templateResource('/javascript/jquery.flexslider.js'); ?>"></script>
<script  src="<?php echo $this->templateResource('/javascript/cloud-zoom.1.0.2.js'); ?>"></script>
<script  type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.validate.js'); ?>"></script>
<script type="text/javascript"  src="<?php echo $this->templateResource('/javascript/jquery.carouFredSel-6.1.0-packed.js'); ?>"></script>
<script type="text/javascript"  src="<?php echo $this->templateResource('/javascript/jquery.mousewheel.min.js'); ?>"></script>
<script type="text/javascript"  src="<?php echo $this->templateResource('/javascript/jquery.touchSwipe.min.js'); ?>"></script>
<script type="text/javascript"  src="<?php echo $this->templateResource('/javascript/jquery.ba-throttle-debounce.min.js'); ?>"></script>
<script type="text/javascript"  src="<?php echo $this->templateResource('/javascript/jquery.onebyone.min.js'); ?>"></script>
<script defer src="<?php echo $this->templateResource('/javascript/custom.js'); ?>"></script>