<?php
	//set size for bootstrap
	$style = str_replace('short','input-mini', $style);
	$style = str_replace('long','input-large', $style);
	//Possible class values : input-mini, input-small, input-medium, input-large, input-xlarge, input-xxlarge
?>
<input type="<?php echo $type ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?> <?php echo ($style ? 'class="'.$style.'"':''); ?> <?php echo $regexp_pattern ? 'pattern="'.$regexp_pattern.'"':'';?> <?php echo $error_text ? 'title="'.$error_text.'"':'';?>/>
<?php if ( $required == 'Y' ) : ?>
<span class="add-on required">*</span>
<?php endif; ?>