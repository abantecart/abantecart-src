<div class="form-check-inline d-flex flex-wrap">
<?php
	foreach ( (array)$options as $v => $text ) {
	$radio_id = preg_replace('/[^a-zA-Z0-9\.-_]/', '', $id . $v);
?>
    <div class="flex-nowrap me-3">
		<input id="<?php echo $radio_id ?>"
				type="radio"
				value="<?php echo $v ?>"
				<?php echo $attr; ?>
				name="<?php echo $name ?>"
				<?php echo ($v == $value ? ' checked="checked" ':'') ?>
				<?php echo (in_array($v, (array)$disabled_options) ? ' disabled="disabled" ':'');?>>
        <label class="form-check-label" for="<?php echo $radio_id ?>"><?php echo $text ?></label>
    </div>
<?php } ?>
    <?php
    if ( $required ) { ?>
        <div class="ms-auto text-danger">*</div>
    <?php } ?>
</div>
