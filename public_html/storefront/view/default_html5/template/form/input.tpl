<?php
	//set size for bootstrap
	$style = str_replace('short','input-mini', $style);
	$style = str_replace('long','input-large', $style);
?>

<input type="<?php echo $type ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?> <?php echo ($style ? 'class="'.$style.'"':''); ?>/>
<?php if ( $required == 'Y' ) : ?>
<span class="add-on required">*</span>
<?php endif; ?>