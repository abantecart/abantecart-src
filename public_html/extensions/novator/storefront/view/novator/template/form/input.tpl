<?php if (!$no_wrapper){ ?>
<div class="input-group">
    <?php } ?>
    <input type="<?php echo $type ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>"
           placeholder="<?php echo $placeholder ?>" class="form-control w-auto <?php echo $style; ?>"
        <?php echo $attr;
        echo $regexp_pattern ? 'pattern="' . $regexp_pattern . '"' : '';
        echo $error_text ? 'title="' . $error_text . '"' : '';
        if ($list) {
            echo ' list="' . $id . '_list"';
        }
        if ($required) {
            echo 'required';
        } ?>/>
    <?php
    if ($list) { ?>
        <datalist id="<?php echo $id . '_list' ?>">
            <?php foreach ((array)$list as $l) {
                echo '<option value="' . htmlspecialchars($l, ENT_QUOTES, 'UTF-8') . '">';
            } ?>
        </datalist>
    <?php }
    if ($required) { ?>
        <span class="input-group-text text-danger">*</span>
    <?php } ?>
    <?php if (!$no_wrapper){ ?>
</div>
<?php } ?>
