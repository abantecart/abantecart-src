<?php if(!$no_wrapper){?>

    <?php } ?>
    <div class="prod-option">
        <?php
        foreach ( $options as $v => $text ) {
            $check_id = preg_replace('/[^a-zA-Z0-9.-_]/', '', $id . $v); ?>
           
                <div class="form-check form-check-inline">
                    <input id="<?php echo $check_id ?>"
                           class="form-check-input"
                           type="checkbox"
                           value="<?php echo $v ?>"
                           name="<?php echo $name ?>"
                        <?php echo (in_array($v, $value) ? ' checked="checked" ':'') ?>
                        <?php echo $attr; ?>
                        <?php echo (in_array($v, (array)$disabled_options) ? ' disabled="disabled" ':''); ?>>
                    <label class="form-check-label"
                           for="<?php echo $check_id ?>">
                        <?php echo $text ?>
                    </label>
                </div>
           
        <?php } ?>
        <?php if ( $required) { ?>
            <span class="input-group-text border-0 text-danger">*</span>
        <?php } ?>
    </div>
        <?php
        if(!$no_wrapper){?>

<?php } ?>
