<?php
$no_wrapper  = false;
if(!$no_wrapper){?>
    <div class="input-group">
<?php } ?>

    <select name="<?php echo $name ?>" id="<?php echo $id ?>" class="form-control form-select <?php echo $style; ?>"
            data-placeholder="<?php echo $placeholder ?>" <?php echo $attr ?>
            <?php echo $disabled ? ' disabled="disabled" ' : ''; ?>
            <?php if ( $required ) { echo 'required'; }?>>
        <?php
        if(!current((array)$value) && $placeholder){ ?>
            <option disabled="disabled" value="">-- <?php echo $placeholder; ?> -- </option>
        <?php
        }
        foreach ( (array)$options as $v => $text ) { ?>
            <option value="<?php echo $v ?>" <?php
            echo (in_array($v, (array)$value) ? ' selected="selected" ':'');
            echo (in_array($v, (array)$disabled_options) ? ' disabled="disabled" ':'');	?>><?php echo $text ?></option>
        <?php } ?>
    </select>
    <?php
    if ( $required ) { ?>
    <span class="input-group-text text-danger py-auto">*</span>
    <?php } ?>
    <?php if(!$no_wrapper){?>
</div>
<?php } ?>