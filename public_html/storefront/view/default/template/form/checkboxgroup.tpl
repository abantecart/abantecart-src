<?php foreach ( $options as $v => $text ) {
    $check_id = preg_replace('/[^a-zA-Z0-9\.-_]/', '', $id . $v);
?>
    <label class="" for="<?php echo $check_id ?>">
    	<input id="<?php echo $check_id ?>" type="checkbox" class="<?php echo $style; ?>" value="<?php echo $v ?>" name="<?php echo $name ?>" <?php echo (in_array($v, $value) ? ' checked="checked" ':'') ?> <?php echo $attr; ?>>
    	<?php echo $text ?>
    </label>
<?php } ?>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>