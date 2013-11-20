<span class="select_element">
    <select name="<?php echo $name ?>" id="<?php echo $id ?>" <?php echo $attr ?> <?php echo ($style ? 'class="'.$style.'"':''); ?>>
        <?php
		if(!current($value) && $placeholder){ ?>
			<option value=""><?php echo $placeholder; ?></option>
		<?php }
		foreach ( $options as $v => $text ) { ?>
            <option value="<?php echo $v ?>" <?php echo (in_array($v, (array)$value) ? ' selected="selected" ':'') ?> >
                <?php echo $text ?>
            </option>
        <?php } ?>
    </select>
</span>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>