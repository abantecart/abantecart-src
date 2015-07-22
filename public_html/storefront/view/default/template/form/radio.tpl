<?php foreach ( (array)$options as $v => $text ) {
    $radio_id = preg_replace('/[^a-zA-Z0-9\.-_]/', '', $id . $v);
?>
    <label for="<?php echo $radio_id ?>">
    	<input id="<?php echo $radio_id ?>"
			   type="radio"
			   value="<?php echo $v ?>"
				<?php echo $attr; ?>
			   name="<?php echo $name ?>"
				<?php echo ($v == $value ? ' checked="checked" ':'') ?>>
				&nbsp;<?php echo $text ?>&nbsp;&nbsp;
	</label>
<?php } ?>
<?php if ( $required == 'Y' ) { ?>
<span class="required">*</span>
<?php } ?>