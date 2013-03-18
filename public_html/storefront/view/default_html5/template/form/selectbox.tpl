<select name="<?php echo $name ?>" id="<?php echo $id ?>" <?php echo $attr ?> <?php echo ($style ? 'class="'.$style.'"':''); ?>>
	<?php foreach ( $options as $v => $text ) { ?>
	    <option value="<?php echo $v ?>" <?php echo (in_array($v, $value) ? ' selected="selected" ':'') ?> >
	        <?php echo $text ?>
	    </option>
	<?php } ?>
</select>
<?php if ( $required == 'Y' ) : ?>
<span class="add-on required">*</span>
<?php endif; ?>