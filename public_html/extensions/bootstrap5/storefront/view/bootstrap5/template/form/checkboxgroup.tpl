<div class="form-check-inline d-flex flex-wrap ">
<?php
foreach ( $options as $v => $text ) {
    $check_id = preg_replace('/[^a-zA-Z0-9.-_]/', '', $id . $v); ?>
    <div class="flex-nowrap me-3">
        <input id="<?php echo $check_id ?>" type="checkbox"
                class="<?php echo $style; ?>"
                value="<?php echo $v ?>"
                name="<?php echo $name ?>" <?php echo (in_array($v, $value) ? ' checked="checked" ':'') ?> <?php echo $attr; ?>
                <?php echo (in_array($v, (array)$disabled_options) ? ' disabled="disabled" ':''); ?>>
        <label for="<?php echo $check_id ?>">
            <?php echo $text ?>
        </label>
    </div>
<?php } ?>
<?php if ( $required == 'Y' ) { ?>
    <span class="ms-auto text-danger">*</span>
<?php } ?>
</div>