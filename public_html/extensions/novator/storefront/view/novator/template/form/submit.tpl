<?php 
	if(!$style) {
		$style = ' btn-primary';
	} 
?>
<button type="submit" class="btn <?php echo $href_class . $style; ?>" title="<?php echo_html2view($name) ?>">
<?php if($icon) { ?>
<i class="<?php echo $icon; ?>"></i>
<?php } ?>
<?php echo $name ?>
</button>