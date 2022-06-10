<?php foreach ($children_blocks as $k => $block) { ?>
<?php echo ${$block}; ?>
<?php if ( $k < sizeof($children_blocks) ) { ?>
<div class="sep"></div>
<?php } ?>  
<?php } ?>