<?php if ( $block_wrapper ) { ?>
<div id="wide_block">
	  <div class="tl"></div>
	  <div class="tr"></div>
	  <div class="tc"><div class="heading"><?php echo $heading_title; ?></div></div>

        	<div class="cc">
<?php } ?>
<?php echo  $content; ?>
<?php if ( $block_wrapper ) { ?>
    
  </div>
  <div class="bl"></div>
  <div class="br"></div>
  <div class="bc"></div>
</div>
<?php } ?>