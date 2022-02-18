<?php 
	if(!$style) {
		$style = ' btn-default';
	} 
?>
<?php if($href) { ?>
<a id="<?php echo $id; ?>" class="btn <?php echo $href_class . $style; ?>" href="<?php echo $href ?>" title="<?php echo ($title ? $title : $text); ?>" <?php echo $attr; ?>>
<?php if($icon) { ?>
<i class="<?php echo $icon; ?>"></i>
<?php } ?>
 <?php echo $text ?></a>
<?php } else { ?>
<button type="button" id="<?php echo $id; ?>" class="btn <?php echo $href_class . $style; ?>" title="<?php echo ($title ? $title : $text); ?>" <?php echo $attr; ?>>
<?php if($icon) { ?>
<i class="<?php echo $icon; ?>"></i>
<?php } ?>
 <?php echo $text ?></button>
<?php } ?>