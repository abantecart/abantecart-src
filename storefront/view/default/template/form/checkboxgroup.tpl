<span class="checkbox_element">
<?php foreach ( $options as $v => $text ) {
    $check_id = preg_replace('/[^a-zA-Z0-9\.-_]/', '', $id . $v);
?>
    <label for="<?php echo $check_id ?>"><input id="<?php echo $check_id ?>" type="checkbox" value="<?php echo $v ?>" name="<?php echo $name ?>" <?php echo (in_array($v, $value) ? ' checked="checked" ':'') ?>><?php echo $text ?></label>
<?php } ?>
</span>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>