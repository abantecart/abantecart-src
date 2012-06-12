<span class="radio_element">
<?php foreach ( $options as $v => $text ) {
    $radio_id = preg_replace('/[^a-zA-Z0-9\.-_]/', '', $id . $v);
?>
    <label for="<?php echo $radio_id ?>"><input id="<?php echo $radio_id ?>" type="radio" value="<?php echo $v ?>" name="<?php echo $name ?>" <?php echo ($v == $value ? ' checked="checked" ':'') ?>><?php echo $text ?></label>
<?php } ?>
</span>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>