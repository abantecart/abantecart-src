<?php
	//set size for bootstrap
	$style = str_replace('short','input-mini', $style);
	$style = str_replace('long','input-large', $style);
	//Possible class values : input-mini, input-small, input-medium, input-large, input-xlarge, input-xxlarge
?>
<span class="textarea_element" >
    <textarea name="<?php echo $name ?>" id="<?php echo $id ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?> <?php echo ($style ? 'class="'.$style.'"':''); ?>><?php echo $value ?></textarea>
</span>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>

