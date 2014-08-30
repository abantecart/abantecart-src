<?php
	//set size for bootstrap
	$style = str_replace('short','input-mini', $style);
	$style = str_replace('long','input-large', $style);
	//Possible class values : input-mini, input-small, input-medium, input-large, input-xlarge, input-xxlarge
?>
<select name="<?php echo $name ?>" id="<?php echo $id ?>" <?php echo $attr ?> <?php echo ($style ? 'class="'.$style.'"':''); ?>>
	<?php
	if(!current($value) && $placeholder){ ?>
		<option value=""><?php echo $placeholder; ?></option>
	<?php
	}
	foreach ( $options as $v => $text ) { ?>
	    <option value="<?php echo $v ?>" <?php echo (in_array($v, (array)$value) ? ' selected="selected" ':'') ?>
				title="<?php echo htmlentities($text,ENT_QUOTES,'UTF-8') ?>">
	        <?php echo $text ?>
	    </option>
	<?php } ?>
</select>
<?php if ( $required == 'Y' ) : ?>
<span class="add-on required">*</span>
<?php endif; ?>