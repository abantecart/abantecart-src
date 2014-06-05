<?php 
// find selected item
foreach ( $options as $v => $text ) { 
	if (in_array((string)$v, $value, true)) {
		$ovalue = $v;
		$seleted_text = mb_substr($text,0,60);
	}
} ?>
<?php if( $search_mode ) { ?>
	<?php foreach ( $options as $v => $text ) { ?>
	<?php } ?>
<?php
		$additional = 'class="form-control chosen-select atext '.$style.'" data-placeholder="'.$placeholder.'" style="display: none;">';
	} else if( stristr( $attr, 'multiple' ) !== false ) {
    	$additional = 'class="form-control atext '.$style.'" data-placeholder="'.$placeholder.'">';
	} else {
    	$additional = 'class="form-control atext '.$style.'" data-placeholder="'.$placeholder.'">';
 	} 
?> 
<select name="<?php echo $name ?>" id="<?php echo $id ?>" ovalue="<?php echo $ovalue; ?>" <?php echo $attr ?> <?php echo $additional ?> >
<?php foreach ( $options as $v => $text ) { ?>
		<option value="<?php echo $v ?>"
		<?php echo (in_array((string)$v, (array)$value, true) ? ' selected="selected" ':'') ?>
		<?php echo (in_array((string)$v, (array)$disabled, true) ? ' disabled="disabled" ':'') ?>
		><?php echo $text ?></option>
<?php } ?>
</select>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>
<?php if ( !empty ($help_url) ) : ?>
<span class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></span>
<?php endif; ?>
