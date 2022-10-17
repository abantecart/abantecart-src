<?php if(!$no_wrapper){?>
<div class="input-group h-100">
<?php } ?>
    <div class="form-check-inline d-flex flex-wrap form-control me-0">
    <?php
    foreach ( $options as $v => $text ) {
        $check_id = preg_replace('/[^a-zA-Z0-9.-_]/', '', $id . $v); ?>
        <div class="d-flex flex-nowrap me-3 align-items-center">
            <input id="<?php echo $check_id ?>" type="checkbox"
                    class="form-check <?php echo $style; ?>"
                    value="<?php echo $v ?>"
                    name="<?php echo $name ?>" <?php echo (in_array($v, $value) ? ' checked="checked" ':'') ?> <?php echo $attr; ?>
                    <?php echo (in_array($v, (array)$disabled_options) ? ' disabled="disabled" ':''); ?>>
            <label class="ms-1 form-check-label" for="<?php echo $check_id ?>">
                <?php echo $text ?>
            </label>
        </div>
    <?php } ?>
    </div>
    <?php if ( $required) { ?>
            <span class="input-group-text text-danger">*</span>
    <?php } ?>

<?php
if(!$no_wrapper){?>
    </div>
<?php } ?>