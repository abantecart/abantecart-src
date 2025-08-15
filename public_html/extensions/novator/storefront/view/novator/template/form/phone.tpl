<?php if(!$no_wrapper){?>
    <div class="input-group h-100">
<?php }
if($icon){?>
    <div class="input-group-text"><?php echo $icon; ?></div>
<?php }
?>
<input type="tel"
       name="<?php echo $name ?>"
       id="<?php echo $id ?>"
       value="<?php echo $value ?>"
       placeholder="<?php echo $placeholder ?>"
       class="form-control <?php echo $style; ?>"
        <?php echo $attr;
        echo $regexp_pattern ? 'pattern="'.$regexp_pattern.'"':'';
        echo $error_text ? 'title="'.$error_text.'"':'';
        if ( $required ) { echo 'required'; } ?> />
<?php if ( $required) { ?>
    <span class="input-group-text text-danger rounded-end">*</span>
<?php }
if(!$no_wrapper){?>
    </div>
<?php } ?>