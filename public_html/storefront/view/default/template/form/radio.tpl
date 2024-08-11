<?php if(!$no_wrapper){?>
<div class="input-group h-100">
<?php } ?>
<div class="form-check-inline d-flex flex-wrap form-control me-0">
<?php
	foreach ( (array)$options as $v => $text ) {
	$radio_id = preg_replace('/[^a-zA-Z0-9\.-_]/', '', $id . $v);
?>
    <div class="flex-nowrap mx-1">
		<input id="<?php echo $radio_id ?>"
				type="radio"
				value="<?php echo $v ?>"
				<?php echo $attr; ?>
				name="<?php echo $name ?>"
				<?php echo ($v == $value ? ' checked="checked" ':'') ?>
				<?php echo (in_array($v, (array)$disabled_options) ? ' disabled="disabled" ':'');?>>
    <?php if($text){ ?>
        <label class="form-check-label" for="<?php echo $radio_id ?>"><?php echo $text ?></label>
    <?php } ?>
    </div>
<?php } ?>
</div>
<?php
if ( $required ) { ?>
    <span class="input-group-text text-danger">*</span>
<?php } ?>
<?php if(!$no_wrapper){?>
</div>
<?php } ?>